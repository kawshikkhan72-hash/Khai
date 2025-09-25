<?php
require_once __DIR__ . '/../includes/session.php';
require_login(['customer']);
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/utils.php';

$rid = (int)($_GET['rid'] ?? 0);
if ($rid <= 0) redirect('/food_delivery/customer/restaurants.php');

$rstmt = $conn->prepare('SELECT id, name FROM restaurants WHERE id = ? AND status = "approved"');
$rstmt->bind_param('i', $rid);
$rstmt->execute();
$restaurant = $rstmt->get_result()->fetch_assoc();
if (!$restaurant) redirect('/food_delivery/customer/restaurants.php');

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $food_id = (int)($_POST['food_id'] ?? 0);
    $qty = max(1, (int)($_POST['qty'] ?? 1));
    $fstmt = $conn->prepare('SELECT id, name, price FROM foods WHERE id = ? AND restaurant_id = ?');
    $fstmt->bind_param('ii', $food_id, $rid);
    $fstmt->execute();
    $food = $fstmt->get_result()->fetch_assoc();
    if ($food) {
        $key = (string)$food['id'];
        if (!isset($_SESSION['cart'][$key])) {
            $_SESSION['cart'][$key] = [ 'food_id'=>$food['id'], 'name'=>$food['name'], 'price'=>$food['price'], 'qty'=>0, 'restaurant_id'=>$rid ];
        }
        $_SESSION['cart'][$key]['qty'] += $qty;
    }
}

$stmt = $conn->prepare('SELECT id, name, description, price FROM foods WHERE restaurant_id = ? ORDER BY name');
$stmt->bind_param('i', $rid);
$stmt->execute();
$foods = $stmt->get_result();

require_once __DIR__ . '/../includes/header.php';
?>
<h3 class="mb-3">Menu - <?php echo htmlspecialchars($restaurant['name']); ?></h3>
<div class="row g-3">
<?php while ($f = $foods->fetch_assoc()): ?>
  <div class="col-md-4">
    <div class="card h-100">
      <div class="card-body">
        <h5 class="card-title"><?php echo htmlspecialchars($f['name']); ?></h5>
        <p class="card-text"><?php echo htmlspecialchars($f['description']); ?></p>
        <p class="card-text fw-bold">$<?php echo format_price($f['price']); ?></p>
        <form method="post">
          <input type="hidden" name="food_id" value="<?php echo (int)$f['id']; ?>">
          <div class="input-group">
            <input type="number" class="form-control" name="qty" min="1" value="1">
            <button class="btn btn-primary" type="submit">Add to Cart</button>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php endwhile; ?>
</div>
<div class="mt-4">
  <a class="btn btn-outline-secondary" href="/food_delivery/customer/cart.php">Go to Cart</a>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>


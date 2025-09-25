<?php
require_once __DIR__ . '/../includes/session.php';
require_login(['restaurant']);
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/utils.php';
require_once __DIR__ . '/../includes/header.php';

$user = current_user();
$rid = (int)$user['id'];
$id = (int)($_GET['id'] ?? 0);
$editing = $id > 0;
$name = $desc = '';
$price = 0.0;

if ($editing) {
  $stmt = $conn->prepare('SELECT id, name, description, price FROM foods WHERE id = ? AND restaurant_id = ?');
  $stmt->bind_param('ii', $id, $rid);
  $stmt->execute();
  $row = $stmt->get_result()->fetch_assoc();
  if ($row) { $name = $row['name']; $desc = $row['description']; $price = $row['price']; }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  $desc = trim($_POST['description'] ?? '');
  $price = (float)($_POST['price'] ?? 0);
  if ($editing) {
    $stmt = $conn->prepare('UPDATE foods SET name=?, description=?, price=? WHERE id=? AND restaurant_id=?');
    $stmt->bind_param('ssdii', $name, $desc, $price, $id, $rid);
    $stmt->execute();
  } else {
    $stmt = $conn->prepare('INSERT INTO foods (restaurant_id, name, description, price, image) VALUES (?,?,?,?, NULL)');
    $stmt->bind_param('issd', $rid, $name, $desc, $price);
    $stmt->execute();
  }
  redirect('/food_delivery/restaurant/dashboard.php');
}
?>
<div class="card">
  <div class="card-body">
    <h4 class="card-title mb-3"><?php echo $editing ? 'Edit' : 'Add'; ?> Menu Item</h4>
    <form method="post">
      <div class="mb-3">
        <label class="form-label">Name</label>
        <input class="form-control" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea class="form-control" name="description" rows="3"><?php echo htmlspecialchars($desc); ?></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Price</label>
        <input type="number" step="0.01" class="form-control" name="price" value="<?php echo htmlspecialchars($price); ?>" required>
      </div>
      <button class="btn btn-primary" type="submit"><?php echo $editing ? 'Save Changes' : 'Create'; ?></button>
      <a class="btn btn-outline-secondary" href="/food_delivery/restaurant/dashboard.php">Cancel</a>
    </form>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>


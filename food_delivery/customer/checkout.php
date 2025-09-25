<?php
require_once __DIR__ . '/../includes/session.php';
require_login(['customer']);
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/utils.php';

$user = current_user();
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) === 0) redirect('/food_delivery/customer/cart.php');

// Ensure items are from a single restaurant
$restaurant_id = null;
foreach ($_SESSION['cart'] as $item) {
    if ($restaurant_id === null) $restaurant_id = (int)$item['restaurant_id'];
    if ($restaurant_id !== (int)$item['restaurant_id']) {
        die('Cart contains items from multiple restaurants. Please clear and add from one restaurant.');
    }
}

$total = 0.0;
foreach ($_SESSION['cart'] as $item) {
    $total += ((float)$item['price']) * ((int)$item['qty']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->begin_transaction();
    try {
        $status = 'pending';
        $delivery_id = NULL;
        $stmt = $conn->prepare('INSERT INTO orders (user_id, restaurant_id, delivery_id, total_price, status, order_date) VALUES (?,?,?,?,?, NOW())');
        $stmt->bind_param('iiids', $user['id'], $restaurant_id, $delivery_id, $total, $status);
        $stmt->execute();
        $order_id = $conn->insert_id;

        $oi = $conn->prepare('INSERT INTO order_items (order_id, food_id, quantity, price) VALUES (?,?,?,?)');
        foreach ($_SESSION['cart'] as $it) {
            $fid = (int)$it['food_id'];
            $qty = (int)$it['qty'];
            $price = (float)$it['price'];
            $oi->bind_param('iiid', $order_id, $fid, $qty, $price);
            $oi->execute();
        }
        $conn->commit();
        $_SESSION['cart'] = [];
        redirect('/food_delivery/customer/orders.php?placed=1');
    } catch (Throwable $e) {
        $conn->rollback();
        die('Failed to place order.');
    }
}

require_once __DIR__ . '/../includes/header.php';
?>
<div class="card">
  <div class="card-body">
    <h3 class="card-title mb-3">Checkout</h3>
    <p>Payment Method: <strong>Cash on Delivery</strong></p>
    <p>Total: <strong>$<?php echo format_price($total); ?></strong></p>
    <form method="post">
      <button class="btn btn-success" type="submit">Place Order</button>
      <a class="btn btn-outline-secondary" href="/food_delivery/customer/cart.php">Back to Cart</a>
    </form>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>


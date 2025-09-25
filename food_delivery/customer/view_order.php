<?php
require_once __DIR__ . '/../includes/session.php';
require_login(['customer']);
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

$user = current_user();
$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare('SELECT o.id, r.name AS restaurant, o.total_price, o.status, o.order_date FROM orders o JOIN restaurants r ON r.id = o.restaurant_id WHERE o.user_id = ? AND o.id = ?');
$stmt->bind_param('ii', $user['id'], $id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
if (!$order) { echo '<div class="alert alert-danger">Order not found.</div>'; require_once __DIR__ . '/../includes/footer.php'; exit; }

$it = $conn->prepare('SELECT oi.quantity, oi.price, f.name FROM order_items oi JOIN foods f ON f.id = oi.food_id WHERE oi.order_id = ?');
$it->bind_param('i', $id);
$it->execute();
$items = $it->get_result();
?>
<h3 class="mb-3">Order #<?php echo (int)$order['id']; ?></h3>
<p><strong>Restaurant:</strong> <?php echo htmlspecialchars($order['restaurant']); ?></p>
<p><strong>Status:</strong> <?php echo htmlspecialchars(ucwords(str_replace('_',' ',$order['status']))); ?></p>
<p><strong>Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?></p>
<div class="table-responsive">
  <table class="table">
    <thead><tr><th>Item</th><th>Qty</th><th>Price</th></tr></thead>
    <tbody>
    <?php while ($row = $items->fetch_assoc()): ?>
      <tr>
        <td><?php echo htmlspecialchars($row['name']); ?></td>
        <td><?php echo (int)$row['quantity']; ?></td>
        <td>$<?php echo number_format($row['price'], 2); ?></td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</div>
<p class="fw-bold">Total: $<?php echo number_format($order['total_price'], 2); ?></p>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>


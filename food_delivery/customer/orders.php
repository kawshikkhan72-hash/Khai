<?php
require_once __DIR__ . '/../includes/session.php';
require_login(['customer']);
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

$user = current_user();
$stmt = $conn->prepare('SELECT o.id, r.name AS restaurant, o.total_price, o.status, o.order_date FROM orders o JOIN restaurants r ON r.id = o.restaurant_id WHERE o.user_id = ? ORDER BY o.order_date DESC');
$stmt->bind_param('i', $user['id']);
$stmt->execute();
$orders = $stmt->get_result();
?>
<h3 class="mb-3">My Orders</h3>
<?php if (isset($_GET['placed'])): ?><div class="alert alert-success">Order placed successfully.</div><?php endif; ?>
<div class="table-responsive">
  <table class="table">
    <thead><tr><th>ID</th><th>Restaurant</th><th>Total</th><th>Status</th><th>Date</th><th></th></tr></thead>
    <tbody>
    <?php while ($o = $orders->fetch_assoc()): ?>
      <tr>
        <td>#<?php echo (int)$o['id']; ?></td>
        <td><?php echo htmlspecialchars($o['restaurant']); ?></td>
        <td>$<?php echo number_format($o['total_price'], 2); ?></td>
        <td><?php echo htmlspecialchars(ucwords(str_replace('_',' ',$o['status']))); ?></td>
        <td><?php echo htmlspecialchars($o['order_date']); ?></td>
        <td><a class="btn btn-sm btn-outline-primary" href="/food_delivery/customer/view_order.php?id=<?php echo (int)$o['id']; ?>">View</a></td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
 </div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>


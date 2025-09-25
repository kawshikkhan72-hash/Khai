<?php
require_once __DIR__ . '/../includes/session.php';
require_login(['delivery']);
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

$user = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = (int)($_POST['order_id'] ?? 0);
    $status = $_POST['status'] ?? '';
    $allowed = ['picked_up','on_the_way','delivered'];
    if ($order_id > 0 && in_array($status, $allowed, true)) {
        $stmt = $conn->prepare('UPDATE orders SET status = ? WHERE id = ? AND delivery_id = ?');
        $stmt->bind_param('sii', $status, $order_id, $user['id']);
        $stmt->execute();
    }
}

$stmt = $conn->prepare('SELECT o.id, r.name AS restaurant, o.total_price, o.status, o.order_date FROM orders o JOIN restaurants r ON r.id = o.restaurant_id WHERE o.delivery_id = ? AND o.status IN ("ready_for_delivery","picked_up","on_the_way") ORDER BY o.order_date DESC');
$stmt->bind_param('i', $user['id']);
$stmt->execute();
$assigned = $stmt->get_result();

$hist = $conn->prepare('SELECT o.id, r.name AS restaurant, o.total_price, o.status, o.order_date FROM orders o JOIN restaurants r ON r.id = o.restaurant_id WHERE o.delivery_id = ? AND o.status = "delivered" ORDER BY o.order_date DESC');
$hist->bind_param('i', $user['id']);
$hist->execute();
$history = $hist->get_result();
?>
<h3 class="mb-3">Assigned Orders</h3>
<div class="table-responsive">
  <table class="table">
    <thead><tr><th>ID</th><th>Restaurant</th><th>Total</th><th>Status</th><th>Action</th></tr></thead>
    <tbody>
    <?php while ($o = $assigned->fetch_assoc()): ?>
      <tr>
        <td>#<?php echo (int)$o['id']; ?></td>
        <td><?php echo htmlspecialchars($o['restaurant']); ?></td>
        <td>$<?php echo number_format($o['total_price'], 2); ?></td>
        <td><?php echo htmlspecialchars($o['status']); ?></td>
        <td>
          <form method="post" class="d-flex gap-2">
            <input type="hidden" name="order_id" value="<?php echo (int)$o['id']; ?>">
            <select name="status" class="form-select form-select-sm" style="width:auto;">
              <option value="picked_up">Picked Up</option>
              <option value="on_the_way">On the Way</option>
              <option value="delivered">Delivered</option>
            </select>
            <button class="btn btn-sm btn-primary" type="submit">Update</button>
          </form>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</div>

<h3 class="mt-5 mb-3">Delivery History</h3>
<div class="table-responsive">
  <table class="table">
    <thead><tr><th>ID</th><th>Restaurant</th><th>Total</th><th>Status</th><th>Date</th></tr></thead>
    <tbody>
    <?php while ($o = $history->fetch_assoc()): ?>
      <tr>
        <td>#<?php echo (int)$o['id']; ?></td>
        <td><?php echo htmlspecialchars($o['restaurant']); ?></td>
        <td>$<?php echo number_format($o['total_price'], 2); ?></td>
        <td><?php echo htmlspecialchars($o['status']); ?></td>
        <td><?php echo htmlspecialchars($o['order_date']); ?></td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>


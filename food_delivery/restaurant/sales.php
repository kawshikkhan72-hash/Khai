<?php
require_once __DIR__ . '/../includes/session.php';
require_login(['restaurant']);
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

$user = current_user();
$rid = (int)$user['id'];

$summary = $conn->prepare('SELECT DATE(order_date) as day, COUNT(*) as orders_count, SUM(total_price) as revenue FROM orders WHERE restaurant_id = ? AND status IN ("delivered") GROUP BY DATE(order_date) ORDER BY day DESC LIMIT 30');
$summary->bind_param('i', $rid);
$summary->execute();
$rows = $summary->get_result();

$totals = $conn->prepare('SELECT COUNT(*) as total_orders, SUM(total_price) as total_revenue FROM orders WHERE restaurant_id = ? AND status IN ("delivered")');
$totals->bind_param('i', $rid);
$totals->execute();
$t = $totals->get_result()->fetch_assoc();
?>
<h3 class="mb-3">Sales Report</h3>
<div class="row g-3 mb-3">
  <div class="col-md-4">
    <div class="card"><div class="card-body"><div class="text-muted">Delivered Orders</div><div class="h4"><?php echo (int)($t['total_orders'] ?? 0); ?></div></div></div>
  </div>
  <div class="col-md-4">
    <div class="card"><div class="card-body"><div class="text-muted">Total Revenue</div><div class="h4">$<?php echo number_format((float)($t['total_revenue'] ?? 0),2); ?></div></div></div>
  </div>
</div>
<div class="card">
  <div class="card-body">
    <h5 class="card-title">Last 30 Days</h5>
    <div class="table-responsive">
      <table class="table table-sm">
        <thead><tr><th>Date</th><th>Orders</th><th>Revenue</th></tr></thead>
        <tbody>
          <?php while ($r = $rows->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($r['day']); ?></td>
            <td><?php echo (int)$r['orders_count']; ?></td>
            <td>$<?php echo number_format((float)$r['revenue'],2); ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>


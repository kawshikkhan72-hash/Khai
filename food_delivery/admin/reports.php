<?php
require_once __DIR__ . '/../includes/session.php';
require_login(['admin']);
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

$sales = $conn->query('SELECT r.name as restaurant, COUNT(o.id) as orders_count, SUM(o.total_price) as revenue FROM orders o JOIN restaurants r ON r.id = o.restaurant_id WHERE o.status = "delivered" GROUP BY r.id ORDER BY revenue DESC');
$users = $conn->query('SELECT role, COUNT(*) as cnt FROM users GROUP BY role');
$rest = $conn->query('SELECT status, COUNT(*) as cnt FROM restaurants GROUP BY status');
?>
<h3 class="mb-3">Admin Reports</h3>
<div class="row g-3 mb-3">
  <div class="col-md-6">
    <div class="card"><div class="card-body">
      <h5 class="card-title">Sales by Restaurant</h5>
      <div class="table-responsive">
        <table class="table table-sm"><thead><tr><th>Restaurant</th><th>Orders</th><th>Revenue</th></tr></thead><tbody>
        <?php while ($r = $sales->fetch_assoc()): ?>
          <tr><td><?php echo htmlspecialchars($r['restaurant']); ?></td><td><?php echo (int)$r['orders_count']; ?></td><td>$<?php echo number_format((float)$r['revenue'],2); ?></td></tr>
        <?php endwhile; ?>
        </tbody></table>
      </div>
    </div></div>
  </div>
  <div class="col-md-6">
    <div class="card"><div class="card-body">
      <h5 class="card-title">User Distribution</h5>
      <div class="table-responsive">
        <table class="table table-sm"><thead><tr><th>Role</th><th>Count</th></tr></thead><tbody>
        <?php while ($u = $users->fetch_assoc()): ?>
          <tr><td><?php echo htmlspecialchars($u['role']); ?></td><td><?php echo (int)$u['cnt']; ?></td></tr>
        <?php endwhile; ?>
        </tbody></table>
      </div>
      <h5 class="card-title mt-4">Restaurant Status</h5>
      <div class="table-responsive">
        <table class="table table-sm"><thead><tr><th>Status</th><th>Count</th></tr></thead><tbody>
        <?php while ($s = $rest->fetch_assoc()): ?>
          <tr><td><?php echo htmlspecialchars($s['status']); ?></td><td><?php echo (int)$s['cnt']; ?></td></tr>
        <?php endwhile; ?>
        </tbody></table>
      </div>
    </div></div>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>


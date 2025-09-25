<?php
require_once __DIR__ . '/../includes/session.php';
require_login(['admin']);
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

if (isset($_POST['approve_restaurant'])) {
    $rid = (int)$_POST['restaurant_id'];
    $stmt = $conn->prepare('UPDATE restaurants SET status = "approved" WHERE id = ?');
    $stmt->bind_param('i', $rid);
    $stmt->execute();
}
if (isset($_POST['delete_order'])) {
    $oid = (int)$_POST['order_id'];
    $conn->query('DELETE FROM order_items WHERE order_id = ' . $oid);
    $conn->query('DELETE FROM orders WHERE id = ' . $oid);
}

$pending_restaurants = $conn->query('SELECT id, name, email, location, phone FROM restaurants WHERE status = "pending" ORDER BY name');
$orders = $conn->query('SELECT o.id, u.name as customer, r.name as restaurant, o.total_price, o.status, o.order_date FROM orders o JOIN users u ON u.id = o.user_id JOIN restaurants r ON r.id = o.restaurant_id ORDER BY o.order_date DESC LIMIT 50');
$users = $conn->query('SELECT id, name, email, role FROM users ORDER BY id DESC LIMIT 50');

?>
<div class="row g-4">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Pending Restaurants</h4>
        <div class="table-responsive">
          <table class="table table-sm">
            <thead><tr><th>Name</th><th>Email</th><th>Location</th><th>Phone</th><th></th></tr></thead>
            <tbody>
              <?php while ($r = $pending_restaurants->fetch_assoc()): ?>
                <tr>
                  <td><?php echo htmlspecialchars($r['name']); ?></td>
                  <td><?php echo htmlspecialchars($r['email']); ?></td>
                  <td><?php echo htmlspecialchars($r['location']); ?></td>
                  <td><?php echo htmlspecialchars($r['phone']); ?></td>
                  <td>
                    <form method="post">
                      <input type="hidden" name="restaurant_id" value="<?php echo (int)$r['id']; ?>">
                      <button class="btn btn-sm btn-success" name="approve_restaurant" value="1">Approve</button>
                    </form>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Recent Orders</h4>
        <div class="table-responsive">
          <table class="table table-sm">
            <thead><tr><th>ID</th><th>Customer</th><th>Restaurant</th><th>Total</th><th>Status</th><th>Date</th><th></th></tr></thead>
            <tbody>
              <?php while ($o = $orders->fetch_assoc()): ?>
                <tr>
                  <td>#<?php echo (int)$o['id']; ?></td>
                  <td><?php echo htmlspecialchars($o['customer']); ?></td>
                  <td><?php echo htmlspecialchars($o['restaurant']); ?></td>
                  <td>$<?php echo number_format($o['total_price'], 2); ?></td>
                  <td><?php echo htmlspecialchars($o['status']); ?></td>
                  <td><?php echo htmlspecialchars($o['order_date']); ?></td>
                  <td>
                    <form method="post" onsubmit="return confirm('Delete order?')">
                      <input type="hidden" name="order_id" value="<?php echo (int)$o['id']; ?>">
                      <button class="btn btn-sm btn-outline-danger" name="delete_order" value="1">Delete</button>
                    </form>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Recent Users</h4>
        <div class="table-responsive">
          <table class="table table-sm">
            <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr></thead>
            <tbody>
              <?php while ($u = $users->fetch_assoc()): ?>
                <tr>
                  <td><?php echo (int)$u['id']; ?></td>
                  <td><?php echo htmlspecialchars($u['name']); ?></td>
                  <td><?php echo htmlspecialchars($u['email']); ?></td>
                  <td><?php echo htmlspecialchars($u['role']); ?></td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>


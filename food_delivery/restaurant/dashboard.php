<?php
require_once __DIR__ . '/../includes/session.php';
require_login(['restaurant']);
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/utils.php';
require_once __DIR__ . '/../includes/header.php';

$user = current_user();
$rid = (int)$user['id'];

// Handle order status updates and assign delivery
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['order_status'])) {
        $order_id = (int)$_POST['order_id'];
        $status = $_POST['order_status'];
        $allowed = ['accepted','preparing','ready_for_delivery'];
        if (in_array($status, $allowed, true)) {
            $stmt = $conn->prepare('UPDATE orders SET status = ? WHERE id = ? AND restaurant_id = ?');
            $stmt->bind_param('sii', $status, $order_id, $rid);
            $stmt->execute();
        }
    }
    if (isset($_POST['assign_delivery'])) {
        $order_id = (int)$_POST['order_id'];
        $delivery_id = (int)$_POST['delivery_id'];
        $stmt = $conn->prepare('UPDATE orders SET delivery_id = ?, status = "ready_for_delivery" WHERE id = ? AND restaurant_id = ?');
        $stmt->bind_param('iii', $delivery_id, $order_id, $rid);
        $stmt->execute();
    }
}

// Fetch menu items
$foods = $conn->prepare('SELECT id, name, price FROM foods WHERE restaurant_id = ? ORDER BY name');
$foods->bind_param('i', $rid);
$foods->execute();
$menu = $foods->get_result();

// Fetch incoming orders
$orders = $conn->prepare('SELECT id, user_id, total_price, status, order_date FROM orders WHERE restaurant_id = ? AND status IN ("pending","accepted","preparing","ready_for_delivery") ORDER BY order_date DESC');
$orders->bind_param('i', $rid);
$orders->execute();
$incoming = $orders->get_result();

// Delivery people list
$dlist = $conn->query('SELECT id, name FROM delivery WHERE status = "active" ORDER BY name');
?>
<div class="row g-4">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Menu Items</h4>
        <a class="btn btn-sm btn-primary mb-2" href="/food_delivery/restaurant/menu_form.php">Add Item</a>
        <div class="table-responsive">
          <table class="table table-sm">
            <thead><tr><th>Name</th><th>Price</th><th></th></tr></thead>
            <tbody>
              <?php while ($f = $menu->fetch_assoc()): ?>
                <tr>
                  <td><?php echo htmlspecialchars($f['name']); ?></td>
                  <td>$<?php echo number_format($f['price'],2); ?></td>
                  <td>
                    <a class="btn btn-sm btn-outline-secondary" href="/food_delivery/restaurant/menu_form.php?id=<?php echo (int)$f['id']; ?>">Edit</a>
                    <a class="btn btn-sm btn-outline-danger" href="/food_delivery/restaurant/menu_delete.php?id=<?php echo (int)$f['id']; ?>" onclick="return confirm('Delete item?')">Delete</a>
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
        <h4 class="card-title">Incoming Orders</h4>
        <div class="table-responsive">
          <table class="table table-sm">
            <thead><tr><th>ID</th><th>Total</th><th>Status</th><th>Update</th><th>Assign</th></tr></thead>
            <tbody>
              <?php while ($o = $incoming->fetch_assoc()): ?>
                <tr>
                  <td>#<?php echo (int)$o['id']; ?></td>
                  <td>$<?php echo number_format($o['total_price'],2); ?></td>
                  <td><?php echo htmlspecialchars($o['status']); ?></td>
                  <td>
                    <form method="post" class="d-flex gap-2">
                      <input type="hidden" name="order_id" value="<?php echo (int)$o['id']; ?>">
                      <select class="form-select form-select-sm" name="order_status">
                        <option value="accepted">Accepted</option>
                        <option value="preparing">Preparing</option>
                        <option value="ready_for_delivery">Ready</option>
                      </select>
                      <button class="btn btn-sm btn-primary">Update</button>
                    </form>
                  </td>
                  <td>
                    <form method="post" class="d-flex gap-2">
                      <input type="hidden" name="order_id" value="<?php echo (int)$o['id']; ?>">
                      <select class="form-select form-select-sm" name="delivery_id">
                        <?php while ($d = $dlist->fetch_assoc()): ?>
                          <option value="<?php echo (int)$d['id']; ?>"><?php echo htmlspecialchars($d['name']); ?></option>
                        <?php endwhile; $dlist->data_seek(0); ?>
                      </select>
                      <button class="btn btn-sm btn-success" name="assign_delivery" value="1">Assign</button>
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
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>


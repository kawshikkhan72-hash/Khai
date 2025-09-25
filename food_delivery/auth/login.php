<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/utils.php';
require_once __DIR__ . '/../config/db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'] ?? 'customer';
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($role === 'customer' || $role === 'admin') {
        $stmt = $conn->prepare('SELECT id, name, email, password, phone, role FROM users WHERE email = ? AND role = ? LIMIT 1');
        $stmt->bind_param('ss', $email, $role);
        $stmt->execute();
        $res = $stmt->get_result();
        $user = $res->fetch_assoc();
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = [ 'id'=>(int)$user['id'], 'name'=>$user['name'], 'email'=>$user['email'], 'phone'=>$user['phone'], 'role'=>$user['role'] ];
            redirect('/food_delivery/index.php');
        } else {
            $error = 'Invalid credentials.';
        }
    } elseif ($role === 'delivery') {
        $stmt = $conn->prepare('SELECT id, name, email, password, phone, status FROM delivery WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $res = $stmt->get_result();
        $d = $res->fetch_assoc();
        if ($d && password_verify($password, $d['password'])) {
            $_SESSION['user'] = [ 'id'=>(int)$d['id'], 'name'=>$d['name'], 'email'=>$d['email'], 'phone'=>$d['phone'], 'role'=>'delivery' ];
            redirect('/food_delivery/delivery/dashboard.php');
        } else {
            $error = 'Invalid credentials.';
        }
    } elseif ($role === 'restaurant') {
        $stmt = $conn->prepare('SELECT id, name, email, password, phone, status FROM restaurants WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $res = $stmt->get_result();
        $r = $res->fetch_assoc();
        if ($r && $r['status'] === 'approved' && password_verify($password, $r['password'])) {
            $_SESSION['user'] = [ 'id'=>(int)$r['id'], 'name'=>$r['name'], 'email'=>$r['email'], 'phone'=>$r['phone'], 'role'=>'restaurant' ];
            redirect('/food_delivery/restaurant/dashboard.php');
        } else {
            $error = 'Invalid credentials or restaurant not approved yet.';
        }
    }
}
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<div class="row">
  <div class="col-md-6 mx-auto">
    <div class="card">
      <div class="card-body">
        <h3 class="card-title mb-3">Login</h3>
        <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <form method="post">
          <div class="mb-3">
            <label class="form-label">Role</label>
            <select class="form-select" name="role" required>
              <option value="customer">Customer</option>
              <option value="delivery">Delivery</option>
              <option value="restaurant">Restaurant Owner</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" class="form-control" name="password" required>
          </div>
          <button class="btn btn-primary" type="submit">Login</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>


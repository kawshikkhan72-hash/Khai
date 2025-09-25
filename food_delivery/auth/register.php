<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/utils.php';
require_once __DIR__ . '/../config/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $register_type = $_POST['register_type'] ?? 'customer';
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if ($password !== $confirm) {
        $error = 'Passwords do not match';
    } else {
        if ($register_type === 'customer') {
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $role = 'customer';
            $stmt = $conn->prepare('INSERT INTO users (name, email, password, phone, role) VALUES (?,?,?,?,?)');
            $stmt->bind_param('sssss', $name, $email, $hashed, $phone, $role);
            if ($stmt->execute()) {
                $success = 'Registration successful. You can login now.';
            } else {
                $error = 'Failed to register. Email may be in use.';
            }
        } elseif ($register_type === 'restaurant') {
            $location = trim($_POST['location'] ?? '');
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $status = 'pending';
            $stmt = $conn->prepare('INSERT INTO restaurants (owner_id, name, email, password, location, phone, status) VALUES (NULL,?,?,?,?,?,?)');
            $stmt->bind_param('ssssss', $name, $email, $hashed, $location, $phone, $status);
            if ($stmt->execute()) {
                $success = 'Restaurant registered. Await admin approval.';
            } else {
                $error = 'Failed to register restaurant. Email may be in use.';
            }
        }
    }
}
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<div class="row">
  <div class="col-md-8 mx-auto">
    <div class="card">
      <div class="card-body">
        <h3 class="card-title mb-3">Register</h3>
        <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
        <form method="post">
          <div class="mb-3">
            <label class="form-label">Register as</label>
            <select class="form-select" name="register_type" id="register_type">
              <option value="customer">Customer</option>
              <option value="restaurant">Restaurant Owner</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" class="form-control" name="name" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" class="form-control" name="phone" required>
          </div>
          <div id="restaurant_fields" style="display:none;">
            <div class="mb-3">
              <label class="form-label">Location</label>
              <input type="text" class="form-control" name="location">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" class="form-control" name="password" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" class="form-control" name="confirm" required>
          </div>
          <button class="btn btn-primary" type="submit">Register</button>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
document.getElementById('register_type').addEventListener('change', function() {
  document.getElementById('restaurant_fields').style.display = this.value === 'restaurant' ? 'block' : 'none';
});
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>


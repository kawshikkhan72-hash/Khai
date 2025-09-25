<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

$q = $conn->query("SELECT id, name, location, phone, status FROM restaurants WHERE status='approved' ORDER BY name");
?>
<div class="row g-3">
<?php while ($r = $q->fetch_assoc()): ?>
  <div class="col-md-4">
    <div class="card h-100">
      <div class="card-body">
        <h5 class="card-title"><?php echo htmlspecialchars($r['name']); ?></h5>
        <p class="card-text mb-1">Location: <?php echo htmlspecialchars($r['location']); ?></p>
        <p class="card-text">Phone: <?php echo htmlspecialchars($r['phone']); ?></p>
        <a class="btn btn-primary" href="/food_delivery/customer/menu.php?rid=<?php echo (int)$r['id']; ?>">View Menu</a>
      </div>
    </div>
  </div>
<?php endwhile; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>


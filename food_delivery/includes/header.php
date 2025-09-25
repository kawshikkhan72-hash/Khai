<?php require_once __DIR__ . '/session.php'; $user = current_user(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Delivery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
<nav class="navbar navbar-expand-lg bg-white border-b shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand" href="/food_delivery/index.php">FoodDelivery</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="/food_delivery/customer/restaurants.php">Restaurants</a></li>
        <?php if ($user && $user['role'] === 'customer'): ?>
        <li class="nav-item"><a class="nav-link" href="/food_delivery/customer/cart.php">Cart</a></li>
        <li class="nav-item"><a class="nav-link" href="/food_delivery/customer/orders.php">My Orders</a></li>
        <?php endif; ?>
        <?php if ($user && $user['role'] === 'delivery'): ?>
        <li class="nav-item"><a class="nav-link" href="/food_delivery/delivery/dashboard.php">Delivery</a></li>
        <?php endif; ?>
        <?php if ($user && $user['role'] === 'restaurant'): ?>
        <li class="nav-item"><a class="nav-link" href="/food_delivery/restaurant/dashboard.php">Restaurant</a></li>
        <?php endif; ?>
        <?php if ($user && $user['role'] === 'admin'): ?>
        <li class="nav-item"><a class="nav-link" href="/food_delivery/admin/dashboard.php">Admin</a></li>
        <?php endif; ?>
      </ul>
      <div class="d-flex">
        <?php if ($user): ?>
          <span class="me-3">Hello, <?php echo htmlspecialchars($user['name']); ?></span>
          <a class="btn btn-outline-danger" href="/food_delivery/auth/logout.php">Logout</a>
        <?php else: ?>
          <a class="btn btn-outline-primary me-2" href="/food_delivery/auth/login.php">Login</a>
          <a class="btn btn-primary" href="/food_delivery/auth/register.php">Sign Up</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
  </nav>
  <main class="container py-4">

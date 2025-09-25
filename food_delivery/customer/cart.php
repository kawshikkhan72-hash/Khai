<?php
require_once __DIR__ . '/../includes/session.php';
require_login(['customer']);
require_once __DIR__ . '/../includes/utils.php';
require_once __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        foreach ($_POST['qty'] as $fid => $q) {
            $qty = max(0, (int)$q);
            if ($qty <= 0) {
                unset($_SESSION['cart'][$fid]);
            } else {
                if (isset($_SESSION['cart'][$fid])) {
                    $_SESSION['cart'][$fid]['qty'] = $qty;
                }
            }
        }
    } elseif (isset($_POST['clear'])) {
        $_SESSION['cart'] = [];
    }
}

$total = 0.0;
foreach ($_SESSION['cart'] as $item) {
    $total += ((float)$item['price']) * ((int)$item['qty']);
}
?>
<h3 class="mb-3">Your Cart</h3>
<form method="post">
<div class="table-responsive">
  <table class="table">
    <thead><tr><th>Item</th><th>Price</th><th>Qty</th><th>Subtotal</th></tr></thead>
    <tbody>
    <?php foreach ($_SESSION['cart'] as $fid => $item): ?>
      <tr>
        <td><?php echo htmlspecialchars($item['name']); ?></td>
        <td>$<?php echo format_price($item['price']); ?></td>
        <td style="max-width:120px"><input class="form-control" type="number" min="0" name="qty[<?php echo htmlspecialchars($fid); ?>]" value="<?php echo (int)$item['qty']; ?>"></td>
        <td>$<?php echo format_price($item['price'] * $item['qty']); ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
  <div class="d-flex justify-content-between align-items-center">
    <div class="fw-bold">Total: $<?php echo format_price($total); ?></div>
    <div>
      <button class="btn btn-outline-secondary" name="update" value="1">Update Cart</button>
      <button class="btn btn-outline-danger" name="clear" value="1">Clear</button>
      <a class="btn btn-primary<?php echo $total<=0?' disabled':''; ?>" href="/food_delivery/customer/checkout.php">Checkout</a>
    </div>
  </div>
</form>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>


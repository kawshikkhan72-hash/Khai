<?php
require_once __DIR__ . '/../includes/session.php';
require_login(['restaurant']);
require_once __DIR__ . '/../config/db.php';

$user = current_user();
$rid = (int)$user['id'];
$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
  $stmt = $conn->prepare('DELETE FROM foods WHERE id = ? AND restaurant_id = ?');
  $stmt->bind_param('ii', $id, $rid);
  $stmt->execute();
}
header('Location: /food_delivery/restaurant/dashboard.php');
exit;
?>


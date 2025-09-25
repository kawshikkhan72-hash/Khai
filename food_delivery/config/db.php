<?php
// Database connection using mysqli
// Update credentials if your XAMPP MySQL config differs
$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'food_delivery';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Ensure UTF-8 charset
$conn->set_charset('utf8mb4');
?>


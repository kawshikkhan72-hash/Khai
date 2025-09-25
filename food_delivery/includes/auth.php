<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/session.php';

function find_user_by_email($email) {
    global $conn;
    $stmt = $conn->prepare('SELECT id, name, email, password, phone, role FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function login($email, $password) {
    $user = find_user_by_email($email);
    if (!$user) return false;
    if (!password_verify($password, $user['password'])) return false;
    $_SESSION['user'] = [
        'id' => (int)$user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'phone' => $user['phone'],
        'role' => $user['role'],
    ];
    return true;
}

function register_customer($name, $email, $password, $phone) {
    global $conn;
    $hashed = password_hash($password, PASSWORD_BCRYPT);
    $role = 'customer';
    $stmt = $conn->prepare('INSERT INTO users (name, email, password, phone, role) VALUES (?,?,?,?,?)');
    $stmt->bind_param('sssss', $name, $email, $hashed, $phone, $role);
    return $stmt->execute();
}

?>


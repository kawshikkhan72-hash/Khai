<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_login(array $roles = []) {
    if (!isset($_SESSION['user'])) {
        header('Location: /food_delivery/auth/login.php');
        exit;
    }
    if (!empty($roles)) {
        $userRole = $_SESSION['user']['role'] ?? '';
        if (!in_array($userRole, $roles, true)) {
            header('Location: /food_delivery/index.php');
            exit;
        }
    }
}

function current_user() {
    return $_SESSION['user'] ?? null;
}

function logout_user() {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}
?>


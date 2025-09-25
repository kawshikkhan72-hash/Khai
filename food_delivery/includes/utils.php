<?php
function sanitize($value) {
    return htmlspecialchars(trim((string)$value), ENT_QUOTES, 'UTF-8');
}

function format_price($value) {
    return number_format((float)$value, 2);
}

function redirect($path) {
    header('Location: ' . $path);
    exit;
}
?>


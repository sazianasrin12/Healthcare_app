<?php
require_once __DIR__ . '/../config/functions.php';
if (session_status() === PHP_SESSION_NONE) session_start();

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        redirect(APP_BASE . '/modules/public/login.php', 'Please login first', 'error');
    }
}

function requireRole($role) {
    requireLogin();
    if (strtolower($_SESSION['role'] ?? '') !== strtolower($role)) {
        redirect(APP_BASE . '/modules/public/login.php', 'Access denied', 'error');
    }
}
?>

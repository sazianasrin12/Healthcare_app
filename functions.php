<?php
define('APP_BASE', '/healthcare_app');

function getBaseUrl() {
    return APP_BASE;
}

function redirect($url, $msg = '', $type = '') {
    // If URL starts with / but doesn't start with /healthcare_app, prepend base
    if (strpos($url, '/') === 0 && strpos($url, APP_BASE) !== 0) {
        $url = APP_BASE . $url;
    }
    if ($msg) {
        $url .= (strpos($url, '?') === false ? '?' : '&') . "msg=" . urlencode($msg) . "&type=$type";
    }
    header("Location: $url");
    exit();
}

function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function isDoctor() {
    return isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'doctor';
}

function isPatient() {
    return isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'patient';
}

function getRoleBasedHome() {
    return isDoctor()
        ? APP_BASE . '/modules/doctor/dashboard.php'
        : APP_BASE . '/modules/patient/dashboard.php';
}
?>

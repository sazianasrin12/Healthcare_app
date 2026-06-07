<?php $base = function_exists('getBaseUrl') ? getBaseUrl() : ''; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Digital Healthcare' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="<?= $base ?>/" class="logo">🏥 Digital Healthcare</a>
            <div class="nav-links">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span class="nav-welcome">👋 <?= htmlspecialchars($_SESSION['name'] ?? 'User') ?></span>
                    <a href="<?= $base ?>/modules/<?= ($_SESSION['role'] ?? '') === 'doctor' ? 'doctor' : 'patient' ?>/dashboard.php" class="nav-btn">Dashboard</a>
                    <a href="<?= $base ?>/modules/auth/logout.php" class="nav-btn nav-btn-outline">Logout</a>
                <?php else: ?>
                    <a href="<?= $base ?>/modules/public/login.php" class="nav-btn">Login</a>
                    <a href="<?= $base ?>/modules/public/register.php" class="nav-btn nav-btn-outline">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <main class="container">

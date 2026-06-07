<?php
require_once '../../config/functions.php';
$pageTitle = "Home - Digital Healthcare";
include '../../includes/header.php';
?>
<section class="hero">
    <h1>Welcome to Digital Healthcare & Medicine Reminder</h1>
    <p>Manage your health, consult doctors online, never miss a medicine.</p>
    <div class="hero-buttons">
        <a href="login.php" class="btn btn-primary">Login</a>
        <a href="register.php" class="btn btn-secondary">Register</a>
    </div>
</section>
<section class="features">
    <div class="feature"><h3>💊 Medicine Reminders</h3><p>Smart alerts for your medications.</p></div>
    <div class="feature"><h3>🩺 Online Consultation</h3><p>Video calls with doctors via Zoom.</p></div>
    <div class="feature"><h3>📋 Digital Prescriptions</h3><p>Access your prescriptions anytime.</p></div>
</section>
<?php include '../../includes/footer.php'; ?>

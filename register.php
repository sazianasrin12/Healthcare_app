<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = "Invalid request";
    } else {
        $name = trim($_POST['fullname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'patient';
        $phone = trim($_POST['phone'] ?? '');

        if (strlen($password) < 6) {
            $error = "Password must be at least 6 characters";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, phone) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $email, $hashed, $role, $phone);
            if ($stmt->execute()) {
                $success = "Registration successful! <a href='login.php'>Login here</a>";
            } else {
                $error = $conn->error;
            }
        }
    }
}
$csrf_token = generateCSRFToken();
include '../../includes/header.php';
?>
<div class="auth-card">
    <h2>Register</h2>
    <?php if (isset($error)): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if (isset($success)): ?><div class="alert success"><?= $success ?></div><?php endif; ?>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
        <div class="form-group"><label>Full Name</label><input type="text" name="fullname" required></div>
        <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
        <div class="form-group"><label>Password (min 6)</label><input type="password" name="password" minlength="6" required></div>
        <div class="form-group"><label>Phone (optional)</label><input type="text" name="phone"></div>
        <div class="form-group"><label>I am a</label>
            <select name="role">
                <option value="patient">Patient</option>
                <option value="doctor">Doctor</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login</a></p>
</div>
<?php include '../../includes/footer.php'; ?>

<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = "Invalid request. Please try again.";
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = trim($_POST['role'] ?? 'patient');

        $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            if (strtolower($user['role']) !== strtolower($role)) {
                $error = "Role mismatch. You are registered as " . $user['role'];
            } else {
                 $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = strtolower($user['role']);
                session_write_close();
                redirect(getRoleBasedHome());
            }
        } else {
            $error = "Invalid email or password";
        }
    }
}
$csrf_token = generateCSRFToken();
include '../../includes/header.php';
?>
<div class="auth-card">
    <h2>Login</h2>
    <?php if (isset($error)): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
        <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
        <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
        <div class="form-group"><label>Login as</label>
            <select name="role">
                <option value="patient">Patient</option>
                <option value="doctor">Doctor</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register</a></p>
</div>
<?php include '../../includes/footer.php'; ?>

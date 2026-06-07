<?php
require_once '../../includes/auth.php';
requireRole('doctor');
require_once '../../config/database.php';
require_once '../../config/csrf.php';

$doctor_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) redirect('/modules/doctor/zoom_settings.php', 'Invalid token', 'error');
    
    $zoom_id = trim($_POST['zoom_meeting_id']);
    $zoom_pwd = trim($_POST['zoom_meeting_password']);
    
    $stmt = $conn->prepare("UPDATE users SET zoom_meeting_id = ?, zoom_meeting_password = ? WHERE id = ?");
    $stmt->bind_param("ssi", $zoom_id, $zoom_pwd, $doctor_id);
    $stmt->execute();
    
    redirect('/modules/doctor/dashboard.php', 'Zoom settings saved', 'success');
}

$stmt = $conn->prepare("SELECT zoom_meeting_id, zoom_meeting_password FROM users WHERE id = ?");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$doc = $stmt->get_result()->fetch_assoc();

$csrf = generateCSRFToken();
$pageTitle = "Zoom Settings";
include '../../includes/header.php';
?>
<div class="card">
    <h2>Configure Your Zoom Meeting</h2>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
        <div class="form-group"><label>Zoom Meeting ID *</label><input type="text" name="zoom_meeting_id" value="<?= htmlspecialchars($doc['zoom_meeting_id'] ?? '') ?>" required></div>
        <div class="form-group"><label>Meeting Password (optional)</label><input type="text" name="zoom_meeting_password" value="<?= htmlspecialchars($doc['zoom_meeting_password'] ?? '') ?>"></div>
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>
<?php include '../../includes/footer.php'; ?>

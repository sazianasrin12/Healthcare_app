<?php
require_once '../../includes/auth.php';
requireLogin();
require_once '../../config/database.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT zoom_meeting_id, zoom_meeting_pwd, doctor_id, patient_id FROM consultations WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$call = $stmt->get_result()->fetch_assoc();

if (!$call || empty($call['zoom_meeting_id'])) die("Meeting not found.");
if ($_SESSION['user_id'] != $call['doctor_id'] && $_SESSION['user_id'] != $call['patient_id']) die("Access denied");

$zoom_url = "https://zoom.us/j/{$call['zoom_meeting_id']}" . ($call['zoom_meeting_pwd'] ? "?pwd={$call['zoom_meeting_pwd']}" : "");

include '../../includes/header.php';
?>
<div class="card" style="text-align:center; padding:40px;">
    <h2>Video Consultation</h2>
    <p style="margin-bottom:20px; color:var(--text-muted);">Click the button below to join the secure Zoom meeting.</p>
    <a href="<?= $zoom_url ?>" target="_blank" class="btn btn-primary" style="font-size:1.1rem; padding:12px 24px;">🎥 Join Zoom Meeting</a>
    <?php if (isDoctor()): ?>
        <br><br>
        <a href="../doctor/end_consultation.php?id=<?= $id ?>" class="btn btn-danger">End Consultation</a>
    <?php endif; ?>
</div>
<?php include '../../includes/footer.php'; ?>

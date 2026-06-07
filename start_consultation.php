<?php
require_once '../../includes/auth.php';
requireRole('doctor');
require_once '../../config/database.php';

$consultation_id = (int)($_GET['id'] ?? 0);
$doctor_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT zoom_meeting_id, zoom_meeting_password FROM users WHERE id = ?");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$doc = $stmt->get_result()->fetch_assoc();

if (empty($doc['zoom_meeting_id'])) {
    redirect('/modules/doctor/dashboard.php', 'Please set your Zoom credentials first', 'error');
}

$stmt = $conn->prepare("UPDATE consultations SET status='ongoing', start_time=NOW(), zoom_meeting_id=?, zoom_meeting_pwd=? WHERE id=? AND doctor_id=?");
$stmt->bind_param("ssii", $doc['zoom_meeting_id'], $doc['zoom_meeting_password'], $consultation_id, $doctor_id);
$stmt->execute();

header("Location: ../consultation/video_call.php?id=$consultation_id");
exit;

<?php
session_start();
require_once '../../includes/auth.php';
requireRole('patient');
require_once '../../config/database.php';
require_once '../../config/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    redirect('/modules/patient/dashboard.php', 'Invalid request', 'error');
}

$patient_id = $_SESSION['user_id'];
$doctor_id = (int)($_POST['doctor_id'] ?? 0);
$date = $_POST['date'] ?? '';
$time = $_POST['time'] ?? '';
$message = trim($_POST['message'] ?? '');

if (!$doctor_id || !$date || !$time || !$message) {
    redirect('/modules/patient/dashboard.php', 'All fields required', 'error');
}

$stmt = $conn->prepare("INSERT INTO consultations (patient_id, doctor_id, date, time, message, status) VALUES (?, ?, ?, ?, ?, 'pending')");
$stmt->bind_param("iisss", $patient_id, $doctor_id, $date, $time, $message);
$stmt->execute();

redirect('/modules/patient/dashboard.php', 'Request sent successfully!', 'success');

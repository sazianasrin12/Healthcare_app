<?php
require_once '../../includes/auth.php';
requireRole('doctor');
require_once '../../config/database.php';
$id = (int)($_GET['id'] ?? 0);
$doctor_id = $_SESSION['user_id'];
$stmt = $conn->prepare("UPDATE consultations SET status='rejected' WHERE id=? AND doctor_id=?");
$stmt->bind_param("ii", $id, $doctor_id);
$stmt->execute();
redirect('/modules/doctor/dashboard.php', 'Request rejected', 'warning');

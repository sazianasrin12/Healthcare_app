<?php
session_start();
require_once '../../config/database.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') exit;

$rem_id = (int)$_POST['rem_id'];
$action = $_POST['action'];
$status = $action === 'taken' ? 'taken' : 'missed';

$stmt = $conn->prepare("UPDATE reminders SET status = ? WHERE id = ? AND patient_id = ?");
$stmt->bind_param("sii", $status, $rem_id, $_SESSION['user_id']);
$stmt->execute();

header("Location: reminders.php");
exit;

<?php
require_once '../../includes/auth.php';
requireLogin();
require_once '../../config/database.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT c.*, d.name as doctor_name, p.name as patient_name FROM consultations c JOIN users d ON c.doctor_id=d.id JOIN users p ON c.patient_id=p.id WHERE c.id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row) die("Not found");

include '../../includes/header.php';
?>
<div class="card">
    <h2>Consultation Details</h2>
    <p><strong>Doctor:</strong> <?= htmlspecialchars($row['doctor_name']) ?></p>
    <p><strong>Patient:</strong> <?= htmlspecialchars($row['patient_name']) ?></p>
    <p><strong>Date:</strong> <?= $row['date'] ?> <?= $row['time'] ?></p>
    <p><strong>Message:</strong> <?= nl2br(htmlspecialchars($row['message'])) ?></p>
    <p><strong>Status:</strong> <span class="badge <?= $row['status'] ?>"><?= ucfirst($row['status']) ?></span></p>
</div>
<?php include '../../includes/footer.php'; ?>

<?php
require_once '../../includes/auth.php';
requireLogin();
require_once '../../config/database.php';

$role = strtolower($_SESSION['role']);
if ($role === 'patient') {
    $stmt = $conn->prepare("SELECT c.id, u1.name as doctor_name, c.date, c.time FROM consultations c JOIN users u1 ON c.doctor_id=u1.id WHERE c.patient_id=? AND c.status='completed' ORDER BY c.id DESC");
    $stmt->bind_param("i", $_SESSION['user_id']);
} else {
    $stmt = $conn->prepare("SELECT c.id, u2.name as patient_name, c.date, c.time FROM consultations c JOIN users u2 ON c.patient_id=u2.id WHERE c.doctor_id=? AND c.status='completed' ORDER BY c.id DESC");
    $stmt->bind_param("i", $_SESSION['user_id']);
}
$stmt->execute();
$list = $stmt->get_result();

include '../../includes/header.php';
?>
<h1>Consultation History</h1>
<table class="table">
    <tr><th>ID</th><th><?= $role==='patient'?'Doctor':'Patient' ?></th><th>Date</th><th>Time</th><th>Action</th></tr>
    <?php while($r = $list->fetch_assoc()): ?>
        <tr><td>#<?= $r['id'] ?></td><td><?= htmlspecialchars($role==='patient'?$r['doctor_name']:$r['patient_name']) ?></td><td><?= $r['date'] ?></td><td><?= $r['time'] ?></td><td><a href="view.php?id=<?= $r['id'] ?>">View</a></td></tr>
    <?php endwhile; ?>
</table>
<?php include '../../includes/footer.php'; ?>

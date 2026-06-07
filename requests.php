<?php
require_once '../../includes/auth.php';
requireRole('patient');
require_once '../../config/database.php';

$patient_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT c.*, u.name as doctor_name FROM consultations c JOIN users u ON c.doctor_id = u.id WHERE c.patient_id = ? ORDER BY c.id DESC");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$requests = $stmt->get_result();

$pageTitle = "My Requests";
include '../../includes/header.php';
?>
<h1>My Consultation Requests</h1>
<?php if ($requests->num_rows === 0): ?>
    <p>No requests found.</p>
<?php else: ?>
    <?php while($row = $requests->fetch_assoc()): ?>
        <?php
        $zoom_url = '';
        if (!empty($row['zoom_meeting_id'])) {
            $zoom_url = "https://zoom.us/j/{$row['zoom_meeting_id']}" . (!empty($row['zoom_meeting_pwd']) ? "?pwd={$row['zoom_meeting_pwd']}" : "");
        }
        ?>
        <div class="request-card">
            <div>
                <h3>Dr. <?= htmlspecialchars($row['doctor_name']) ?></h3>
                <p><?= $row['date'] ?> at <?= $row['time'] ?></p>
                <p><?= htmlspecialchars($row['message']) ?></p>
                <span class="badge <?= $row['status'] ?>"><?= ucfirst($row['status']) ?></span>
            </div>
            <div class="actions">
                <?php if ($row['status'] == 'ongoing' && $zoom_url): ?>
                    <a href="<?= $zoom_url ?>" target="_blank" class="btn btn-primary">Join Zoom</a>
                <?php elseif ($row['status'] == 'completed'): ?>
                    <a href="../consultation/view.php?id=<?= $row['id'] ?>" class="btn btn-secondary">View</a>
                    <a href="prescriptions.php" class="btn btn-secondary">Prescriptions</a>
                <?php elseif ($row['status'] == 'accepted'): ?>
                    <span>Waiting for doctor to start...</span>
                <?php elseif ($row['status'] == 'pending'): ?>
                    <span>Pending approval</span>
                <?php else: ?>
                    <span>Rejected</span>
                <?php endif; ?>
            </div>
        </div>
    <?php endwhile; ?>
<?php endif; ?>

<script>
    const hasPending = document.querySelectorAll('.badge.pending, .badge.accepted').length > 0;
    if (hasPending) setTimeout(() => location.reload(), 10000);
</script>
<?php include '../../includes/footer.php'; ?>

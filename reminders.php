<?php
require_once '../../includes/auth.php';
requireRole('patient');
require_once '../../config/database.php';

$patient_id = $_SESSION['user_id'];
$today = date('Y-m-d');

$stmt = $conn->prepare("SELECT * FROM reminders WHERE patient_id = ? AND start_date <= ? AND (end_date IS NULL OR end_date >= ?) ORDER BY reminder_time");
$stmt->bind_param("iss", $patient_id, $today, $today);
$stmt->execute();
$reminders = $stmt->get_result();

$pageTitle = "Medicine Reminders";
include '../../includes/header.php';
?>
<h1>Today's Medicine Reminders</h1>
<a href="add_reminder.php" class="btn btn-success" style="margin-bottom:16px;">+ New Reminder</a>
<?php if ($reminders->num_rows === 0): ?>
    <p>No reminders for today.</p>
<?php else: ?>
    <?php while($r = $reminders->fetch_assoc()): ?>
        <div class="reminder-card" data-time="<?= $r['reminder_time'] ?>">
            <div>
                <strong><?= htmlspecialchars($r['medicine_name']) ?></strong> - <?= $r['dosage'] ?>
                <br><small><?= date('h:i A', strtotime($r['reminder_time'])) ?> | <?= $r['frequency'] ?></small>
            </div>
            <div class="actions">
                <span class="badge <?= $r['status'] ?>"><?= $r['status'] ?></span>
                <?php if ($r['status'] == 'pending'): ?>
                    <form method="POST" action="update_reminder_status.php" style="display:inline">
                        <input type="hidden" name="rem_id" value="<?= $r['id'] ?>">
                        <input type="hidden" name="action" value="taken">
                        <button class="btn btn-sm btn-success">Taken</button>
                    </form>
                    <form method="POST" action="update_reminder_status.php" style="display:inline">
                        <input type="hidden" name="rem_id" value="<?= $r['id'] ?>">
                        <input type="hidden" name="action" value="missed">
                        <button class="btn btn-sm btn-danger">Missed</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    <?php endwhile; ?>
<?php endif; ?>
<?php include '../../includes/footer.php'; ?>

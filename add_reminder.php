<?php
require_once '../../includes/auth.php';
requireRole('patient');
require_once '../../config/database.php';
require_once '../../config/csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) redirect('/modules/patient/add_reminder.php', 'Invalid token', 'error');
    
    $stmt = $conn->prepare("INSERT INTO reminders (patient_id, medicine_name, dosage, reminder_time, frequency, start_date, end_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("issssss", $_SESSION['user_id'], $_POST['medicine_name'], $_POST['dosage'], $_POST['reminder_time'], $_POST['frequency'], $_POST['start_date'], $_POST['end_date']);
    $stmt->execute();
    
    redirect('/modules/patient/reminders.php', 'Reminder added', 'success');
}

$csrf = generateCSRFToken();
include '../../includes/header.php';
?>
<div class="card">
    <h2>Set Medicine Reminder</h2>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
        <div class="form-group"><label>Medicine Name</label><input type="text" name="medicine_name" required></div>
        <div class="form-group"><label>Dosage</label><input type="text" name="dosage" placeholder="e.g., 500mg"></div>
        <div class="form-group"><label>Reminder Time</label><input type="time" name="reminder_time" required></div>
        <div class="form-group"><label>Frequency</label><select name="frequency"><option>Once Daily</option><option>Twice Daily</option><option>Thrice Daily</option></select></div>
        <div class="form-group"><label>Start Date</label><input type="date" name="start_date" value="<?= date('Y-m-d') ?>" required></div>
        <div class="form-group"><label>End Date (optional)</label><input type="date" name="end_date"></div>
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>
<?php include '../../includes/footer.php'; ?>

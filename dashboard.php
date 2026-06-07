<?php
require_once '../../includes/auth.php';
requireRole('patient');
require_once '../../config/database.php';
require_once '../../config/csrf.php';

$patient_id = $_SESSION['user_id'];
$msg = $_GET['msg'] ?? '';
$type = $_GET['type'] ?? '';

$stmt = $conn->prepare("SELECT id, name FROM users WHERE role='doctor' ORDER BY name");
$stmt->execute();
$doctors = $stmt->get_result();
$csrf = generateCSRFToken();

$stmt2 = $conn->prepare("SELECT p.id as presc_id, p.prescription_date, p.medicines, p.notes, u.name as doctor_name FROM prescriptions p JOIN users u ON p.doctor_id = u.id WHERE p.patient_id = ? ORDER BY p.created_at DESC LIMIT 1");
$stmt2->bind_param("i", $patient_id);
$stmt2->execute();
$latest_presc = $stmt2->get_result()->fetch_assoc();

$prescribed_details = [];
if ($latest_presc) {
    $meds = json_decode($latest_presc['medicines'], true) ?? [];
    foreach ($meds as $m) {
        $name = $m['name'] ?? '';
        if (!$name) continue;
        $stmt3 = $conn->prepare("SELECT * FROM medicines WHERE name = ? LIMIT 1");
        $stmt3->bind_param("s", $name);
        $stmt3->execute();
        $prescribed_details[] = ['prescribed' => $m, 'db' => $stmt3->get_result()->fetch_assoc()];
    }
}

$pageTitle = "Patient Dashboard";
include '../../includes/header.php';
?>
<?php if($msg): ?><div class="alert <?= $type ?>"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
<div class="dashboard-grid">
    <div class="card">
        <h2>🩺 Request Consultation</h2>
        <form action="send_request.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
            <div class="form-group"><label>Doctor</label><select name="doctor_id" required><option value="">-- Select --</option><?php while($d=$doctors->fetch_assoc()): ?><option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option><?php endwhile; ?></select></div>
            <div class="form-group"><label>Date</label><input type="date" name="date" required></div>
            <div class="form-group"><label>Time</label><input type="time" name="time" required></div>
            <div class="form-group"><label>Symptoms</label><textarea name="message" rows="3" required></textarea></div>
            <button type="submit" class="btn btn-primary">Send Request</button>
        </form>
    </div>
    <div class="card">
        <h2>Quick Actions</h2>
        <ul class="action-list">
            <li><a href="requests.php">📄 My Requests</a></li>
            <li><a href="prescriptions.php">💊 Prescriptions</a></li>
            <li><a href="reminders.php">⏰ Reminders</a></li>
            <li><a href="add_reminder.php">➕ New Reminder</a></li>
            <li><a href="medicine_search.php">🔍 Medicine Database</a></li>
            <li><a href="view_doctors.php">👨‍⚕️ Doctors List</a></li>
            <li><a href="../consultation/history.php">📜 History</a></li>
        </ul>
    </div>
</div>
<?php if($latest_presc && !empty($prescribed_details)): ?>
<div class="card" style="margin-top:8px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
        <h2 style="margin-bottom:0;">💊 Latest Prescription Medicines</h2>
        <div style="font-size:0.82rem;color:var(--text-muted);">
            🩺 Dr. <?= htmlspecialchars($latest_presc['doctor_name']) ?> &nbsp;·&nbsp;
            <?= date('d M Y', strtotime($latest_presc['prescription_date'])) ?> &nbsp;·&nbsp;
            <a href="prescriptions.php" style="color:var(--primary);font-weight:600;">View All →</a>
        </div>
    </div>
    <div class="med-details-grid">
    <?php foreach($prescribed_details as $entry): $m=$entry['prescribed']; $db=$entry['db']; ?>
        <div class="med-detail-card">
            <div class="med-detail-header">
                <span class="med-detail-name">💊 <?= htmlspecialchars($m['name']??'') ?></span>
                <?php if($db): ?><span class="badge accepted"><?= htmlspecialchars($db['category']) ?></span><?php endif; ?>
            </div>
            <div class="med-detail-row">
                <div class="med-detail-item"><span class="med-detail-label">Dosage</span><span class="med-detail-val"><?= htmlspecialchars($m['dosage']??'-') ?></span></div>
                <div class="med-detail-item"><span class="med-detail-label">Frequency</span><span class="med-detail-val"><?= htmlspecialchars($m['frequency']??'-') ?></span></div>
                <div class="med-detail-item"><span class="med-detail-label">Duration</span><span class="med-detail-val"><?= htmlspecialchars($m['duration']??'-') ?></span></div>
                <?php if($db): ?><div class="med-detail-item"><span class="med-detail-label">Price</span><span class="med-detail-val">৳<?= number_format($db['price'],2) ?></span></div><?php endif; ?>
            </div>
            <?php if($db): ?>
                <?php if($db['description']): ?><div class="med-detail-section"><span class="med-detail-label">📋 Description</span><p><?= htmlspecialchars($db['description']) ?></p></div><?php endif; ?>
                <?php if($db['side_effects']): ?><div class="med-detail-section warn"><span class="med-detail-label">⚠️ Side Effects</span><p><?= htmlspecialchars($db['side_effects']) ?></p></div><?php endif; ?>
                <?php if($db['warnings']): ?><div class="med-detail-section danger"><span class="med-detail-label">🚫 Warnings</span><p><?= htmlspecialchars($db['warnings']) ?></p></div><?php endif; ?>
            <?php else: ?>
                <div class="med-detail-section" style="color:var(--text-muted);font-style:italic;font-size:0.85rem;">ℹ️ Not in our database. Ask your doctor for details.</div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    </div>
    <?php if($latest_presc['notes']): ?>
    <div style="margin-top:16px;padding:12px 16px;background:var(--primary-light);border-radius:var(--radius-sm);border-left:4px solid var(--primary);">
        <div style="font-size:0.75rem;font-weight:700;color:var(--primary);text-transform:uppercase;margin-bottom:4px;">Doctor's Notes</div>
        <div style="font-size:0.9rem;"><?= nl2br(htmlspecialchars($latest_presc['notes'])) ?></div>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>
<style>
.med-details-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px;}
.med-detail-card{background:var(--bg);border:1px solid var(--border);border-radius:var(--radius-sm);padding:16px;transition:box-shadow 0.2s;}
.med-detail-card:hover{box-shadow:var(--shadow-hover);}
.med-detail-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;gap:8px;flex-wrap:wrap;}
.med-detail-name{font-weight:700;font-size:0.95rem;color:var(--text);}
.med-detail-row{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px;}
.med-detail-item{background:var(--card);border-radius:var(--radius-sm);padding:8px 10px;border:1px solid var(--border);}
.med-detail-label{display:block;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:var(--text-muted);margin-bottom:2px;}
.med-detail-val{font-size:0.88rem;font-weight:600;color:var(--text);}
.med-detail-section{font-size:0.85rem;padding:8px 10px;border-radius:var(--radius-sm);margin-bottom:6px;background:var(--card);border:1px solid var(--border);}
.med-detail-section p{margin-top:3px;color:var(--text);line-height:1.5;}
.med-detail-section.warn{border-left:3px solid #f59e0b;}
.med-detail-section.danger{border-left:3px solid #ef4444;}
@media(max-width:768px){.med-details-grid{grid-template-columns:1fr;}.med-detail-row{grid-template-columns:1fr;}}
</style>
<?php include '../../includes/footer.php'; ?>

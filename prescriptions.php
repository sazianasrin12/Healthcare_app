<?php
require_once '../../includes/auth.php';
requireRole('patient');
require_once '../../config/database.php';

$stmt = $conn->prepare("SELECT p.*, u.name as doctor_name, c.date as consult_date FROM prescriptions p JOIN users u ON p.doctor_id = u.id JOIN consultations c ON p.consultation_id = c.id WHERE p.patient_id = ? ORDER BY p.created_at DESC");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$pageTitle = "My Prescriptions";
include '../../includes/header.php';
?>
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
    <h1 style="font-size:1.6rem;font-weight:800;">💊 My Prescriptions</h1>
</div>
<?php if(empty($rows)): ?>
<div class="card" style="text-align:center;padding:48px 24px;color:var(--text-muted);">
    <div style="font-size:3rem;margin-bottom:12px;">📋</div>
    <h3 style="margin-bottom:8px;">No Prescriptions Yet</h3>
    <p>Your doctor's prescriptions will appear here after a consultation.</p>
</div>
<?php else: ?>
<?php foreach($rows as $p): $meds = json_decode($p['medicines'], true) ?? []; ?>
<div class="prescription-card">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:12px;margin-bottom:16px;padding-bottom:14px;border-bottom:2px solid var(--border);">
        <div>
            <div style="font-size:1.05rem;font-weight:700;color:var(--primary);">🩺 Dr. <?= htmlspecialchars($p['doctor_name']) ?></div>
            <div style="font-size:0.82rem;color:var(--text-muted);margin-top:3px;">
                Prescribed on <?= date('d M Y', strtotime($p['prescription_date'])) ?>
                &nbsp;·&nbsp; Consultation: <?= date('d M Y', strtotime($p['consult_date'])) ?>
            </div>
        </div>
        <span class="badge completed">Prescription #<?= $p['id'] ?></span>
    </div>
    <div style="overflow-x:auto;">
        <table class="table" style="margin-top:0;">
            <thead><tr><th>#</th><th>Medicine</th><th>Dosage</th><th>Frequency</th><th>Duration</th></tr></thead>
            <tbody>
            <?php foreach($meds as $i => $m): ?>
            <tr>
                <td style="color:var(--text-muted);font-weight:600;"><?= $i+1 ?></td>
                <td style="font-weight:600;">💊 <?= htmlspecialchars($m['name'] ?? '') ?></td>
                <td><?= htmlspecialchars($m['dosage'] ?? '-') ?></td>
                <td><?= htmlspecialchars($m['frequency'] ?? '-') ?></td>
                <td><?= htmlspecialchars($m['duration'] ?? '-') ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if(!empty($p['notes'])): ?>
    <div style="margin-top:14px;padding:12px 16px;background:var(--primary-light);border-radius:var(--radius-sm);border-left:4px solid var(--primary);">
        <div style="font-size:0.78rem;font-weight:700;color:var(--primary);text-transform:uppercase;margin-bottom:4px;">Doctor's Notes</div>
        <div style="font-size:0.9rem;"><?= nl2br(htmlspecialchars($p['notes'])) ?></div>
    </div>
    <?php endif; ?>
</div>
<?php endforeach; ?>
<?php endif; ?>
<?php include '../../includes/footer.php'; ?>

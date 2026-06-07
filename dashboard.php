<?php
require_once '../../includes/auth.php';
requireRole('doctor');
require_once '../../config/database.php';

$doctor_id = $_SESSION['user_id'];
$msg  = $_GET['msg']  ?? '';
$type = $_GET['type'] ?? '';

// All consultations
$stmt = $conn->prepare("SELECT c.*, u.name as patient_name FROM consultations c JOIN users u ON c.patient_id = u.id WHERE c.doctor_id = ? ORDER BY c.id DESC");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$consultations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Stats
$total     = count($consultations);
$pending   = count(array_filter($consultations, fn($c) => $c['status'] === 'pending'));
$ongoing   = count(array_filter($consultations, fn($c) => $c['status'] === 'ongoing'));
$completed = count(array_filter($consultations, fn($c) => $c['status'] === 'completed'));

$pageTitle = "Doctor Dashboard";
include '../../includes/header.php';
?>

<!-- Page Header -->
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:12px;">
    <div>
        <h1 style="font-size:1.75rem; font-weight:800; color:var(--text); margin:0 0 4px 0;">
            👋 Welcome, <?= htmlspecialchars($_SESSION['name'] ?? '') ?>
        </h1>
        <p style="color:var(--text-muted); font-size:0.9rem; margin:0;">
            <?= date('l, d F Y') ?>
        </p>
    </div>
    <a href="zoom_settings.php" class="btn btn-secondary">🎥 Zoom Settings</a>
</div>

<!-- Alert -->
<?php if ($msg): ?>
    <div class="alert <?= htmlspecialchars($type) ?>" style="margin-bottom:20px;">
        <?= htmlspecialchars($msg) ?>
    </div>
<?php endif; ?>

<!-- Stats Row -->
<div style="display:flex; flex-wrap:wrap; gap:16px; margin-bottom:28px;">

    <div style="flex:1 1 140px; background:var(--card); border:1px solid var(--border); border-radius:var(--radius); padding:20px; text-align:center;">
        <div style="font-size:1.8rem; font-weight:800; color:var(--text);"><?= $total ?></div>
        <div style="font-size:0.8rem; color:var(--text-muted); font-weight:500; margin-top:4px; text-transform:uppercase; letter-spacing:0.05em;">Total</div>
    </div>

    <div style="flex:1 1 140px; background:#fef3c7; border:1px solid #fde68a; border-radius:var(--radius); padding:20px; text-align:center;">
        <div style="font-size:1.8rem; font-weight:800; color:#92400e;"><?= $pending ?></div>
        <div style="font-size:0.8rem; color:#92400e; font-weight:500; margin-top:4px; text-transform:uppercase; letter-spacing:0.05em;">Pending</div>
    </div>

    <div style="flex:1 1 140px; background:#d1fae5; border:1px solid #a7f3d0; border-radius:var(--radius); padding:20px; text-align:center;">
        <div style="font-size:1.8rem; font-weight:800; color:#065f46;"><?= $ongoing ?></div>
        <div style="font-size:0.8rem; color:#065f46; font-weight:500; margin-top:4px; text-transform:uppercase; letter-spacing:0.05em;">Ongoing</div>
    </div>

    <div style="flex:1 1 140px; background:#e0f2fe; border:1px solid #bae6fd; border-radius:var(--radius); padding:20px; text-align:center;">
        <div style="font-size:1.8rem; font-weight:800; color:#0369a1;"><?= $completed ?></div>
        <div style="font-size:0.8rem; color:#0369a1; font-weight:500; margin-top:4px; text-transform:uppercase; letter-spacing:0.05em;">Completed</div>
    </div>

</div>

<!-- Consultations List -->
<div style="font-size:1.1rem; font-weight:700; color:var(--text); margin-bottom:16px;">
    📋 Consultation Requests
</div>

<?php if (empty($consultations)): ?>
    <div style="text-align:center; padding:60px 24px; background:var(--card); border-radius:var(--radius); border:1px solid var(--border);">
        <div style="font-size:3rem; margin-bottom:12px;">🩺</div>
        <h3 style="color:var(--text); margin-bottom:8px;">No consultations yet</h3>
        <p style="color:var(--text-muted);">Patient requests will appear here.</p>
    </div>

<?php else:
    $colors = ['#0ea5e9','#8b5cf6','#10b981','#f59e0b','#ef4444','#06b6d4','#6366f1'];
    foreach ($consultations as $row):
        $words    = explode(' ', trim($row['patient_name']));
        $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
        $color    = $colors[abs(crc32($row['patient_name'])) % count($colors)];

        $statusStyles = [
            'pending'   => ['bg'=>'#fef3c7', 'color'=>'#92400e', 'border'=>'#fde68a'],
            'accepted'  => ['bg'=>'#dbeafe', 'color'=>'#1e40af', 'border'=>'#bfdbfe'],
            'ongoing'   => ['bg'=>'#d1fae5', 'color'=>'#065f46', 'border'=>'#a7f3d0'],
            'completed' => ['bg'=>'#f1f5f9', 'color'=>'#475569', 'border'=>'#e2e8f0'],
            'rejected'  => ['bg'=>'#fee2e2', 'color'=>'#991b1b', 'border'=>'#fecaca'],
        ];
        $ss = $statusStyles[$row['status']] ?? $statusStyles['completed'];
?>

    <!-- Consultation Card -->
    <div style="background:var(--card); border:1px solid var(--border); border-radius:var(--radius); padding:20px; margin-bottom:14px; display:flex; align-items:center; gap:16px; flex-wrap:wrap; transition:box-shadow 0.2s;"
         onmouseover="this.style.boxShadow='0 8px 24px rgba(0,0,0,0.07)'"
         onmouseout="this.style.boxShadow=''">

        <!-- Avatar -->
        <div style="width:52px; height:52px; border-radius:50%; background:<?= $color ?>22; border:2px solid <?= $color ?>55; display:flex; align-items:center; justify-content:center; font-size:1.1rem; font-weight:700; color:<?= $color ?>; flex-shrink:0;">
            <?= $initials ?>
        </div>

        <!-- Info -->
        <div style="flex:1; min-width:180px;">
            <div style="font-size:1rem; font-weight:700; color:var(--text); margin-bottom:4px;">
                <?= htmlspecialchars($row['patient_name']) ?>
            </div>
            <div style="font-size:0.85rem; color:var(--text-muted); margin-bottom:6px;">
                📅 <?= date('d M Y', strtotime($row['date'])) ?>
                &nbsp;·&nbsp;
                🕐 <?= date('h:i A', strtotime($row['time'])) ?>
            </div>
            <?php if ($row['message']): ?>
            <div style="font-size:0.82rem; color:var(--text-muted); background:var(--bg); border-radius:6px; padding:6px 10px; border-left:3px solid <?= $color ?>; max-width:480px;">
                <?= htmlspecialchars(mb_strimwidth($row['message'], 0, 100, '...')) ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Status + Actions -->
        <div style="display:flex; flex-direction:column; align-items:flex-end; gap:10px; flex-shrink:0;">
            <span style="display:inline-block; padding:4px 12px; border-radius:999px; background:<?= $ss['bg'] ?>; color:<?= $ss['color'] ?>; border:1px solid <?= $ss['border'] ?>; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em;">
                <?= ucfirst($row['status']) ?>
            </span>
            <div style="display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end;">
                <?php if ($row['status'] === 'pending'): ?>
                    <a href="accept.php?id=<?= $row['id'] ?>" class="btn btn-success btn-sm">✓ Accept</a>
                    <a href="reject.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm">✕ Reject</a>
                <?php elseif ($row['status'] === 'accepted'): ?>
                    <a href="start_consultation.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">▶ Start</a>
                <?php elseif ($row['status'] === 'ongoing'): ?>
                    <a href="../consultation/video_call.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">🎥 Join</a>
                    <a href="end_consultation.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm">■ End</a>
                <?php elseif ($row['status'] === 'completed'): ?>
                    <a href="../consultation/view.php?id=<?= $row['id'] ?>" class="btn btn-secondary btn-sm">👁 View</a>
                    <a href="add_prescription.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">💊 Prescribe</a>
                <?php endif; ?>
            </div>
        </div>

    </div>

<?php endforeach; endif; ?>

<?php include '../../includes/footer.php'; ?>
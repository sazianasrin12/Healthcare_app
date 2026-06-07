<?php
require_once '../../includes/auth.php';
requireRole('patient');
require_once '../../config/database.php';

$stmt = $conn->prepare("SELECT id, name, phone FROM users WHERE role='doctor' ORDER BY name");
$stmt->execute();
$doctors = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$pageTitle = "Doctors Directory";
include '../../includes/header.php';
?>

<!-- Page Header -->
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:28px; flex-wrap:wrap; gap:12px;">
    <div>
        <h1 style="font-size:1.75rem; font-weight:800; color:var(--text); margin:0 0 4px 0;">👨‍⚕️ Doctors Directory</h1>
        <p style="color:var(--text-muted); font-size:0.9rem; margin:0;"><?= count($doctors) ?> doctor<?= count($doctors) !== 1 ? 's' : '' ?> available</p>
    </div>
    <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
</div>

<?php if (empty($doctors)): ?>

    <div style="text-align:center; padding:60px 24px; background:var(--card); border-radius:var(--radius); border:1px solid var(--border);">
        <div style="font-size:3rem; margin-bottom:12px;">🩺</div>
        <h3 style="margin-bottom:8px; color:var(--text);">No doctors found</h3>
        <p style="color:var(--text-muted);">Please check back later.</p>
    </div>

<?php else: ?>

    <!-- Doctors Grid -->
    <div style="display:flex; flex-wrap:wrap; gap:20px;">

        <?php
        $colors = ['#0ea5e9','#8b5cf6','#10b981','#f59e0b','#ef4444','#06b6d4','#6366f1'];
        foreach ($doctors as $d):
            $words    = explode(' ', trim($d['name']));
            $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
            $color    = $colors[abs(crc32($d['name'])) % count($colors)];
        ?>

        <!-- Doctor Card -->
        <div style="flex:1 1 220px; max-width:280px; background:var(--card); border:1px solid var(--border); border-radius:var(--radius); padding:28px 20px 20px; text-align:center; display:flex; flex-direction:column; align-items:center; gap:14px; box-shadow:0 2px 8px rgba(0,0,0,0.04); transition:transform 0.2s ease, box-shadow 0.2s ease;"
             onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 16px 32px rgba(0,0,0,0.1)';"
             onmouseout="this.style.transform=''; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.04)';">

            <!-- Avatar -->
            <div style="width:76px; height:76px; border-radius:50%; background:<?= $color ?>22; border:2.5px solid <?= $color ?>66; display:flex; align-items:center; justify-content:center; font-size:1.5rem; font-weight:700; color:<?= $color ?>; flex-shrink:0;">
                <?= $initials ?>
            </div>

            <!-- Name + Badge -->
            <div>
                <div style="font-size:1rem; font-weight:700; color:var(--text); margin-bottom:6px;">
                    Dr. <?= htmlspecialchars($d['name']) ?>
                </div>
                <span style="display:inline-block; padding:3px 12px; border-radius:999px; background:<?= $color ?>18; color:<?= $color ?>; font-size:0.72rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em;">
                    General Physician
                </span>
            </div>

            <!-- Phone -->
            <div style="width:100%; padding:10px 0; border-top:1px solid var(--border); border-bottom:1px solid var(--border); font-size:0.85rem; color:var(--text-muted);">
                📞 <?= $d['phone'] ? htmlspecialchars($d['phone']) : 'Not available' ?>
            </div>

            <!-- Button -->
            <a href="dashboard.php" class="btn btn-primary" style="width:100%; text-align:center; justify-content:center;">
                Request Consultation
            </a>

        </div>

        <?php endforeach; ?>

    </div>

<?php endif; ?>

<?php include '../../includes/footer.php'; ?>
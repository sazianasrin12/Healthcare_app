<?php
require_once '../../includes/auth.php';
requireRole('patient');
require_once '../../config/database.php';

$id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM medicines WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$med = $stmt->get_result()->fetch_assoc();

if (!$med) die("Medicine not found");

include '../../includes/header.php';
?>
<div class="card">
    <h2><?= htmlspecialchars($med['name']) ?></h2>
    <p><strong>Category:</strong> <?= $med['category'] ?></p>
    <p><strong>Price:</strong> ৳<?= $med['price'] ?></p>
    <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($med['description'])) ?></p>
    <?php if($med['dosage_instructions']): ?><p><strong>Dosage:</strong> <?= nl2br(htmlspecialchars($med['dosage_instructions'])) ?></p><?php endif; ?>
    <?php if($med['side_effects']): ?><p><strong>Side Effects:</strong> <?= nl2br(htmlspecialchars($med['side_effects'])) ?></p><?php endif; ?>
    <?php if($med['warnings']): ?><p><strong>Warnings:</strong> <?= nl2br(htmlspecialchars($med['warnings'])) ?></p><?php endif; ?>
</div>
<?php include '../../includes/footer.php'; ?>

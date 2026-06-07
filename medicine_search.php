<?php
require_once '../../includes/auth.php';
requireRole('patient');
require_once '../../config/database.php';

$search = trim($_GET['search'] ?? '');
$medicines = [];
if ($search) {
    $stmt = $conn->prepare("SELECT id, name, category, price FROM medicines WHERE name LIKE ? OR category LIKE ?");
    $term = "%$search%";
    $stmt->bind_param("ss", $term, $term);
    $stmt->execute();
    $medicines = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
include '../../includes/header.php';
?>
<h1>Medicine Database</h1>
<form method="GET">
    <div class="search-box"><input type="text" name="search" placeholder="Search medicine..." value="<?= htmlspecialchars($search) ?>"><button type="submit" class="btn btn-primary">Search</button></div>
</form>
<?php if ($search && empty($medicines)): ?><p>No results found.</p><?php endif; ?>
<div class="medicine-list">
    <?php foreach($medicines as $m): ?>
        <a href="medicine_details.php?id=<?= $m['id'] ?>" class="med-item" style="display:block; padding:12px; background:var(--bg); border-radius:8px; margin-bottom:8px; text-decoration:none; color:var(--text); border:1px solid var(--border);"><strong><?= htmlspecialchars($m['name']) ?></strong> - <?= $m['category'] ?> (৳<?= $m['price'] ?>)</a>
    <?php endforeach; ?>
</div>
<?php include '../../includes/footer.php'; ?>

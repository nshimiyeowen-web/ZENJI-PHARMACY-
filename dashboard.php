<?php
require 'includes/auth.php';
require 'includes/connect.php';
require_login();

// READ: search and filter medicines
$q = trim($_GET['q'] ?? '');
$cat = $_GET['category'] ?? '';

$sql = "SELECT m.*, c.name AS category FROM medicines m JOIN categories c ON c.id = m.category_id WHERE 1";
$params = [];
$types = "";

if ($q !== '') {
    $sql .= " AND (m.name LIKE ? OR m.supplier LIKE ?)";
    $like = "%$q%";
    $params[] = $like;
    $params[] = $like;
    $types .= "ss";
}
if ($cat !== '') {
    $sql .= " AND m.category_id = ?";
    $params[] = $cat;
    $types .= "i";
}
$sql .= " ORDER BY m.name";

// Prepare and execute statement for medicines
$stmt = mysqli_prepare($conn, $sql);
if ($params) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

// Fetch categories for the dropdown filter
$cat_result = mysqli_query($conn, "SELECT id, name FROM categories ORDER BY name");
$cats = mysqli_fetch_all($cat_result, MYSQLI_ASSOC);

// Fetch stats
$stats_query = "SELECT COUNT(*) total, COALESCE(SUM(quantity*unit_price),0) value,
  SUM(quantity <= reorder_level) low, SUM(expiry_date < DATE_ADD(CURDATE(), INTERVAL 90 DAY)) expiring FROM medicines";
$stats_result = mysqli_query($conn, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);

$title = 'Medicines';
require 'includes/app_header.php';
flash();
?>
<div class="stats">
  <div class="stat">Medicines<b><?= (int)$stats['total'] ?></b></div>
  <div class="stat">Stock value (UGX)<b><?= number_format($stats['value']) ?></b></div>
  <div class="stat">Low stock<b><?= (int)$stats['low'] ?></b></div>
  <div class="stat">Expiring in 90 days<b><?= (int)$stats['expiring'] ?></b></div>
</div>
<form class="filters" method="get">
  <div><label>Search</label><input name="q" value="<?= e($q) ?>" placeholder="Medicine or supplier"></div>
  <div><label>Category</label><select name="category"><option value="">All</option>
    <?php foreach ($cats as $c): ?><option value="<?= $c['id'] ?>" <?= $cat == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?>
  </select></div>
  <div><button class="btn">Filter</button> <a class="btn secondary" href="dashboard.php">Reset</a></div>
</form>
<table>
  <tr><th>Name</th><th>Category</th><th>Supplier</th><th>Qty</th><th>Price (UGX)</th><th>Expiry</th><th>Actions</th></tr>
  <?php foreach ($rows as $r): ?>
  <tr>
    <td><?= e($r['name']) ?></td><td><?= e($r['category']) ?></td><td><?= e($r['supplier']) ?></td>
    <td><?= (int)$r['quantity'] ?><?= $r['quantity'] <= $r['reorder_level'] ? ' ⚠' : '' ?></td>
    <td><?= number_format($r['unit_price']) ?></td><td><?= e($r['expiry_date']) ?></td>
    <td>
      <a class="btn secondary" href="edit.php?id=<?= $r['id'] ?>">Edit</a>
      <?php if ($_SESSION['role'] === 'admin'): ?>
      <form class="confirm-delete" action="delete.php" method="post" style="display:inline">
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>"><input type="hidden" name="id" value="<?= $r['id'] ?>">
        <button class="btn danger">Delete</button>
      </form>
      <?php endif; ?>
    </td>
  </tr>
  <?php endforeach; if (!$rows) echo '<tr><td colspan="7">No medicines found.</td></tr>'; ?>
</table>
<?php require 'includes/app_footer.php'; ?>
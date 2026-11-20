<?php
require 'includes/auth.php';
require 'includes/connect.php';
require_login();

$type = $_GET['type'] ?? 'stock';
$cat = $_GET['category'] ?? '';
$days = max(1, (int)($_GET['days'] ?? 90));
$params = [];
$types = '';
$where = ' WHERE 1';

if ($cat !== '') { 
    $where .= ' AND m.category_id = ?'; 
    $params[] = $cat; 
    $types .= 'i';
}

if ($type === 'low') {
    $where .= ' AND m.quantity <= m.reorder_level'; 
    $heading = 'Low stock report';
} elseif ($type === 'expiry') {
    $where .= ' AND m.expiry_date <= DATE_ADD(CURDATE(), INTERVAL ? DAY)'; 
    $params[] = $days; 
    $types .= 'i';
    $heading = "Expiring within $days days";
} elseif ($type === 'category') {
    $heading = 'Stock summary by category';
} else { 
    $type = 'stock'; 
    $heading = 'Full stock report'; 
}

if ($type === 'category') {
    $sql = "SELECT c.name AS Category, COUNT(m.id) AS Items, SUM(m.quantity) AS Units, SUM(m.quantity*m.unit_price) AS `Value (UGX)`
        FROM categories c JOIN medicines m ON m.category_id = c.id $where GROUP BY c.id ORDER BY c.name";
} else {
    $sql = "SELECT m.name AS Medicine, c.name AS Category, m.supplier AS Supplier, m.quantity AS Qty,
        m.unit_price AS `Price (UGX)`, m.quantity*m.unit_price AS `Value (UGX)`, m.expiry_date AS Expiry
        FROM medicines m JOIN categories c ON c.id = m.category_id $where ORDER BY m.name";
}

$stmt = mysqli_prepare($conn, $sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

// EXPORT to CSV (opens in Excel)
if (isset($_GET['export'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="zenji_' . $type . '_report_' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    if ($rows) {
        fputcsv($out, array_keys($rows[0]));
        foreach ($rows as $r) {
            fputcsv($out, $r);
        }
    }
    fclose($out);
    exit;
}

// Fetch categories for dropdown
$cat_result = mysqli_query($conn, 'SELECT id, name FROM categories ORDER BY name');
$cats = mysqli_fetch_all($cat_result, MYSQLI_ASSOC);

$title = 'Reports';
require 'includes/app_header.php';
?>
<form class="filters no-print" method="get">
  <div><label>Report</label><select name="type">
    <?php foreach (['stock' => 'Full stock', 'low' => 'Low stock', 'expiry' => 'Expiring soon', 'category' => 'By category'] as $k => $v): ?>
    <option value="<?= $k ?>" <?= $type === $k ? 'selected' : '' ?>><?= $v ?></option><?php endforeach; ?></select></div>
  <div><label>Category</label><select name="category"><option value="">All</option>
    <?php foreach ($cats as $c): ?><option value="<?= $c['id'] ?>" <?= $cat == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select></div>
  <div><label>Expiry window (days)</label><input type="number" name="days" min="1" value="<?= $days ?>"></div>
  <div><button class="btn">View</button> <button class="btn secondary" name="export" value="1">Export CSV</button>
    <button type="button" class="btn secondary" onclick="window.print()">Print</button></div>
</form>
<h2><?= e($heading) ?></h2>
<p>Generated <?= date('d M Y H:i') ?> by <?= e($_SESSION['full_name']) ?> — <?= count($rows) ?> rows</p>
<table>
  <?php if ($rows): ?><tr><?php foreach (array_keys($rows[0]) as $h) echo '<th>' . e($h) . '</th>'; ?></tr><?php endif; ?>
  <?php foreach ($rows as $r): ?><tr><?php foreach ($r as $v) echo '<td>' . e(is_numeric($v) && $v > 999 ? number_format($v) : $v) . '</td>'; ?></tr><?php endforeach; ?>
  <?php if (!$rows) echo '<tr><td>No records match this report.</td></tr>'; ?>
</table>
<?php require 'includes/app_footer.php'; ?>
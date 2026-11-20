<?php
// Reads and validates medicine fields from POST into $m and $errors
$m = [
  'name' => trim($_POST['name'] ?? ''), 'category_id' => $_POST['category_id'] ?? '',
  'supplier' => trim($_POST['supplier'] ?? ''), 'quantity' => $_POST['quantity'] ?? '',
  'reorder_level' => $_POST['reorder_level'] ?? '', 'unit_price' => $_POST['unit_price'] ?? '',
  'expiry_date' => $_POST['expiry_date'] ?? '',
];
$errors = [];
if (strlen($m['name']) < 2) $errors[] = 'Name is required.';
if (!ctype_digit((string)$m['category_id'])) $errors[] = 'Choose a category.';
if ($m['supplier'] === '') $errors[] = 'Supplier is required.';
foreach (['quantity', 'reorder_level', 'unit_price'] as $f)
    if (!is_numeric($m[$f]) || $m[$f] < 0) $errors[] = ucfirst(str_replace('_', ' ', $f)) . ' must be a positive number.';
if (!DateTime::createFromFormat('Y-m-d', $m['expiry_date'])) $errors[] = 'Expiry date is invalid.';

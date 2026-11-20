<?php
require 'includes/auth.php';
require 'includes/connect.php';
require_login();

$id = (int)($_GET['id'] ?? 0);

// Fetch medicine using MySQLi
$stmt = mysqli_prepare($conn, 'SELECT * FROM medicines WHERE id = ?');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$m = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$m) { 
    flash('Medicine not found.', 'bad'); 
    header('Location: dashboard.php'); 
    exit; 
}

// Fetch categories for the form using MySQLi
$cat_result = mysqli_query($conn, 'SELECT id, name FROM categories ORDER BY name');
$cats = mysqli_fetch_all($cat_result, MYSQLI_ASSOC);

$errors = [];

// UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    
    // Note: ensure medicine_validate.php populates $errors and handles $_POST variables correctly
    require 'includes/medicine_validate.php';
    
    if (!$errors) {
        // Collect updated values from POST (or fallback to existing values if not set)
        $name = trim($_POST['name'] ?? $m['name']);
        $category_id = (int)($_POST['category_id'] ?? $m['category_id']);
        $supplier = trim($_POST['supplier'] ?? $m['supplier']);
        $quantity = (int)($_POST['quantity'] ?? $m['quantity']);
        $reorder_level = (int)($_POST['reorder_level'] ?? $m['reorder_level']);
        $unit_price = (float)($_POST['unit_price'] ?? $m['unit_price']);
        $expiry_date = trim($_POST['expiry_date'] ?? $m['expiry_date']);

        $update_stmt = mysqli_prepare($conn, 'UPDATE medicines SET name=?, category_id=?, supplier=?, quantity=?, reorder_level=?, unit_price=?, expiry_date=? WHERE id=?');
        mysqli_stmt_bind_param($update_stmt, 'sisiiids', $name, $category_id, $supplier, $quantity, $reorder_level, $unit_price, $expiry_date, $id);
        
        if (mysqli_stmt_execute($update_stmt)) {
            flash('Medicine updated.');
            header('Location: dashboard.php'); 
            exit;
        } else {
            $errors[] = 'Failed to update medicine.';
        }
        mysqli_stmt_close($update_stmt);
    }
}

$title = 'Edit medicine';
require 'includes/app_header.php';
echo '<h2>Edit: ' . e($m['name']) . '</h2>';
require 'includes/medicine_form.php';
require 'includes/app_footer.php';
<?php
require 'includes/auth.php';
require 'includes/connect.php';
require_login();

// Fetch categories using MySQLi
$cat_result = mysqli_query($conn, 'SELECT id, name FROM categories ORDER BY name');
$cats = mysqli_fetch_all($cat_result, MYSQLI_ASSOC);

$m = ['name' => '', 'category_id' => '', 'supplier' => '', 'quantity' => '', 'reorder_level' => 10, 'unit_price' => '', 'expiry_date' => ''];
$errors = [];

// CREATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    
    // Note: ensure medicine_validate.php populates $errors and handles $_POST variables correctly
    require 'includes/medicine_validate.php';
    
    if (!$errors) {
        // Collect posted values safely
        $name = trim($_POST['name'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? 0);
        $supplier = trim($_POST['supplier'] ?? '');
        $quantity = (int)($_POST['quantity'] ?? 0);
        $reorder_level = (int)($_POST['reorder_level'] ?? 10);
        $unit_price = (float)($_POST['unit_price'] ?? 0);
        $expiry_date = trim($_POST['expiry_date'] ?? '');
        $created_by = (int)($_SESSION['user_id'] ?? 0);

        $stmt = mysqli_prepare($conn, 'INSERT INTO medicines (name, category_id, supplier, quantity, reorder_level, unit_price, expiry_date, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'sisiidsi', $name, $category_id, $supplier, $quantity, $reorder_level, $unit_price, $expiry_date, $created_by);
        
        if (mysqli_stmt_execute($stmt)) {
            flash('Medicine added.');
            header('Location: dashboard.php'); 
            exit;
        } else {
            $errors[] = 'Failed to add medicine to the database.';
        }
        mysqli_stmt_close($stmt);
    }
}

$title = 'Add medicine';
require 'includes/app_header.php';
echo '<h2>Add a new medicine</h2>';
require 'includes/medicine_form.php';
require 'includes/app_footer.php';
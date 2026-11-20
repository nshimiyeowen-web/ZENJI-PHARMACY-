<?php
require 'includes/auth.php';
require 'includes/connect.php';
require_admin(); // only administrators may delete

// DELETE (POST only, with CSRF token and a JavaScript confirmation prompt)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $id = (int)($_POST['id'] ?? 0);

    $stmt = mysqli_prepare($conn, 'DELETE FROM medicines WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    
    $affected = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);

    flash($affected > 0 ? 'Medicine deleted.' : 'Medicine not found.', $affected > 0 ? 'ok' : 'bad');
}
header('Location: dashboard.php');
exit;
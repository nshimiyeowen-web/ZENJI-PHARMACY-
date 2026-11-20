<?php
// Initialize variables
$errors = [];
$error = '';
$success = '';
$old = ['full_name' => '', 'email' => ''];

if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
} 

if (!function_exists('csrf_token')) {
    function csrf_token() {
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf'];
    }
}

require_once 'includes/connect.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    $old['full_name'] = $full_name;
    $old['email'] = $email;

    if (empty($full_name) || empty($email) || empty($password) || empty($confirm)) {
        $errors[] = "All fields are required.";
    } elseif ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    } else {
        // Check if email already exists
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors[] = "Email is already registered.";
            mysqli_stmt_close($stmt);
        } else {
            mysqli_stmt_close($stmt);

            // Hash password securely
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user matching the exact table columns
            $insert_stmt = mysqli_prepare($conn, "INSERT INTO users (full_name, email, password_hash) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($insert_stmt, "sss", $full_name, $email, $hashed_password);

            if (mysqli_stmt_execute($insert_stmt)) {
                $success = "Account created successfully! You can now <a href='login.php'>login</a>.";
            } else {
                $errors[] = "Error registering account. Please try again.";
            }
            mysqli_stmt_close($insert_stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | Zenji Pharmacy</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <img src="images/logo.jpg" class="logo" alt="Zenji Pharmacy logo">
    <h1>CREATE ACCOUNT</h1>
    <nav>
        <a href="index1.html">HOME</a>
        <a href="about.html">ABOUT US</a>
        <a href="contact.html">CONTACT US</a>
        <a href="register.php" class="active">REGISTER</a>
        <a href="login.php">LOGIN</a>
    </nav>
</header>
<main>
<form class="box validate" method="post" novalidate>
  <?php foreach ($errors as $er): ?>
      <div class="alert bad"><?= e($er) ?></div>
  <?php endforeach; ?>

  <?php if (!empty($success)): ?>
      <div class="alert good"><?= $success ?></div>
  <?php endif; ?>

  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
  
  <label>Full name</label>
  <input name="full_name" value="<?= e($old['full_name']) ?>" data-rule="required min:3">
  
  <label>Email</label>
  <input name="email" type="email" value="<?= e($old['email']) ?>" data-rule="required email">
  
  <label>Password</label>
  <input name="password" type="password" data-rule="required min:8">
  
  <label>Confirm password</label>
  <input name="confirm" type="password" data-rule="required match:password">
  
  <button class="btn" type="submit">Register</button>
  <p>Already registered? <a href="login.php">Log in</a></p>
</form>
</main>
<footer><p><b>Email: zenji256@gmail.com | Tel: +256 7758968</b></p></footer>
<script src="js/validate.js"></script>
</body>
</html>
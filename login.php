 <?php
$errors = [];
$error = '';
$success = '';
$old = ['email' => ''];

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

if (!function_exists('flash')) {
    function flash($name = '', $message = '') {
        return '';
    }
}

require_once 'includes/connect.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $old['email'] = $email;

    if (empty($email) || empty($password)) {
        $errors[] = "Please fill in all fields.";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, full_name, password_hash, role FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
		if (password_verify($password, $row['password_hash'])) {
			// Password is correct, start session and store the expected keys
			$_SESSION['user_id'] = $row['id'];
			$_SESSION['full_name'] = $row['full_name']; // Fixes the app_header warning
			$_SESSION['role'] = $row['role'];           // Fixes the role warning
			
			header("Location: dashboard.php");
			exit();
		} else {
                $errors[] = "Invalid email or password.";
            }
        } else {
            $errors[] = "Invalid email or password.";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Zenji Pharmacy</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <img src="images/logo.jpg" class="logo" alt="Zenji Pharmacy logo">
    <h1>USER LOGIN</h1>
    <nav>
        <a href="index1.html">HOME</a>
        <a href="about.html">ABOUT US</a>
        <a href="contact.html">CONTACT US</a>
        <a href="register.php">REGISTER</a>
        <a href="login.php" class="active">LOGIN</a>
    </nav>
</header>
<main>
<form class="box validate" method="post" novalidate>
  <?php foreach ($errors as $er): ?>
      <div class="alert bad"><?= e($er) ?></div>
  <?php endforeach; ?>

  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
  
  <label>Email</label>
  <input name="email" type="email" value="<?= e($old['email']) ?>" data-rule="required email">
  
  <label>Password</label>
  <input name="password" type="password" data-rule="required">
  
  <button class="btn" type="submit">Log In</button>
  <p>Don't have an account? <a href="register.php">Register here</a></p>
</form>
</main>
<footer><p><b>Email: zenji256@gmail.com | Tel: +256 7758968</b></p></footer>
<script src="js/validate.js"></script>
</body>
</html>
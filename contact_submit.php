<?php
require 'includes/auth.php';
require 'includes/connect.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: contact.html'); exit; }

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

// Server-side validation (never rely only on JavaScript)
$errors = [];
if (strlen($name) < 3) $errors[] = 'Name is too short.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email is invalid.';
if ($subject === '') $errors[] = 'Subject is required.';
if (strlen($message) < 10) $errors[] = 'Message is too short.';

if (!$errors) {
    $stmt = mysqli_prepare($conn, 'INSERT INTO messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)');
    mysqli_stmt_bind_param($stmt, 'sssss', $name, $email, $phone, $subject, $message);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><title>Message | Zenji Pharmacy</title><link rel="stylesheet" href="css/style.css"></head>
<body><main><div class="card" style="text-align:center">
<?php if ($errors): ?>
  <h2>Please fix the following</h2><?php foreach ($errors as $er) echo '<p class="error">' . e($er) . '</p>'; ?>
  <a class="btn" href="contact.html">Go back</a>
<?php else: ?>
  <h2>Thank you, <?= e($name) ?>!</h2><p>Your message has been received. We will reply to <?= e($email) ?> shortly.</p>
  <a class="btn" href="index1.html">Back to home</a>
<?php endif; ?>
</div></main></body></html>
<?php /* Header for the logged-in web application. Set $title before including. */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title ?? 'Dashboard') ?> | Zenji Pharmacy System</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
  <img src="images/logo.jpg" alt="Zenji Pharmacy logo" class="logo" style="width:90px;height:90px">
  <h1 style="font-size:32px">ZENJI PHARMACY SYSTEM</h1>
  <p class="tagline">Signed in as <?= e($_SESSION['full_name']) ?> (<?= e($_SESSION['role']) ?>)</p>
  <nav>
    <a href="index1.html">WEBSITE</a>
    <a href="dashboard.php">MEDICINES</a>
    <a href="add.php">ADD MEDICINE</a>
    <a href="reports.php">REPORTS</a>
    <?php if ($_SESSION['role'] === 'admin'): ?><a href="messages.php">MESSAGES</a><?php endif; ?>
    <a href="logout.php">LOGOUT</a>
  </nav>
</header>
<main>

<?php
require 'includes/connect.php';
$_SESSION = [];
session_destroy();
header('Location: login.php');

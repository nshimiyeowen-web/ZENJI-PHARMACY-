<?php
// Secure session handling shared by all pages
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
session_start();

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function csrf_token() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function check_csrf() {
    if (!isset($_POST['csrf']) || !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'])) {
        http_response_code(403); die('Invalid request token.');
    }
}
function require_login() {
    if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; }
    // Log out after 30 minutes of inactivity
    if (isset($_SESSION['last']) && time() - $_SESSION['last'] > 1800) {
        session_unset(); session_destroy(); header('Location: login.php?timeout=1'); exit;
    }
    $_SESSION['last'] = time();
}
function require_admin() {
    require_login();
    if ($_SESSION['role'] !== 'admin') { http_response_code(403); die('Administrators only.'); }
}
function flash($msg = null, $type = 'ok') {
    if ($msg !== null) { $_SESSION['flash'] = [$msg, $type]; return; }
    if (!empty($_SESSION['flash'])) {
        [$m, $t] = $_SESSION['flash']; unset($_SESSION['flash']);
        echo '<div class="alert ' . e($t) . '">' . e($m) . '</div>';
    }
}

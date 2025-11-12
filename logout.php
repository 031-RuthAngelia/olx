<?php
session_start();
// Hapus semua variabel sesi
$_SESSION = [];

// Hapus cookie sesi jika ada
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

// Hapus cookie remember_identity
if (isset($_COOKIE['remember_identity'])) {
    setcookie('remember_identity', '', time() - 3600, '/');
}

// Hancurkan sesi
session_destroy();

// Redirect ke login
header('Location: login.php');
echo '<!DOCTYPE html><html><head><meta http-equiv="refresh" content="0;url=login.php"></head><body><script>window.location.replace("login.php");</script></body></html>';
exit;

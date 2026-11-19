<?php
session_start();

$_SESSION = [];

$sessionName = session_name();

if (ini_get('session.use_cookies') && $sessionName !== false) {
    $params = session_get_cookie_params();

    setcookie(
        $sessionName,
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

session_destroy();

header("Location: login.php?logged_out=1");
exit;
?>
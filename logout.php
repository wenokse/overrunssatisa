<?php
session_start();
session_unset();
session_destroy();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000, 
        $params["path"], 
        $params["domain"], 
        $params["secure"], 
        $params["httponly"]
    );
}
setcookie('cookieConsent', '', time() - 3600, '/'); 
setcookie('fbm_467857506159967', '', time() - 3600, '/'); 
setcookie('lastVisit', '', time() - 3600, '/'); 
session_start();
$_SESSION['success'] = 'Successfully logged out.';
header('Location: index');
exit();
?>

<?php
session_start();

// Clear all session variables
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

// Use JavaScript for redirection to bypass server-side URL rewriting issues
// echo '<script>window.location.href = "/Garagelk/public/login.php?status=logout_success";</script>';
// exit();

// The following commented out section is no longer needed as we are using JS redirect
/*
// Redirect to login page with a success message parameter
header('Location: /Garagelk/public/login.php?status=logout_success');
exit();

try {
    echo json_encode([
        'success' => true,
        'message' => 'Logout successful'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error during logout: ' . $e->getMessage()
    ]);
}
*/

try {
    echo json_encode([
        'success' => true,
        'message' => 'Logout successful'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error during logout: ' . $e->getMessage()
    ]);
}
?> 
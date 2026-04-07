<?php
/**
 * logout.php
 * Securely terminates the user session and redirects to the login page.
 */

// 1. Initialize the session
session_start();

// 2. Unset all session variables in memory
$_SESSION = array();

// 3. Delete the session cookie from the browser
// This is critical to prevent the browser from sending the old ID back to the server.
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

// 4. Completely destroy the session data on the server
session_destroy();

// 5. Clear any specific local storage or state if using JS (optional)
// 6. Redirect to the login page with a success message
header("Location: login.php?logout=success");
exit();
?>
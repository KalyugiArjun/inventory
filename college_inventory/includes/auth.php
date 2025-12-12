    <?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/db.php";

/**
 * Check if user logged in
 */
function require_login() {
    if (!isset($_SESSION['user'])) {
        header("Location:login.php");
        exit;
    }
}

/**
 * Check specific role: admin, storekeeper, student, faculty
 */
function require_role($roleArray = []) {
    require_login();
    if (!in_array($_SESSION['role'], $roleArray)) {
        // Access denied simple message
        echo "<h2 style='color:white; text-align:center; margin-top:40px;'>
                Access Denied: You are not allowed to access this page.
              </h2>";
        exit;
    }
}

/**
 * Get current user data (simple)
 */
function current_user() {
    if (!isset($_SESSION['user'])) return null;
    return [
        'id'   => $_SESSION['user'],
        'name' => $_SESSION['name'],
        'role' => $_SESSION['role'],
    ];
}

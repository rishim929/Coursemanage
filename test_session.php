<?php
// Set cookie params to match login.php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/OnlineCourseManagementSystem',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

echo "<h1>Session Debug Info</h1>";
echo "<h2>Session Data:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>Session ID:</h2>";
echo "<p>" . session_id() . "</p>";

echo "<h2>Cookie Info:</h2>";
echo "<pre>";
print_r($_COOKIE);
echo "</pre>";

echo "<h2>Session Cookie Parameters:</h2>";
echo "<pre>";
print_r(session_get_cookie_params());
echo "</pre>";

echo "<h2>Server Info:</h2>";
echo "<p>Request URI: " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p>Script Name: " . $_SERVER['SCRIPT_NAME'] . "</p>";
echo "<p>PHP Self: " . $_SERVER['PHP_SELF'] . "</p>";

if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    echo "<h2 style='color: green;'>✓ Session is AUTHENTICATED</h2>";
} else {
    echo "<h2 style='color: red;'>✗ Session is NOT authenticated</h2>";
}

echo "<hr>";
echo "<p><a href='index.php'>Go to Index</a> | <a href='add.php'>Go to Add Course</a> | <a href='logout.php'>Logout</a></p>";
?>

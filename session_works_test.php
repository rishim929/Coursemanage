<?php
// CRITICAL SESSION TEST - Check if PHP sessions work at all
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/OnlineCourseManagementSystem',
]);
session_start();

echo "<!DOCTYPE html><html><head><title>Session Test</title>";
echo "<style>body{font-family:Arial;margin:20px;}.good{color:green;font-weight:bold;}.bad{color:red;font-weight:bold;}</style></head><body>";
echo "<h1>PHP Session Test</h1>";

// Test if session is working
if (!isset($_SESSION['counter'])) {
    $_SESSION['counter'] = 0;
}
$_SESSION['counter']++;

echo "<p>Session ID: <strong>" . session_id() . "</strong></p>";
echo "<p>Counter: <strong class='good'>" . $_SESSION['counter'] . "</strong></p>";

if ($_SESSION['counter'] == 1) {
    echo "<p class='bad'>This is your FIRST visit. <a href='session_works_test.php'>Click here to refresh</a> - counter should increase to 2.</p>";
} else {
    echo "<p class='good'>✓✓ SESSIONS ARE WORKING! Counter is " . $_SESSION['counter'] . "</p>";
    echo "<p>Now test login: <a href='direct_login_test.php' style='background:blue;color:white;padding:10px;text-decoration:none;'>Go to Direct Login Test</a></p>";
}

echo "<hr>";
echo "<p>Session save path: " . session_save_path() . "</p>";
echo "<p>Cookie params:</p><pre>";
print_r(session_get_cookie_params());
echo "</pre>";

echo "<p>All session data:</p><pre>";
print_r($_SESSION);
echo "</pre>";

echo "<hr>";
echo "<p><a href='?reset=1'>Reset Counter</a> | <a href='session_works_test.php'>Refresh Page</a></p>";

if (isset($_GET['reset'])) {
    session_destroy();
    header("Location: session_works_test.php");
    exit;
}

echo "</body></html>";
?>

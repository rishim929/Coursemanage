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
// Check session at the very start
session_start();
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header("Location: login.php");
    exit;
}

// Check session timeout (30 minutes)
$timeout = 1800;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    session_destroy();
    header("Location: login.php?timeout=1");
    exit;
}

// Update last activity
$_SESSION['last_activity'] = time();

require "db.php";

$q = "%".$_GET['q']."%";
$stmt = $pdo->prepare("SELECT id, name FROM instructors WHERE name LIKE ? LIMIT 10");
$stmt->execute([$q]);

while ($row = $stmt->fetch()) {
    echo "<div onclick=\"selectInstructor(". $row['id'] .", '". htmlspecialchars($row['name']) ."')\">". htmlspecialchars($row['name']) ."</div>";
}
?>
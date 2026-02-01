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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $instructor_id = $_POST['instructor_id'];
    $course_id = $_POST['course_id'];

    $stmt = $pdo->prepare("UPDATE courses SET instructor_id = ? WHERE id = ?");
    if ($stmt->execute([$instructor_id, $course_id])) {
        header("Location: view_instructor_courses.php?id=" . $instructor_id);
        exit();
    } else {
        echo "Error assigning course";
    }
}
?>

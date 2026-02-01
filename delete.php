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

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    $_SESSION['error'] = "Invalid course ID.";
    header("Location: index.php");
    exit;
}

try {
    // First, delete related enrollments
    $stmt = $pdo->prepare("DELETE FROM enrollments WHERE course_id=?");
    $stmt->execute([$id]);

    // Then delete the course
    $stmt = $pdo->prepare("DELETE FROM courses WHERE id=?");
    $stmt->execute([$id]);
    
    $_SESSION['success'] = "Course deleted successfully!";
} catch (PDOException $e) {
    $_SESSION['error'] = "Error deleting course: " . $e->getMessage();
}

session_write_close();
header("Location: index.php");
?>

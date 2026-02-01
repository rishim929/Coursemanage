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
    $_SESSION['error'] = "Invalid instructor ID.";
    header("Location: instructors.php");
    exit;
}

try {
    // Check if instructor has courses
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM courses WHERE instructor_id = ?");
    $stmt->execute([$id]);
    $result = $stmt->fetch();

    if ($result['count'] > 0) {
        $_SESSION['error'] = "Cannot delete instructor with assigned courses. Please reassign or delete the courses first.";
        header("Location: instructors.php");
        exit;
    }

    // Delete the instructor
    $deleteStmt = $pdo->prepare("DELETE FROM instructors WHERE id = ?");
    $deleteStmt->execute([$id]);
    $_SESSION['success'] = "Instructor deleted successfully!";
} catch (PDOException $e) {
    $_SESSION['error'] = "Error deleting instructor: " . $e->getMessage();
}

session_write_close();
header("Location: instructors.php");
?>

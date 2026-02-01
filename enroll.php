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
    $student_name = trim($_POST['student_name']);
    $course_id = $_POST['course_id'];

    if (empty($student_name)) {
        echo "Student name is required.";
        exit;
    }

    // Check if student exists
    $stmt = $pdo->prepare("SELECT id FROM students WHERE name = ?");
    $stmt->execute([$student_name]);
    $student = $stmt->fetch();

    if (!$student) {
        // Create new student
        $stmt = $pdo->prepare("INSERT INTO students (name, email) VALUES (?, ?)");
        $stmt->execute([$student_name, strtolower(str_replace(' ', '.', $student_name)) . '@example.com']);
        $student_id = $pdo->lastInsertId();
    } else {
        $student_id = $student['id'];
    }

    try {
        $stmt = $pdo->prepare(
            "INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)"
        );
        $stmt->execute([$student_id, $course_id]);
        $_SESSION['success'] = "Student enrolled successfully!";
        session_write_close();
        header("Location: enrollments.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error enrolling student: " . $e->getMessage();
        session_write_close();
        header("Location: enrollments.php");
        exit;
    }
}
?>
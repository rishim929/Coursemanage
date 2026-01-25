<?php
require "session_check.php";
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
        header("Location: enrollments.php");
        exit;
    } catch (PDOException $e) {
        echo "Error enrolling student: " . $e->getMessage();
    }
}
?>
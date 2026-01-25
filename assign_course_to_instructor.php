<?php
require "session_check.php";
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

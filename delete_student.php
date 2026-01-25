<?php
require "session_check.php";
require "db.php";

$id = $_GET['id'];

// First, delete related enrollments
$stmt = $pdo->prepare("DELETE FROM enrollments WHERE student_id=?");
$stmt->execute([$id]);

// Then delete the student
$stmt = $pdo->prepare("DELETE FROM students WHERE id=?");
$stmt->execute([$id]);

header("Location: students.php");
?>
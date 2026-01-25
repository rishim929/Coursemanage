<?php
require "session_check.php";
require "db.php";

$id = $_GET['id'];

// First, delete related enrollments
$stmt = $pdo->prepare("DELETE FROM enrollments WHERE course_id=?");
$stmt->execute([$id]);

// Then delete the course
$stmt = $pdo->prepare("DELETE FROM courses WHERE id=?");
$stmt->execute([$id]);

header("Location: index.php");
?>

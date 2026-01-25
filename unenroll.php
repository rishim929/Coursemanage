<?php
require "session_check.php";
require "db.php";

$id = $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM enrollments WHERE id=?");
$stmt->execute([$id]);

header("Location: enrollments.php");
?>
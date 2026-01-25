<?php
require "session_check.php";
require "db.php";

$id = $_GET['id'];

// Check if instructor has courses
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM courses WHERE instructor_id = ?");
$stmt->execute([$id]);
$result = $stmt->fetch();

if ($result['count'] > 0) {
    echo "Cannot delete instructor with assigned courses. Please reassign or delete the courses first.";
    echo "<br><a href='instructors.php'>Back to Instructors</a>";
    exit();
}

// Delete the instructor
$deleteStmt = $pdo->prepare("DELETE FROM instructors WHERE id = ?");
if ($deleteStmt->execute([$id])) {
    header("Location: instructors.php");
    exit();
} else {
    echo "Error deleting instructor";
}
?>

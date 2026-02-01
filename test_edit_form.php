<?php
session_start();
require "db.php";

echo "<h1>Edit Course Test</h1>";

// Get first course
$stmt = $pdo->query("SELECT * FROM courses LIMIT 1");
$course = $stmt->fetch();

if (!$course) {
    echo "No courses found";
    exit;
}

$id = $course['id'];
echo "Testing with course ID: $id<br>";
echo "Course: " . htmlspecialchars($course['title']) . "<br>";

// Get instructors
$instructors = $pdo->query("SELECT id, name FROM instructors")->fetchAll(PDO::FETCH_ASSOC);

echo "<form method='POST' action='edit.php?id=" . $id . "'>";
echo "<label>Title: <input type='text' name='title' value='" . htmlspecialchars($course['title']) . "' required></label><br>";
echo "<label>Category: <input type='text' name='category' value='" . htmlspecialchars($course['category']) . "' required></label><br>";
echo "<label>Level: <input type='text' name='level' value='" . htmlspecialchars($course['level']) . "' required></label><br>";
echo "<label>Instructor: <select name='instructor' required>";
foreach ($instructors as $inst) {
    $selected = $inst['id'] == $course['instructor_id'] ? 'selected' : '';
    echo "<option value='" . $inst['id'] . "' $selected>" . htmlspecialchars($inst['name']) . "</option>";
}
echo "</select></label><br>";
echo "<button type='submit'>Update Course</button>";
echo "</form>";
?>

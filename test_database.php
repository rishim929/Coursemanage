<?php
session_start();
require "db.php";

echo "<h1>CRUD Operation Test</h1>";
echo "<style>body { font-family: Arial; margin: 20px; }</style>";

// Test 1: Database connection
try {
    $test = $pdo->query("SELECT 1");
    echo "✅ Database connection: OK<br>";
} catch (Exception $e) {
    echo "❌ Database connection: FAILED - " . $e->getMessage() . "<br>";
    exit;
}

// Test 2: Check courses table
try {
    $count = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
    echo "✅ Courses table: OK ($count courses)<br>";
} catch (Exception $e) {
    echo "❌ Courses table: FAILED<br>";
}

// Test 3: Check instructors table
try {
    $count = $pdo->query("SELECT COUNT(*) FROM instructors")->fetchColumn();
    echo "✅ Instructors table: OK ($count instructors)<br>";
} catch (Exception $e) {
    echo "❌ Instructors table: FAILED<br>";
}

// Test 4: List all courses with instructors
echo "<h2>All Courses:</h2>";
try {
    $courses = $pdo->query("
        SELECT c.*, i.name as instructor_name 
        FROM courses c 
        LEFT JOIN instructors i ON c.instructor_id = i.id
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($courses) > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Title</th><th>Category</th><th>Level</th><th>Instructor</th><th>Edit Link</th><th>Delete Link</th></tr>";
        foreach ($courses as $course) {
            $editLink = "edit.php?id=" . $course['id'];
            $deleteLink = "delete.php?id=" . $course['id'];
            echo "<tr>";
            echo "<td>" . $course['id'] . "</td>";
            echo "<td>" . htmlspecialchars($course['title']) . "</td>";
            echo "<td>" . htmlspecialchars($course['category']) . "</td>";
            echo "<td>" . htmlspecialchars($course['level']) . "</td>";
            echo "<td>" . htmlspecialchars($course['instructor_name'] ?? 'N/A') . "</td>";
            echo "<td><a href='$editLink'>Edit</a></td>";
            echo "<td><a href='$deleteLink'>Delete</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No courses found. <a href='index.php'>Add one</a>";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}

echo "<hr>";
echo "<p><a href='index.php'>Back to Course Management</a></p>";
?>

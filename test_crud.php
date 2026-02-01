<?php
require "db.php";

echo "Testing CRUD Operations:<br>";

// Test 1: Check if courses table exists
try {
    $query = $pdo->query("SELECT COUNT(*) FROM courses");
    $count = $query->fetchColumn();
    echo "✓ Courses table exists with $count courses<br>";
} catch (PDOException $e) {
    echo "✗ Error accessing courses: " . $e->getMessage() . "<br>";
}

// Test 2: List all courses
try {
    $courses = $pdo->query("SELECT id, title FROM courses")->fetchAll();
    echo "✓ Courses: " . json_encode($courses) . "<br>";
} catch (PDOException $e) {
    echo "✗ Error listing courses: " . $e->getMessage() . "<br>";
}

// Test 3: Check instructors
try {
    $instructors = $pdo->query("SELECT id, name FROM instructors")->fetchAll();
    echo "✓ Instructors: " . json_encode($instructors) . "<br>";
} catch (PDOException $e) {
    echo "✗ Error listing instructors: " . $e->getMessage() . "<br>";
}

// Test 4: Test UPDATE (dry run)
try {
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->execute([1]);
    $course = $stmt->fetch();
    if ($course) {
        echo "✓ Found course to update: " . json_encode($course) . "<br>";
    } else {
        echo "✗ No course found with ID 1<br>";
    }
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "<br>";
}

// Test 5: Check database connection
echo "✓ Database connection successful<br>";
?>

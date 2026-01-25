<?php
require 'db.php';
try {
    $stmt = $pdo->prepare("INSERT INTO courses (title, category, level, instructor_id) VALUES (?, ?, ?, ?)");
    $stmt->execute(['Test Course', 'Test Category', 'Beginner', 1]);
    echo "Course added successfully\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
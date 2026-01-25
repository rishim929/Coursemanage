<?php
require 'db.php';
try {
    $stmt = $pdo->query('SELECT * FROM courses');
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Courses:\n";
    foreach ($courses as $course) {
        echo $course['id'] . ': ' . $course['title'] . ' (' . $course['instructor_id'] . ")\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
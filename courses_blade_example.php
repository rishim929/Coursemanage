<?php
/**
 * Example of using Blade templating in your application
 */

require 'session_check.php';
require 'blade_config.php';
require 'db.php';

// Get the Blade instance
$blade = require 'blade_config.php';

try {
    // Fetch courses from database
    $stmt = $pdo->prepare("SELECT id, course_name, description FROM courses");
    $stmt->execute();
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Render Blade template and display
    echo $blade->render('courses', [
        'title' => 'Courses Management',
        'courses' => $courses
    ]);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

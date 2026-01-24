<?php
require "session_check.php";
require "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    file_put_contents('debug.log', date('Y-m-d H:i:s') . " - POST received: " . print_r($_POST, true) . "\n", FILE_APPEND);
    $title = $_POST['title'];
    $category = $_POST['category'];
    $level = $_POST['level'];
    $instructor = $_POST['instructor'];

    if (empty($instructor)) {
        echo "Please select a valid instructor from the suggestions.";
        exit;
    }

    try {
        $stmt = $pdo->prepare(
            "INSERT INTO courses (title, category, level, instructor_id)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$title, $category, $level, $instructor]);
        file_put_contents('debug.log', date('Y-m-d H:i:s') . " - Insert successful\n", FILE_APPEND);
        header("Location: index.php");
        exit;
    } catch (PDOException $e) {
        file_put_contents('debug.log', date('Y-m-d H:i:s') . " - Error: " . $e->getMessage() . "\n", FILE_APPEND);
        echo "Error adding course: " . $e->getMessage();
    }
}
?>

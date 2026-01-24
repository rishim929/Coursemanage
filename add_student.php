<?php
require "session_check.php";
require "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];

    try {
        $stmt = $pdo->prepare(
            "INSERT INTO students (name, email) VALUES (?, ?)"
        );
        $stmt->execute([$name, $email]);
        header("Location: students.php");
        exit;
    } catch (PDOException $e) {
        echo "Error adding student: " . $e->getMessage();
    }
}
?>
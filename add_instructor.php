<?php
require "session_check.php";
require "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'] ?? null;

    $stmt = $pdo->prepare("INSERT INTO instructors (name, email) VALUES (?, ?)");
    if ($stmt->execute([$name, $email])) {
        header("Location: instructors.php");
        exit();
    } else {
        echo "Error adding instructor";
    }
}
?>

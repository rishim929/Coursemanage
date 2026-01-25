<?php
require "session_check.php";
require "db.php";

$id = $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $pdo->prepare(
        "UPDATE students SET name=?, email=? WHERE id=?"
    );
    $stmt->execute([
        $_POST['name'],
        $_POST['email'],
        $id
    ]);
    header("Location: students.php");
}

// Fetch current student data
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
</head>
<body>
    <h1>Edit Student</h1>
    <form action="edit_student.php?id=<?= $id ?>" method="post">
        <label>Name: <input type="text" name="name" value="<?= htmlspecialchars($student['name']) ?>" required></label><br>
        <label>Email: <input type="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" required></label><br>
        <button type="submit">Update Student</button>
    </form>
    <a href="students.php">Back to Students</a>
</body>
</html>
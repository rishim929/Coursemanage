<?php
require "session_check.php";
require "db.php";

$id = $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $pdo->prepare(
        "UPDATE courses SET title=?, category=?, level=?, instructor_id=? WHERE id=?"
    );
    $stmt->execute([
        $_POST['title'],
        $_POST['category'],
        $_POST['level'],
        $_POST['instructor'],
        $id
    ]);
    header("Location: index.php");
}

// Fetch current course data
$stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->execute([$id]);
$course = $stmt->fetch();

// Fetch instructors
$instructorsQuery = $pdo->query("SELECT id, name FROM instructors");
$instructors = $instructorsQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course</title>
</head>
<body>
    <h1>Edit Course</h1>
    <form action="edit.php?id=<?= $id ?>" method="post">
        <label>Title: <input type="text" name="title" value="<?= htmlspecialchars($course['title']) ?>" required></label><br>
        <label>Category: <input type="text" name="category" value="<?= htmlspecialchars($course['category']) ?>" required></label><br>
        <label>Level: <input type="text" name="level" value="<?= htmlspecialchars($course['level']) ?>" required></label><br>
        <label>Instructor: 
            <select name="instructor" required>
                <?php foreach ($instructors as $inst) { ?>
                    <option value="<?= $inst['id'] ?>" <?= $inst['id'] == $course['instructor_id'] ? 'selected' : '' ?>><?= htmlspecialchars($inst['name']) ?></option>
                <?php } ?>
            </select>
        </label><br>
        <button type="submit">Update Course</button>
    </form>
    <a href="index.php">Back to Courses</a>
</body>
</html>

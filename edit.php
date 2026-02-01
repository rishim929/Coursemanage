<?php
// Set cookie params to match login.php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/OnlineCourseManagementSystem',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax',
]);
// Check session at the very start
session_start();
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header("Location: login.php");
    exit;
}

// Check session timeout (30 minutes)
$timeout = 1800;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    session_destroy();
    header("Location: login.php?timeout=1");
    exit;
}

// Update last activity
$_SESSION['last_activity'] = time();

require "db.php";

$id = $_GET['id'] ?? null;
$error = null;
$title = '';
$category = '';
$level = '';
$instructor = '';

if (!$id || !is_numeric($id)) {
    header("Location: index.php");
    exit;
}

// Fetch current course data first
$stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->execute([$id]);
$course = $stmt->fetch();

if (!$course) {
    header("Location: index.php");
    exit;
}

// Pre-populate with existing data or POST data
$title = $_POST['title'] ?? $course['title'];
$category = $_POST['category'] ?? $course['category'];
$level = $_POST['level'] ?? $course['level'];
$instructor = $_POST['instructor'] ?? $course['instructor_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $level = trim($_POST['level'] ?? '');
    $instructor = trim($_POST['instructor'] ?? '');

    if (empty($title) || empty($category) || empty($level) || empty($instructor)) {
        $error = "All fields are required.";
    } else {
        try {
            $stmt = $pdo->prepare(
                "UPDATE courses SET title=?, category=?, level=?, instructor_id=? WHERE id=?"
            );
            $stmt->execute([$title, $category, $level, $instructor, $id]);
            $_SESSION['success'] = "Course updated successfully!";
            session_write_close();
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            $error = "Error updating course: " . $e->getMessage();
        }
    }
}

// Fetch instructors
$instructorsQuery = $pdo->query("SELECT id, name FROM instructors ORDER BY name");
$instructors = $instructorsQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        h1 {
            color: blue;
            text-align: center;
        }
        form {
            background: white;
            padding: 20px;
            border-radius: 5px;
            max-width: 500px;
            margin: 20px auto;
        }
        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }
        input[type="text"], select {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        button[type="submit"]:hover {
            background-color: #45a049;
        }
        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: blue;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Edit Course</h1>
    
    <!-- Display Messages -->
    <?php if (isset($_SESSION['error'])): ?>
        <div style="background-color: #f8d7da; color: #721c24; padding: 12px; margin: 10px 0; border: 1px solid #f5c6cb; border-radius: 4px;">
            <?= htmlspecialchars($_SESSION['error']); ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div style="background-color: #d4edda; color: #155724; padding: 12px; margin: 10px 0; border: 1px solid #c3e6cb; border-radius: 4px;">
            <?= htmlspecialchars($_SESSION['success']); ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div style="background-color: #f8d7da; color: #721c24; padding: 12px; margin: 10px 0; border: 1px solid #f5c6cb; border-radius: 4px;">
            <?= htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <form action="edit.php?id=<?= $id ?>" method="post">
        <label>Title: <input type="text" name="title" value="<?= htmlspecialchars($title) ?>" required></label><br>
        <label>Category: <input type="text" name="category" value="<?= htmlspecialchars($category) ?>" required></label><br>
        <label>Level: <input type="text" name="level" value="<?= htmlspecialchars($level) ?>" required></label><br>
        <label>Instructor: 
            <select name="instructor" required>
                <?php foreach ($instructors as $inst) { ?>
                    <option value="<?= $inst['id'] ?>" <?= $inst['id'] == $instructor ? 'selected' : '' ?>><?= htmlspecialchars($inst['name']) ?></option>
                <?php } ?>
            </select>
        </label><br>
        <button type="submit">Update Course</button>
    </form>
    <a href="index.php">Back to Courses</a>
</body>
</html>
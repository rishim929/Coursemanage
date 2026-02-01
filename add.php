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

$error = null;
$success = null;
$title = '';
$category = '';
$level = '';
$instructor = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $level = trim($_POST['level'] ?? '');
    $instructor = trim($_POST['instructor'] ?? '');

    // Validate inputs
    if (empty($title) || empty($category) || empty($level) || empty($instructor)) {
        $error = "All fields are required.";
    } else {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO courses (title, category, level, instructor_id)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$title, $category, $level, $instructor]);
            $_SESSION['success'] = "Course added successfully!";
            session_write_close(); // Ensure session is written
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            $error = "Error adding course: " . $e->getMessage();
        }
    }
}

// Get list of instructors for dropdown
$instructors = [];
try {
    $stmt = $pdo->query("SELECT id, name FROM instructors ORDER BY name");
    $instructors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($instructors) && !$error) {
        $error = "No instructors found. Please add an instructor first before adding courses.";
    }
} catch (PDOException $e) {
    $error = "Error loading instructors: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Course</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Add New Course</h2>
        
        <?php if ($error): ?>
            <div class="error-message" style="color: red; padding: 10px; margin-bottom: 15px; background-color: #ffcccc; border: 1px solid red;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div style="margin-bottom: 15px;">
                <label for="title">Course Title:</label><br>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
            </div>

            <div style="margin-bottom: 15px;">
                <label for="category">Category:</label><br>
                <input type="text" id="category" name="category" value="<?php echo htmlspecialchars($category); ?>" required>
            </div>

            <div style="margin-bottom: 15px;">
                <label for="level">Level:</label><br>
                <select id="level" name="level" required>
                    <option value="">-- Select Level --</option>
                    <option value="Beginner" <?php echo ($level == 'Beginner' ? 'selected' : ''); ?>>Beginner</option>
                    <option value="Intermediate" <?php echo ($level == 'Intermediate' ? 'selected' : ''); ?>>Intermediate</option>
                    <option value="Advanced" <?php echo ($level == 'Advanced' ? 'selected' : ''); ?>>Advanced</option>
                </select>
            </div>

            <div style="margin-bottom: 15px;">
                <label for="instructor">Instructor:</label><br>
                <select id="instructor" name="instructor" required>
                    <option value="">-- Select Instructor --</option>
                    <?php foreach ($instructors as $inst): ?>
                        <option value="<?php echo htmlspecialchars($inst['id']); ?>" <?php echo ($instructor == $inst['id'] ? 'selected' : ''); ?>>
                            <?php echo htmlspecialchars($inst['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" style="padding: 8px 15px; background-color: #4CAF50; color: white; border: none; cursor: pointer;">Add Course</button>
            <a href="index.php" style="padding: 8px 15px; background-color: #007BFF; color: white; text-decoration: none; margin-left: 10px;">Back</a>
        </form>
    </div>
</body>
</html>

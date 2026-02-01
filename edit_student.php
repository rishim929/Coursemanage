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
$name = '';
$email = '';

if (!$id || !is_numeric($id)) {
    header("Location: students.php");
    exit;
}

// Fetch current student data first
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch();

if (!$student) {
    header("Location: students.php");
    exit;
}

// Pre-populate with existing data or POST data
$name = $_POST['name'] ?? $student['name'];
$email = $_POST['email'] ?? $student['email'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (empty($name) || empty($email)) {
        $error = "Name and Email are required.";
    } else {
        try {
            $stmt = $pdo->prepare(
                "UPDATE students SET name=?, email=? WHERE id=?"
            );
            $stmt->execute([$name, $email, $id]);
            $_SESSION['success'] = "Student updated successfully!";
            session_write_close();
            header("Location: students.php");
            exit;
        } catch (PDOException $e) {
            $error = "Error updating student: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .form-container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            max-width: 500px;
        }
        .error-message {
            color: red;
            padding: 10px;
            margin-bottom: 15px;
            background-color: #ffcccc;
            border: 1px solid red;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Edit Student</h1>
        
        <?php if ($error): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form action="edit_student.php?id=<?= $id ?>" method="post">
            <div style="margin-bottom: 15px;">
                <label>Name:</label><br>
                <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" required>
            </div>
            <div style="margin-bottom: 15px;">
                <label>Email:</label><br>
                <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
            </div>
            <button type="submit">Update Student</button>
        </form>
        <a href="students.php">Back to Students</a>
    </div>
</body>
</html>
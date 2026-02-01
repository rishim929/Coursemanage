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
$name = '';
$email = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (empty($name) || empty($email)) {
        $error = "Name and Email are required.";
    } else {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO students (name, email) VALUES (?, ?)"
            );
            $stmt->execute([$name, $email]);
            $_SESSION['success'] = "Student added successfully!";
            session_write_close();
            header("Location: students.php");
            exit;
        } catch (PDOException $e) {
            $error = "Error adding student: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Student</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Add New Student</h2>
        
        <?php if ($error): ?>
            <div class="error-message" style="color: red; padding: 10px; margin-bottom: 15px; background-color: #ffcccc; border: 1px solid red;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div style="margin-bottom: 15px;">
                <label for="name">Student Name:</label><br>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
            </div>

            <div style="margin-bottom: 15px;">
                <label for="email">Email:</label><br>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>

            <button type="submit" style="padding: 8px 15px; background-color: #4CAF50; color: white; border: none; cursor: pointer;">Add Student</button>
            <a href="students.php" style="padding: 8px 15px; background-color: #007BFF; color: white; text-decoration: none; margin-left: 10px;">Back</a>
        </form>
    </div>
</body>
</html>
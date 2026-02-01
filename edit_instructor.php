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
    header("Location: instructors.php");
    exit;
}

// Fetch instructor data first
$stmt = $pdo->prepare("SELECT * FROM instructors WHERE id = ?");
$stmt->execute([$id]);
$instructor = $stmt->fetch();

if (!$instructor) {
    header("Location: instructors.php");
    exit;
}

// Pre-populate with existing data or POST data
$name = $_POST['name'] ?? $instructor['name'];
$email = $_POST['email'] ?? $instructor['email'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (empty($name)) {
        $error = "Instructor name is required.";
    } else {
        try {
            $updateStmt = $pdo->prepare("UPDATE instructors SET name = ?, email = ? WHERE id = ?");
            $updateStmt->execute([$name, $email ?: null, $id]);
            $_SESSION['success'] = "Instructor updated successfully!";
            session_write_close();
            header("Location: instructors.php");
            exit;
        } catch (PDOException $e) {
            $error = "Error updating instructor: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Instructor</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        h1 {
            color: #333;
        }
        .form-container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            max-width: 500px;
        }
        label {
            display: block;
            margin: 10px 0 5px 0;
            font-weight: bold;
        }
        input[type="text"],
        input[type="email"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-right: 10px;
        }
        button:hover {
            background-color: #45a049;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #007bff;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
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
    <a href="instructors.php" class="back-link">← Back to Instructors</a>
    
    <div class="form-container">
        <h1>Edit Instructor</h1>
        
        <?php if ($error): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form action="edit_instructor.php?id=<?php echo $id; ?>" method="POST">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>">

            <button type="submit">Update Instructor</button>
        </form>
    </div>
</body>
</html>

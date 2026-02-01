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

// Temporary debug - log what we're checking
$debug_mode = true;
if ($debug_mode && isset($_GET['debug'])) {
    echo "<pre style='background: #f0f0f0; padding: 10px; border: 2px solid red;'>";
    echo "INDEX.PHP DEBUG:\n";
    echo "Session ID: " . session_id() . "\n";
    echo "Session authenticated: " . (isset($_SESSION['authenticated']) ? ($_SESSION['authenticated'] ? 'true' : 'false') : 'NOT SET') . "\n";
    echo "Session data:\n";
    print_r($_SESSION);
    echo "</pre>";
    if (!isset($_SESSION['authenticated'])) {
        echo "<p style='color: red; font-weight: bold;'>Session 'authenticated' key is NOT SET - will redirect to login</p>";
    } elseif ($_SESSION['authenticated'] !== true) {
        echo "<p style='color: red; font-weight: bold;'>Session 'authenticated' is NOT TRUE - will redirect to login</p>";
    }
}

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Course Management System</title>
    <style>
        body {
            background-image: url('com.webp');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        a {
            margin-right: 10px;
            text-decoration: none;
            color: blue;
        }
        a:hover {
            text-decoration: underline;
        }
        h1 {
            text-align: center;
            color: blue;
            background-color: #f0f8f0;
            border: 3px solid #4CAF50;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 2px 8px rgba(76, 175, 80, 0.3);
        }
        h2 {
            color: blue;
        }
        .dark-mode {
            background-color: #121212;
            background-image: none;
            color: #ffffff;
        }
        .dark-mode table {
            background-color: #1e1e1e;
        }
        .dark-mode th {
            background-color: #333;
            color: #fff;
        }
        .dark-mode tr:nth-child(even) {
            background-color: #2a2a2a;
        }
        .dark-mode a {
            color: #87ceeb;
        }
        .dark-mode input, .dark-mode select, .dark-mode button {
            background-color: #333;
            color: #fff;
            border: 1px solid #555;
        }
        #instructor_suggestions {
            border: 1px solid #ccc;
            max-height: 150px;
            overflow-y: auto;
            background: white;
            position: absolute;
            z-index: 1000;
            width: 200px;
        }
        #instructor_suggestions div {
            padding: 5px;
            cursor: pointer;
        }
        #instructor_suggestions div:hover {
            background: #f0f0f0;
        }
        button[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        button[type="submit"]:hover {
            background-color: #45a049;
        }
        .nav-box {
            background-color: white;
            border: 2px solid #4CAF50;
            border-radius: 8px;
            padding: 15px 20px;
            margin: 15px 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
        }
        .nav-box a {
            margin: 0 10px;
        }
    </style>
</head>
<body>
    <h1>Course Management</h1>
    <div class="nav-box">
        <a href="students.php">Manage Students</a> | <a href="instructors.php">Manage Instructors</a> | <a href="enrollments.php">Manage Enrollments</a>
    </div>
    <br>
    
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
    
    <input type="text" id="search" placeholder="Search by category, level, or instructor">
    <div id="result"></div>
    <?php

    $query = $pdo->query("
        SELECT courses.*, instructors.name AS instructor
        FROM courses
        LEFT JOIN instructors ON courses.instructor_id = instructors.id
    ");
    ?>

    <table>
    <tr>
      <th>Title</th>
      <th>Category</th>
      <th>Level</th>
      <th>Instructor</th>
      <th>Action</th>
    </tr>
    <tbody id="courseTableBody">

    <?php while ($row = $query->fetch()) { ?>
    <tr>
    <td><?= htmlspecialchars($row['title']) ?></td>
    <td><?= htmlspecialchars($row['category']) ?></td>
    <td><?= htmlspecialchars($row['level']) ?></td>
    <td><?= htmlspecialchars($row['instructor']) ?></td>
    <td>
      <a href="edit.php?id=<?= $row['id'] ?>">Edit</a>
      <a href="delete.php?id=<?= $row['id'] ?>"
         onclick="return confirm('Delete this course?')">Delete</a>
    </td>
    </tr>
    <?php } ?>
    </tbody>
    </table>

    <h2>Add New Course</h2>
    <form action="add.php" method="post" id="addCourseForm">
        <label>Title: <input type="text" name="title" required></label><br>
        <label>Category: <input type="text" name="category" required></label><br>
        <label>Level: <input type="text" name="level" required></label><br>
        <label>Instructor: 
            <select name="instructor" id="instructor_id" required>
                <option value="">-- Select an Instructor --</option>
                <?php
                $instructors = $pdo->query("SELECT id, name FROM instructors ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($instructors as $inst) {
                    echo "<option value=\"" . htmlspecialchars($inst['id']) . "\">" . htmlspecialchars($inst['name']) . "</option>";
                }
                ?>
            </select>
        </label><br>
        <button type="submit">Add Course</button>
    </form>
    
    <!-- Logout Section at Bottom -->
    <div style="margin-top: 40px; padding: 20px; text-align: center; border-top: 2px solid #4CAF50; background-color: #f0f8f0;">
        <p style="margin: 0 0 15px 0; color: #333;">
            Logged in as: <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
        </p>
        <a href="logout.php" style="display: inline-block; background-color: #f44336; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold; transition: background-color 0.3s;">
            Logout
        </a>
    </div>
    
    <script src="script.js"></script>
</body>
</html>

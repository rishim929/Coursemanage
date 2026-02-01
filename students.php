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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management</title>
    <style>
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
        }
        h2 {
            color: blue;
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
    </style>
</head>
<body>
    <h1>Student Management</h1>
    <a href="index.php">Back to Courses</a>

    <?php

    // Display messages
    if (isset($_SESSION['error'])): ?>
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

    <?php
    $query = $pdo->query("SELECT * FROM students");
    ?>

    <table>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Email</th>
      <th>Action</th>
    </tr>

    <?php while ($row = $query->fetch()) { ?>
    <tr>
    <td><?= htmlspecialchars($row['id']) ?></td>
    <td><?= htmlspecialchars($row['name']) ?></td>
    <td><?= htmlspecialchars($row['email']) ?></td>
    <td>
      <a href="edit_student.php?id=<?= $row['id'] ?>">Edit</a>
      <a href="delete_student.php?id=<?= $row['id'] ?>"
         onclick="return confirm('Delete this student?')">Delete</a>
    </td>
    </tr>
    <?php } ?>
    </table>

    <h2>Add New Student</h2>
    <form action="add_student.php" method="post">
        <label>Name: <input type="text" name="name" required></label><br>
        <label>Email: <input type="email" name="email" required></label><br>
        <button type="submit">Add Student</button>
    </form>
</body>
</html>
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
    <title>Instructor Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        h1, h2 {
            color: #333;
        }
        h1 {
            color: blue;
            text-align: center;
        }
        h2 {
            color: blue;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f0f0f0;
        }
        a {
            margin-right: 10px;
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
            color: #0056b3;
        }
        a.delete {
            color: #dc3545;
        }
        a.delete:hover {
            color: #c82333;
        }
        .form-container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin: 20px 0;
        }
        label {
            display: block;
            margin: 10px 0 5px 0;
            font-weight: bold;
        }
        input[type="text"],
        input[type="email"],
        select {
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
        }
        button:hover {
            background-color: #45a049;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .back-link:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <a href="index.php" class="back-link">← Back to Courses</a>
    
    <h1>Instructor Management</h1>

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
    $query = $pdo->query("SELECT * FROM instructors ORDER BY name");
    ?>

    <h2>All Instructors</h2>
    <table>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Email</th>
      <th>Assigned Courses</th>
      <th>Action</th>
    </tr>

    <?php while ($row = $query->fetch()) { 
        // Count courses for this instructor
        $courseCount = $pdo->prepare("SELECT COUNT(*) as count FROM courses WHERE instructor_id = ?");
        $courseCount->execute([$row['id']]);
        $count = $courseCount->fetch()['count'];
    ?>
    <tr>
    <td><?= htmlspecialchars($row['id']) ?></td>
    <td><?= htmlspecialchars($row['name']) ?></td>
    <td><?= htmlspecialchars($row['email'] ?? 'N/A') ?></td>
    <td><?= $count ?></td>
    <td>
      <a href="edit_instructor.php?id=<?= $row['id'] ?>">Edit</a>
      <a href="view_instructor_courses.php?id=<?= $row['id'] ?>">View Courses</a>
      <a href="delete_instructor.php?id=<?= $row['id'] ?>" class="delete" onclick="return confirm('Delete this instructor?')">Delete</a>
    </td>
    </tr>
    <?php } ?>
    </table>

    <div class="form-container">
        <h2>Add New Instructor</h2>
        <form action="add_instructor.php" method="post">
            <label>Name: <input type="text" name="name" required></label>
            <label>Email: <input type="email" name="email"></label>
            <button type="submit">Add Instructor</button>
        </form>
    </div>
</body>
</html>

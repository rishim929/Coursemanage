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
    <title>Enrollments</title>
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
    <h1>Student Enrollments</h1>
    <a href="index.php">Back to Courses</a> | <a href="students.php">Manage Students</a>
    <?php

    $query = $pdo->query("
        SELECT enrollments.id, students.name AS student, courses.title AS course
        FROM enrollments
        JOIN students ON enrollments.student_id = students.id
        JOIN courses ON enrollments.course_id = courses.id
    ");

    $studentsQuery = $pdo->query("SELECT id, name FROM students");
    $students = $studentsQuery->fetchAll(PDO::FETCH_ASSOC);

    $coursesQuery = $pdo->query("SELECT id, title FROM courses");
    $courses = $coursesQuery->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <table>
    <tr>
      <th>Student</th>
      <th>Course</th>
      <th>Action</th>
    </tr>

    <?php while ($row = $query->fetch()) { ?>
    <tr>
    <td><?= htmlspecialchars($row['student']) ?></td>
    <td><?= htmlspecialchars($row['course']) ?></td>
    <td>
      <a href="unenroll.php?id=<?= $row['id'] ?>"
         onclick="return confirm('Unenroll this student?')">Unenroll</a>
    </td>
    </tr>
    <?php } ?>
    </table>

    <h2>Enroll Student in Course</h2>
    <form action="enroll.php" method="post">
        <label>Student Name: <input type="text" name="student_name" required></label><br>
        <label>Course: 
            <select name="course_id" required>
                <?php foreach ($courses as $course) { ?>
                    <option value="<?= $course['id'] ?>"><?= htmlspecialchars($course['title']) ?></option>
                <?php } ?>
            </select>
        </label><br>
        <button type="submit">Enroll</button>
    </form>
</body>
</html>
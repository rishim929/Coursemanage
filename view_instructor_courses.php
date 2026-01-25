<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Courses</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        h1, h2 {
            color: #333;
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
        .no-courses {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <a href="instructors.php" class="back-link">← Back to Instructors</a>

    <?php
    require "db.php";

    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM instructors WHERE id = ?");
    $stmt->execute([$id]);
    $instructor = $stmt->fetch();

    if (!$instructor) {
        echo "Instructor not found";
        exit();
    }
    ?>

    <h1>Courses for <?= htmlspecialchars($instructor['name']) ?></h1>

    <?php
    $courseQuery = $pdo->prepare("SELECT * FROM courses WHERE instructor_id = ?");
    $courseQuery->execute([$id]);
    $courses = $courseQuery->fetchAll();

    if (empty($courses)) {
        echo "<div class='no-courses'>No courses assigned to this instructor yet.</div>";
    } else {
        echo "<table>";
        echo "<tr>";
        echo "<th>Course Title</th>";
        echo "<th>Category</th>";
        echo "<th>Level</th>";
        echo "<th>Action</th>";
        echo "</tr>";
        
        foreach ($courses as $course) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($course['title']) . "</td>";
            echo "<td>" . htmlspecialchars($course['category']) . "</td>";
            echo "<td>" . htmlspecialchars($course['level']) . "</td>";
            echo "<td>";
            echo "<a href='edit.php?id=" . $course['id'] . "'>Edit</a> ";
            echo "<a href='delete.php?id=" . $course['id'] . "' onclick=\"return confirm('Delete this course?')\">Delete</a>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    ?>

    <div class="form-container">
        <h2>Assign New Subject/Course</h2>
        <form action="assign_course_to_instructor.php" method="post">
            <input type="hidden" name="instructor_id" value="<?= $id ?>">
            <label>Select Course:
                <select name="course_id" required>
                    <option value="">-- Select a Course --</option>
                    <?php
                    $availableCourses = $pdo->prepare("SELECT id, title FROM courses WHERE instructor_id IS NULL OR instructor_id = 0 ORDER BY title");
                    $availableCourses->execute();
                    $unassignedCourses = $availableCourses->fetchAll();

                    // Also show courses already assigned to this instructor
                    $allCourses = $pdo->query("SELECT id, title FROM courses WHERE instructor_id != ? ORDER BY title");
                    $allCourses->execute([$id]);
                    $otherCourses = $allCourses->fetchAll();

                    foreach (array_merge($unassignedCourses, $otherCourses) as $course) {
                        echo "<option value=\"" . htmlspecialchars($course['id']) . "\">" . htmlspecialchars($course['title']) . "</option>";
                    }
                    ?>
                </select>
            </label>
            <button type="submit">Assign Course</button>
        </form>
    </div>
</body>
</html>

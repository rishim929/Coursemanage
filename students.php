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
    require "db.php";

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
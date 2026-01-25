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
    </style>
</head>
<body>
    <a href="instructors.php" class="back-link">← Back to Instructors</a>
    
    <h1>Edit Instructor</h1>

    <?php
    require "session_check.php";
    require "db.php";

    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM instructors WHERE id = ?");
    $stmt->execute([$id]);
    $instructor = $stmt->fetch();

    if (!$instructor) {
        echo "Instructor not found";
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST['name'];
        $email = $_POST['email'] ?? null;

        $updateStmt = $pdo->prepare("UPDATE instructors SET name = ?, email = ? WHERE id = ?");
        if ($updateStmt->execute([$name, $email, $id])) {
            header("Location: instructors.php");
            exit();
        } else {
            echo "Error updating instructor";
        }
    }
    ?>

    <div class="form-container">
        <form action="" method="post">
            <label>Name: <input type="text" name="name" value="<?= htmlspecialchars($instructor['name']) ?>" required></label>
            <label>Email: <input type="email" name="email" value="<?= htmlspecialchars($instructor['email'] ?? '') ?>"></label>
            <button type="submit">Update Instructor</button>
            <button type="button" onclick="history.back()">Cancel</button>
        </form>
    </div>
</body>
</html>

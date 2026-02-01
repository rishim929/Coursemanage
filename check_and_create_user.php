<?php
require 'db.php';

echo "<h2>Database Diagnostic Tool</h2>";

// Check if users table exists
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    $tableExists = $stmt->fetch();
    
    if (!$tableExists) {
        echo "<p style='color: red;'>❌ Users table does NOT exist. Creating it now...</p>";
        
        // Create users table
        $pdo->exec("CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        echo "<p style='color: green;'>✓ Users table created successfully!</p>";
    } else {
        echo "<p style='color: green;'>✓ Users table exists</p>";
    }
    
    // Check existing users
    $stmt = $pdo->query("SELECT id, username, password FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Current Users in Database:</h3>";
    
    if (count($users) === 0) {
        echo "<p style='color: orange;'>⚠ No users found in database!</p>";
    } else {
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Username</th><th>Password</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['id']) . "</td>";
            echo "<td>" . htmlspecialchars($user['username']) . "</td>";
            echo "<td>" . htmlspecialchars($user['password']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Form to create new user
    echo "<hr>";
    echo "<h3>Create New User:</h3>";
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user'])) {
        $new_username = trim($_POST['new_username']);
        $new_password = trim($_POST['new_password']);
        
        if (!empty($new_username) && !empty($new_password)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
                $stmt->execute([$new_username, $new_password]);
                echo "<p style='color: green; font-weight: bold;'>✓ User created successfully!</p>";
                echo "<p>Username: <strong>" . htmlspecialchars($new_username) . "</strong></p>";
                echo "<p>Password: <strong>" . htmlspecialchars($new_password) . "</strong></p>";
                echo "<p><a href='login.php' style='background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Login Page</a></p>";
            } catch (PDOException $e) {
                echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
        } else {
            echo "<p style='color: red;'>Username and password are required!</p>";
        }
    }
    
    ?>
    <form method="POST" style="background: #f5f5f5; padding: 20px; border-radius: 5px; max-width: 400px;">
        <div style="margin-bottom: 15px;">
            <label><strong>Username:</strong></label><br>
            <input type="text" name="new_username" required style="width: 100%; padding: 8px; margin-top: 5px;">
        </div>
        <div style="margin-bottom: 15px;">
            <label><strong>Password:</strong></label><br>
            <input type="text" name="new_password" required style="width: 100%; padding: 8px; margin-top: 5px;">
            <small style="color: #666;">Enter a simple password (not hashed)</small>
        </div>
        <button type="submit" name="create_user" style="background: #4CAF50; color: white; padding: 10px 20px; border: none; cursor: pointer; border-radius: 5px;">
            Create User
        </button>
    </form>
    
    <hr>
    <h3>Quick Test Users:</h3>
    <p>Click to create a test user quickly:</p>
    <?php
    
    if (isset($_GET['quick_create'])) {
        try {
            // Check if admin already exists
            $check = $pdo->prepare("SELECT id FROM users WHERE username = 'admin'");
            $check->execute();
            if ($check->fetch()) {
                echo "<p style='color: orange;'>User 'admin' already exists!</p>";
            } else {
                $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES ('admin', 'admin123')");
                $stmt->execute();
                echo "<p style='color: green; font-weight: bold;'>✓ Test user created!</p>";
                echo "<p>Username: <strong>admin</strong></p>";
                echo "<p>Password: <strong>admin123</strong></p>";
                echo "<p><a href='login.php' style='background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Login Page</a></p>";
            }
        } catch (PDOException $e) {
            echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
    ?>
    <a href="?quick_create=1" style="background: #007BFF; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 10px;">
        Create Test User (admin / admin123)
    </a>
    
<?php
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

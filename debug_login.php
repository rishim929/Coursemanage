<?php
require 'db.php';

// Get all users from database
$stmt = $pdo->query("SELECT id, username, password, LENGTH(password) as pwd_length FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h2>Users in Database:</h2>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>ID</th><th>Username</th><th>Password (visible)</th><th>Password Length</th><th>Password (hex)</th></tr>";

foreach ($users as $user) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($user['id']) . "</td>";
    echo "<td>" . htmlspecialchars($user['username']) . "</td>";
    echo "<td>" . htmlspecialchars($user['password']) . "</td>";
    echo "<td>" . $user['pwd_length'] . "</td>";
    echo "<td>" . bin2hex($user['password']) . "</td>";
    echo "</tr>";
}
echo "</table>";

// Test login simulation
if (isset($_GET['test_username']) && isset($_GET['test_password'])) {
    $test_user = $_GET['test_username'];
    $test_pass = $_GET['test_password'];
    
    echo "<h2>Login Test Results:</h2>";
    echo "<p><strong>Testing with:</strong></p>";
    echo "<p>Username: '" . htmlspecialchars($test_user) . "' (length: " . strlen($test_user) . ")</p>";
    echo "<p>Password: '" . htmlspecialchars($test_pass) . "' (length: " . strlen($test_pass) . ")</p>";
    echo "<p>Password hex: " . bin2hex($test_pass) . "</p>";
    
    $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->execute([$test_user]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "<p style='color: green;'>✓ User found in database</p>";
        echo "<p>DB Password: '" . htmlspecialchars($user['password']) . "' (length: " . strlen($user['password']) . ")</p>";
        echo "<p>DB Password hex: " . bin2hex($user['password']) . "</p>";
        echo "<p>Your Password: '" . htmlspecialchars($test_pass) . "' (length: " . strlen($test_pass) . ")</p>";
        echo "<p>Your Password hex: " . bin2hex($test_pass) . "</p>";
        
        if ($user['password'] === $test_pass) {
            echo "<p style='color: green; font-weight: bold;'>✓ EXACT MATCH - Login should work!</p>";
        } else {
            echo "<p style='color: red; font-weight: bold;'>✗ NO MATCH</p>";
            echo "<p>Comparison details:</p>";
            echo "<pre>";
            echo "DB password bytes: ";
            for ($i = 0; $i < strlen($user['password']); $i++) {
                echo ord($user['password'][$i]) . " ";
            }
            echo "\nYour password bytes: ";
            for ($i = 0; $i < strlen($test_pass); $i++) {
                echo ord($test_pass[$i]) . " ";
            }
            echo "</pre>";
            
            // Try trimmed comparison
            if (trim($user['password']) === trim($test_pass)) {
                echo "<p style='color: orange;'>⚠ Passwords match after trimming whitespace!</p>";
            }
        }
    } else {
        echo "<p style='color: red;'>✗ User not found in database</p>";
    }
}

echo "<hr>";
echo "<h3>Test Your Login:</h3>";
echo "<form method='GET'>";
echo "Username: <input type='text' name='test_username' value=''><br><br>";
echo "Password: <input type='text' name='test_password' value=''><br><br>";
echo "<button type='submit'>Test Login</button>";
echo "</form>";
?>

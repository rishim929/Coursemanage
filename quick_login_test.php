<?php
// Quick login test - check what's happening
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Quick Login Test</h2>";

// Check if we can access the database
require 'db.php';
echo "<p style='color: green;'>✓ Database connected</p>";

// Get users
$stmt = $pdo->query("SELECT id, username, LEFT(password, 20) as pwd_preview, LENGTH(password) as pwd_len FROM users LIMIT 5");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h3>Users in Database:</h3>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Username</th><th>Password Preview</th><th>Password Length</th></tr>";
foreach ($users as $user) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($user['id']) . "</td>";
    echo "<td>" . htmlspecialchars($user['username']) . "</td>";
    echo "<td>" . htmlspecialchars($user['pwd_preview']) . "...</td>";
    echo "<td>" . $user['pwd_len'] . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<hr>";
echo "<h3>Now try to login:</h3>";
echo "<form method='POST' action='login.php'>";
echo "Username: <input type='text' name='username' value='admin'><br><br>";
echo "Password: <input type='password' name='password'><br><br>";
echo "<button type='submit'>Login</button>";
echo "</form>";

echo "<hr>";
echo "<p><a href='session_debug.php'>Check Session Status</a></p>";
?>

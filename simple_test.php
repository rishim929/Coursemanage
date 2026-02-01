<?php
// Simple test to check session flow
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Session Flow Test</h1>";

// Test 1: Can we start a session?
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/OnlineCourseManagementSystem',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

echo "<h2>Step 1: Session Started</h2>";
echo "<p>Session ID: " . session_id() . "</p>";

// Test 2: Set a test value
if (!isset($_SESSION['test_value'])) {
    $_SESSION['test_value'] = 'Hello from session!';
    echo "<p style='color: orange;'>⚠ Test value just set. <a href='simple_test.php'>Refresh page</a> to see if it persists.</p>";
} else {
    echo "<p style='color: green;'>✓ Test value persists: " . $_SESSION['test_value'] . "</p>";
}

// Test 3: Check if authenticated
echo "<h2>Step 2: Check Authentication</h2>";
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    echo "<p style='color: green; font-size: 18px;'>✓✓ YOU ARE AUTHENTICATED!</p>";
    echo "<p>Username: " . ($_SESSION['username'] ?? 'not set') . "</p>";
    echo "<p>User ID: " . ($_SESSION['user_id'] ?? 'not set') . "</p>";
    echo "<hr>";
    echo "<p><strong>Now try accessing index.php:</strong></p>";
    echo "<p><a href='index.php' style='background: green; color: white; padding: 10px 20px; text-decoration: none; display: inline-block; border-radius: 5px;'>GO TO INDEX.PHP</a></p>";
} else {
    echo "<p style='color: red;'>✗ You are NOT authenticated</p>";
    
    // Test 4: Try to login
    echo "<h2>Step 3: Test Login</h2>";
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require 'db.php';
        
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        
        echo "<p>Attempting login with username: <strong>" . htmlspecialchars($username) . "</strong></p>";
        
        $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && trim($user['password']) === $password) {
            echo "<p style='color: green;'>✓ Password matches!</p>";
            
            // Set session exactly like login.php does
            $_SESSION['authenticated'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['login_time'] = time();
            $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
            $_SESSION['last_activity'] = time();
            
            echo "<p style='color: green;'>✓ Session variables set!</p>";
            echo "<p><a href='simple_test.php'>Refresh this page</a> to see if session persists</p>";
        } else {
            echo "<p style='color: red;'>✗ Invalid credentials</p>";
        }
    }
    
    echo "<form method='POST'>";
    echo "<p><input type='text' name='username' placeholder='Username' required style='padding: 8px; width: 200px;'></p>";
    echo "<p><input type='password' name='password' placeholder='Password' required style='padding: 8px; width: 200px;'></p>";
    echo "<p><button type='submit' style='padding: 10px 20px; background: blue; color: white; border: none; cursor: pointer;'>Test Login Here</button></p>";
    echo "</form>";
}

echo "<hr>";
echo "<h3>Session Contents:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<hr>";
echo "<p><a href='login.php'>Go to actual login.php</a> | <a href='logout.php'>Logout</a></p>";
?>

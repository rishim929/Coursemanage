<?php
// Diagnostic script to test login flow
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/OnlineCourseManagementSystem',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

echo "<h2>Session Diagnostic</h2>";
echo "<p><strong>Current Session Data:</strong></p>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
echo "<p><strong>Session Save Path:</strong> " . session_save_path() . "</p>";

if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    echo "<p style='color: green;'>✓ You are authenticated!</p>";
    echo "<p>User ID: " . ($_SESSION['user_id'] ?? 'not set') . "</p>";
    echo "<p>Username: " . ($_SESSION['username'] ?? 'not set') . "</p>";
} else {
    echo "<p style='color: red;'>✗ You are NOT authenticated</p>";
}

echo "<hr>";
echo "<h3>Test Login Form</h3>";
echo "<form method='POST'>";
echo "Username: <input type='text' name='username' value='admin'><br><br>";
echo "Password: <input type='password' name='password' value=''><br><br>";
echo "<button type='submit'>Test Login</button>";
echo "</form>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'db.php';
    
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    echo "<hr><h3>Login Attempt Results:</h3>";
    echo "<p>Username entered: '" . htmlspecialchars($username) . "'</p>";
    echo "<p>Password entered: '" . str_repeat('*', strlen($password)) . "' (length: " . strlen($password) . ")</p>";
    
    if (empty($username) || empty($password)) {
        echo "<p style='color: red;'>❌ Username or password is empty</p>";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                echo "<p style='color: green;'>✓ User found in database</p>";
                echo "<p>User ID: " . $user['id'] . "</p>";
                echo "<p>DB Password length: " . strlen($user['password']) . "</p>";
                echo "<p>Input Password length: " . strlen($password) . "</p>";
                
                if (trim($user['password']) === $password) {
                    echo "<p style='color: green;'>✓ Password matches!</p>";
                    
                    // Set session variables
                    $_SESSION['authenticated'] = true;
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['login_time'] = time();
                    $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
                    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
                    $_SESSION['last_activity'] = time();
                    
                    echo "<p style='color: green; font-weight: bold;'>✓ Session variables set successfully!</p>";
                    echo "<pre>";
                    print_r($_SESSION);
                    echo "</pre>";
                    
                    echo "<p><a href='index.php' style='color: blue; font-size: 18px;'>Click here to go to index.php</a></p>";
                    echo "<p><strong>If automatic redirect doesn't work, the issue is with headers/output buffering</strong></p>";
                    
                } else {
                    echo "<p style='color: red;'>❌ Password does NOT match</p>";
                    echo "<p>DB password (first 10 chars): '" . htmlspecialchars(substr($user['password'], 0, 10)) . "...'</p>";
                }
            } else {
                echo "<p style='color: red;'>❌ User not found in database</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
}
?>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>DIRECT LOGIN TEST - Bypass All Complexity</h1>";
echo "<style>body { font-family: Arial; margin: 20px; } .success { color: green; font-weight: bold; } .error { color: red; font-weight: bold; }</style>";

// Check session configuration
echo "<h2>Step 1: Session Configuration</h2>";
echo "<p>Session save path: " . session_save_path() . "</p>";
echo "<p>Path exists: " . (file_exists(session_save_path()) ? '<span class="success">YES</span>' : '<span class="error">NO</span>') . "</p>";
echo "<p>Path writable: " . (is_writable(session_save_path()) ? '<span class="success">YES</span>' : '<span class="error">NO</span>') . "</p>";

// Start session
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/OnlineCourseManagementSystem',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

echo "<p>Session started: <span class='success'>YES</span></p>";
echo "<p>Session ID: " . session_id() . "</p>";

// Check current session
echo "<h2>Step 2: Current Session Status</h2>";
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    echo "<p class='success' style='font-size: 20px;'>✓✓✓ YOU ARE LOGGED IN!</p>";
    echo "<p>Username: " . htmlspecialchars($_SESSION['username']) . "</p>";
    echo "<p>User ID: " . $_SESSION['user_id'] . "</p>";
    echo "<hr>";
    echo "<p><a href='index.php' style='background: green; color: white; padding: 15px 30px; text-decoration: none; display: inline-block; font-size: 18px;'>GO TO HOME PAGE</a></p>";
    echo "<hr>";
    echo "<p><a href='?logout=1' style='background: red; color: white; padding: 10px 20px; text-decoration: none; display: inline-block;'>Logout & Test Again</a></p>";
} else {
    echo "<p class='error'>NOT LOGGED IN</p>";
    
    // Show login form
    echo "<h2>Step 3: Login Test</h2>";
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require 'db.php';
        
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        
        echo "<div style='background: #f0f0f0; padding: 15px; margin: 10px 0;'>";
        echo "<h3>Login Attempt Details:</h3>";
        echo "<p>Username entered: <strong>" . htmlspecialchars($username) . "</strong></p>";
        echo "<p>Password length: " . strlen($password) . " characters</p>";
        
        // Check database
        $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo "<p class='success'>✓ User found in database</p>";
            echo "<p>User ID: " . $user['id'] . "</p>";
            echo "<p>DB Password length: " . strlen($user['password']) . " characters</p>";
            echo "<p>Passwords match: " . ((trim($user['password']) === $password) ? '<span class="success">YES</span>' : '<span class="error">NO</span>') . "</p>";
            
            if (trim($user['password']) === $password) {
                echo "<hr>";
                echo "<p class='success' style='font-size: 18px;'>✓✓ LOGIN SUCCESSFUL!</p>";
                
                // Set session
                $_SESSION['authenticated'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['login_time'] = time();
                $_SESSION['last_activity'] = time();
                $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
                $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
                
                echo "<p>Session variables set. Current session:</p>";
                echo "<pre>";
                print_r($_SESSION);
                echo "</pre>";
                
                echo "<p><strong>Refreshing page to verify session persists...</strong></p>";
                echo "<meta http-equiv='refresh' content='2;url=direct_login_test.php'>";
                echo "<p><a href='direct_login_test.php'>Or click here</a></p>";
            } else {
                echo "<hr>";
                echo "<p class='error' style='font-size: 18px;'>✗ PASSWORD INCORRECT</p>";
                echo "<p>The password you entered doesn't match the database.</p>";
            }
        } else {
            echo "<p class='error'>✗ User NOT found in database</p>";
            echo "<p>The username '<strong>" . htmlspecialchars($username) . "</strong>' does not exist.</p>";
        }
        echo "</div>";
    }
    
    // Show available users
    require_once 'db.php';
    $stmt = $pdo->query("SELECT username, LEFT(password, 20) as pwd_preview, LENGTH(password) as pwd_len FROM users LIMIT 5");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<div style='background: #fffacd; padding: 15px; margin: 10px 0;'>";
    echo "<h3>Available Users in Database:</h3>";
    echo "<table border='1' cellpadding='8' style='border-collapse: collapse;'>";
    echo "<tr><th>Username</th><th>Password Preview</th><th>Length</th></tr>";
    foreach ($users as $u) {
        echo "<tr>";
        echo "<td><strong>" . htmlspecialchars($u['username']) . "</strong></td>";
        echo "<td><code>" . htmlspecialchars($u['pwd_preview']) . "...</code></td>";
        echo "<td>" . $u['pwd_len'] . " chars</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
    
    echo "<form method='POST' style='background: #e3f2fd; padding: 20px; border-radius: 5px;'>";
    echo "<h3>Login Here:</h3>";
    echo "<p><input type='text' name='username' placeholder='Username' required style='padding: 10px; width: 250px; font-size: 16px;'></p>";
    echo "<p><input type='password' name='password' placeholder='Password' required style='padding: 10px; width: 250px; font-size: 16px;'></p>";
    echo "<p><button type='submit' style='padding: 12px 30px; background: #2196F3; color: white; border: none; font-size: 16px; cursor: pointer; border-radius: 5px;'>LOGIN</button></p>";
    echo "</form>";
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: direct_login_test.php");
    exit;
}

echo "<hr>";
echo "<h3>Current Session Data:</h3>";
echo "<pre style='background: #f5f5f5; padding: 10px;'>";
print_r($_SESSION);
echo "</pre>";
?>

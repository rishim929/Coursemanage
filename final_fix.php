<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><title>FINAL LOGIN FIX</title>";
echo "<style>
body { font-family: Arial; margin: 20px; background: #f5f5f5; }
.container { background: white; padding: 20px; border-radius: 10px; max-width: 800px; margin: 0 auto; }
.success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #c3e6cb; }
.error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #f5c6cb; }
.info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #bee5eb; }
table { width: 100%; border-collapse: collapse; margin: 10px 0; }
th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
th { background: #007bff; color: white; }
input { padding: 10px; width: 100%; font-size: 16px; margin: 5px 0; }
button { padding: 12px 30px; background: #28a745; color: white; border: none; font-size: 16px; cursor: pointer; border-radius: 5px; }
button:hover { background: #218838; }
</style></head><body><div class='container'>";

echo "<h1>🔧 FINAL LOGIN DIAGNOSTIC & FIX</h1>";

// STEP 1: Check database
echo "<h2>Step 1: Database Connection</h2>";
try {
    require 'db.php';
    echo "<div class='success'>✅ Database connected successfully</div>";
    
    // Get users
    $stmt = $pdo->query("SELECT id, username, password, LENGTH(password) as pwd_len FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($users) == 0) {
        echo "<div class='error'>❌ NO USERS in database! Creating admin user...</div>";
        $pdo->exec("INSERT INTO users (username, password) VALUES ('admin', 'admin123')");
        echo "<div class='success'>✅ Created: username=admin, password=admin123</div>";
        $users = $pdo->query("SELECT id, username, password, LENGTH(password) as pwd_len FROM users")->fetchAll(PDO::FETCH_ASSOC);
    }
    
    echo "<table><tr><th>ID</th><th>Username</th><th>Password</th><th>Length</th></tr>";
    foreach ($users as $u) {
        echo "<tr>";
        echo "<td>{$u['id']}</td>";
        echo "<td><strong>{$u['username']}</strong></td>";
        echo "<td><code>{$u['password']}</code></td>";
        echo "<td>{$u['pwd_len']} chars</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Database error: " . htmlspecialchars($e->getMessage()) . "</div>";
    die("Cannot continue without database");
}

// STEP 2: Check session
echo "<h2>Step 2: Session Status</h2>";
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/OnlineCourseManagementSystem',
]);
session_start();

echo "<div class='info'>";
echo "<p>Session ID: <strong>" . session_id() . "</strong></p>";
echo "<p>Session Save Path: " . session_save_path() . "</p>";
echo "<p>Path Writable: " . (is_writable(session_save_path()) ? '✅ YES' : '❌ NO') . "</p>";
echo "</div>";

if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    echo "<div class='success'>";
    echo "<h3>✅✅✅ YOU ARE LOGGED IN!</h3>";
    echo "<p>Username: <strong>" . htmlspecialchars($_SESSION['username']) . "</strong></p>";
    echo "<p>User ID: " . $_SESSION['user_id'] . "</p>";
    echo "<hr>";
    echo "<p><a href='index.php' style='background: #007bff; color: white; padding: 15px 30px; text-decoration: none; display: inline-block; font-size: 18px; border-radius: 5px;'>🏠 GO TO HOME PAGE</a></p>";
    echo "<hr>";
    echo "<p><a href='?logout=1' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; display: inline-block;'>Logout</a></p>";
    echo "</div>";
} else {
    echo "<div class='error'>❌ You are NOT logged in</div>";
    
    // STEP 3: Login test
    echo "<h2>Step 3: Test Login</h2>";
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        
        echo "<div class='info'>";
        echo "<h3>Login Attempt:</h3>";
        echo "<p>Username: <strong>" . htmlspecialchars($username) . "</strong></p>";
        echo "<p>Password: " . str_repeat('*', strlen($password)) . " (" . strlen($password) . " chars)</p>";
        
        $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo "<p>✅ User found in database</p>";
            echo "<p>DB Password: <code>{$user['password']}</code> (" . strlen($user['password']) . " chars)</p>";
            echo "<p>Passwords match: " . ((trim($user['password']) === $password) ? '✅ YES' : '❌ NO') . "</p>";
            
            if (trim($user['password']) === $password) {
                echo "</div><div class='success'>";
                echo "<h3>✅✅ PASSWORD CORRECT!</h3>";
                
                // Set session EXACTLY like login.php
                $_SESSION['authenticated'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['login_time'] = time();
                $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
                $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
                $_SESSION['last_activity'] = time();
                
                echo "<p>Session variables set:</p>";
                echo "<pre>";
                print_r($_SESSION);
                echo "</pre>";
                
                echo "<p><strong>✅✅✅ LOGIN SUCCESSFUL!</strong></p>";
                echo "<p><a href='final_fix.php' style='background: #28a745; color: white; padding: 15px 30px; text-decoration: none; display: inline-block; font-size: 18px;'>REFRESH TO SEE LOGGED IN STATE</a></p>";
                echo "<p>Or go directly to: <a href='index.php' style='background: #007bff; color: white; padding: 15px 30px; text-decoration: none; display: inline-block; font-size: 18px;'>HOME PAGE</a></p>";
                echo "</div>";
            } else {
                echo "<p class='error'>❌ Password incorrect</p>";
                echo "</div>";
            }
        } else {
            echo "<p>❌ User '<strong>" . htmlspecialchars($username) . "</strong>' NOT found</p>";
            echo "</div>";
        }
    }
    
    // Show login form
    echo "<form method='POST'>";
    echo "<h3>Login Here:</h3>";
    echo "<input type='text' name='username' placeholder='Username' required>";
    echo "<input type='password' name='password' placeholder='Password' required>";
    echo "<button type='submit'>🔐 LOGIN</button>";
    echo "</form>";
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: final_fix.php");
    exit;
}

echo "<hr>";
echo "<h3>Current Session Data:</h3>";
echo "<pre style='background: #f8f9fa; padding: 10px; border: 1px solid #ddd;'>";
print_r($_SESSION);
echo "</pre>";

echo "<hr>";
echo "<h3>Quick Links:</h3>";
echo "<p><a href='login.php'>→ Go to login.php</a></p>";
echo "<p><a href='index.php'>→ Go to index.php (home)</a></p>";

echo "</div></body></html>";
?>

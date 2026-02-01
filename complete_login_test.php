<!DOCTYPE html>
<html>
<head>
    <title>Complete Login Test</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .section { background: #f5f5f5; padding: 20px; margin: 10px 0; border-radius: 5px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        input { padding: 8px; margin: 5px 0; }
        button { padding: 10px 20px; background: #4CAF50; color: white; border: none; cursor: pointer; }
        button:hover { background: #45a049; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #4CAF50; color: white; }
    </style>
</head>
<body>
    <h1>Complete Login Diagnostic</h1>
    
    <?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    // Test 1: Database Connection
    echo "<div class='section'>";
    echo "<h2>Test 1: Database Connection</h2>";
    try {
        require 'db.php';
        echo "<p class='success'>✓ Database connected successfully</p>";
        
        // Show users
        $stmt = $pdo->query("SELECT id, username, LENGTH(password) as pwd_len FROM users LIMIT 10");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>Available Users:</h3>";
        echo "<table><tr><th>ID</th><th>Username</th><th>Password Length</th></tr>";
        foreach ($users as $user) {
            echo "<tr><td>{$user['id']}</td><td>{$user['username']}</td><td>{$user['pwd_len']} chars</td></tr>";
        }
        echo "</table>";
    } catch (Exception $e) {
        echo "<p class='error'>✗ Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    echo "</div>";
    
    // Test 2: Session Configuration
    echo "<div class='section'>";
    echo "<h2>Test 2: Session Configuration</h2>";
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/OnlineCourseManagementSystem',
        'domain' => '',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
    
    echo "<p>Session ID: " . session_id() . "</p>";
    echo "<p>Session Name: " . session_name() . "</p>";
    echo "<p>Session Save Path: " . session_save_path() . "</p>";
    echo "<p>Session Status: " . session_status() . " (2 = active)</p>";
    
    if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
        echo "<p class='success'>✓ You are currently logged in!</p>";
        echo "<p>Username: " . htmlspecialchars($_SESSION['username']) . "</p>";
        echo "<p>User ID: " . $_SESSION['user_id'] . "</p>";
        echo "<p><a href='index.php'><button>Go to Home Page</button></a></p>";
        echo "<p><a href='logout.php'><button style='background:red;'>Logout</button></a></p>";
    } else {
        echo "<p class='error'>✗ You are NOT logged in</p>";
    }
    echo "</div>";
    
    // Test 3: Login Form
    if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
        echo "<div class='section'>";
        echo "<h2>Test 3: Manual Login Test</h2>";
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_login'])) {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');
            
            echo "<h3>Login Attempt:</h3>";
            echo "<p>Username: '" . htmlspecialchars($username) . "'</p>";
            echo "<p>Password: " . str_repeat('*', strlen($password)) . " (length: " . strlen($password) . ")</p>";
            
            $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                echo "<p class='success'>✓ User found in database</p>";
                echo "<p>DB Password length: " . strlen($user['password']) . "</p>";
                echo "<p>Input Password length: " . strlen($password) . "</p>";
                
                if (trim($user['password']) === $password) {
                    echo "<p class='success'>✓✓ PASSWORD MATCHES!</p>";
                    
                    // Set session
                    $_SESSION['authenticated'] = true;
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['login_time'] = time();
                    $_SESSION['last_activity'] = time();
                    
                    session_write_close();
                    
                    echo "<p class='success'>✓✓✓ Session variables set!</p>";
                    echo "<p><strong>Redirecting to index.php in 2 seconds...</strong></p>";
                    echo "<meta http-equiv='refresh' content='2;url=index.php'>";
                    echo "<p><a href='index.php'><button>Or click here to go to Home Page</button></a></p>";
                } else {
                    echo "<p class='error'>✗ Password does NOT match</p>";
                    echo "<p>DB password (trimmed): '" . htmlspecialchars(substr(trim($user['password']), 0, 20)) . "...'</p>";
                }
            } else {
                echo "<p class='error'>✗ User not found in database</p>";
            }
        }
        
        echo "<form method='POST'>";
        echo "<h3>Try Login Here:</h3>";
        echo "<p><input type='text' name='username' placeholder='Username' required></p>";
        echo "<p><input type='password' name='password' placeholder='Password' required></p>";
        echo "<p><button type='submit' name='test_login'>Test Login</button></p>";
        echo "</form>";
        
        echo "<hr>";
        echo "<h3>Or use the actual login page:</h3>";
        echo "<p><a href='login.php'><button>Go to Login Page</button></a></p>";
        
        echo "</div>";
    }
    ?>
    
    <div class='section'>
        <h2>Quick Links:</h2>
        <p><a href='session_debug.php'><button>Check Session Details</button></a></p>
        <p><a href='debug_login.php'><button>View All Users & Passwords</button></a></p>
        <p><a href='quick_login_test.php'><button>Quick Login Test</button></a></p>
    </div>
</body>
</html>

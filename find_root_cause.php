<?php
// COMPREHENSIVE LOGIN DIAGNOSTIC - Find the REAL problem
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Complete Login Diagnostic</title>
    <style>
        body { font-family: monospace; background: #1e1e1e; color: #d4d4d4; padding: 20px; }
        .test { background: #2d2d30; padding: 15px; margin: 10px 0; border-left: 4px solid #007acc; }
        .pass { border-left-color: #4ec9b0; }
        .fail { border-left-color: #f48771; }
        .warn { border-left-color: #dcdcaa; }
        h1 { color: #4ec9b0; }
        h2 { color: #569cd6; margin-top: 20px; }
        pre { background: #1e1e1e; padding: 10px; border: 1px solid #3e3e42; overflow-x: auto; }
        .status { font-weight: bold; font-size: 18px; }
        .pass .status { color: #4ec9b0; }
        .fail .status { color: #f48771; }
        .warn .status { color: #dcdcaa; }
    </style>
</head>
<body>
<h1>🔍 COMPREHENSIVE LOGIN DIAGNOSTIC</h1>
<p>Testing EVERY component to find the exact failure point...</p>
<hr>

<?php
$results = [];

// TEST 1: PHP Version and Extensions
echo "<h2>TEST 1: PHP Environment</h2>";
echo "<div class='test pass'>";
echo "<p class='status'>✓ PASS</p>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Session Extension: " . (extension_loaded('session') ? '✓ Loaded' : '✗ Missing') . "</p>";
echo "<p>PDO Extension: " . (extension_loaded('pdo') ? '✓ Loaded' : '✗ Missing') . "</p>";
echo "<p>PDO MySQL: " . (extension_loaded('pdo_mysql') ? '✓ Loaded' : '✗ Missing') . "</p>";
echo "</div>";

// TEST 2: Database Connection
echo "<h2>TEST 2: Database Connection</h2>";
try {
    require 'db.php';
    echo "<div class='test pass'>";
    echo "<p class='status'>✓ PASS</p>";
    echo "<p>Database connected successfully</p>";
    echo "<p>Connection type: " . get_class($pdo) . "</p>";
    $results['db'] = true;
    echo "</div>";
} catch (Exception $e) {
    echo "<div class='test fail'>";
    echo "<p class='status'>✗ FAIL</p>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    $results['db'] = false;
    echo "</div>";
    die("<p style='color: red; font-size: 20px;'>❌ CRITICAL: Cannot continue without database</p>");
}

// TEST 3: Users Table
echo "<h2>TEST 3: Users Table Exists</h2>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    if ($count > 0) {
        echo "<div class='test pass'>";
        echo "<p class='status'>✓ PASS</p>";
        echo "<p>Users table exists with {$count} users</p>";
        $results['users_table'] = true;
    } else {
        echo "<div class='test warn'>";
        echo "<p class='status'>⚠ WARNING</p>";
        echo "<p>Users table exists but is EMPTY</p>";
        $results['users_table'] = false;
    }
    echo "</div>";
} catch (Exception $e) {
    echo "<div class='test fail'>";
    echo "<p class='status'>✗ FAIL</p>";
    echo "<p>Users table does NOT exist</p>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    $results['users_table'] = false;
    echo "</div>";
}

// TEST 4: Show Users
if ($results['users_table']) {
    echo "<h2>TEST 4: Users in Database</h2>";
    $stmt = $pdo->query("SELECT id, username, password, LENGTH(password) as pwd_len FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<div class='test pass'>";
    echo "<p class='status'>✓ DATA</p>";
    echo "<table style='width:100%; border-collapse: collapse;'>";
    echo "<tr style='background: #3e3e42;'><th style='padding:8px; text-align:left;'>ID</th><th style='padding:8px; text-align:left;'>Username</th><th style='padding:8px; text-align:left;'>Password (visible)</th><th style='padding:8px; text-align:left;'>Length</th></tr>";
    foreach ($users as $u) {
        echo "<tr style='border-top: 1px solid #3e3e42;'>";
        echo "<td style='padding:8px;'>{$u['id']}</td>";
        echo "<td style='padding:8px;'><strong>{$u['username']}</strong></td>";
        echo "<td style='padding:8px;'><code style='background:#3e3e42;padding:4px;'>{$u['password']}</code></td>";
        echo "<td style='padding:8px;'>{$u['pwd_len']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
}

// TEST 5: Session Configuration
echo "<h2>TEST 5: Session Configuration</h2>";
echo "<div class='test'>";
$save_path = session_save_path();
if (empty($save_path)) {
    $save_path = sys_get_temp_dir();
}
echo "<p>Save Path: <code>{$save_path}</code></p>";
echo "<p>Path Exists: " . (file_exists($save_path) ? '✓ Yes' : '✗ No') . "</p>";
echo "<p>Path Writable: " . (is_writable($save_path) ? '✓ Yes' : '✗ No') . "</p>";

if (is_writable($save_path)) {
    echo "<p class='status' style='color: #4ec9b0;'>✓ PASS</p>";
    $results['session_writable'] = true;
} else {
    echo "<p class='status' style='color: #f48771;'>✗ FAIL - Session files cannot be written!</p>";
    $results['session_writable'] = false;
}
echo "</div>";

// TEST 6: Session Functionality
echo "<h2>TEST 6: Session Actually Works</h2>";
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/OnlineCourseManagementSystem',
]);
session_start();

if (!isset($_SESSION['test_counter'])) {
    $_SESSION['test_counter'] = 0;
}
$_SESSION['test_counter']++;

echo "<div class='test'>";
echo "<p>Session ID: <code>" . session_id() . "</code></p>";
echo "<p>Test Counter: <strong>{$_SESSION['test_counter']}</strong></p>";

if ($_SESSION['test_counter'] == 1) {
    echo "<p class='status' style='color: #dcdcaa;'>⚠ FIRST VISIT</p>";
    echo "<p><a href='find_root_cause.php' style='color: #4ec9b0;'>Click here to test if session persists</a></p>";
    $results['session_works'] = 'unknown';
} else {
    echo "<p class='status' style='color: #4ec9b0;'>✓ PASS - Session persists! (Visit #{$_SESSION['test_counter']})</p>";
    $results['session_works'] = true;
}
echo "</div>";

// TEST 7: Login Simulation
echo "<h2>TEST 7: Login Simulation</h2>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    echo "<div class='test'>";
    echo "<p>Username: <strong>" . htmlspecialchars($username) . "</strong></p>";
    echo "<p>Password Length: {$_POST['password']} characters</p>";
    
    // Query database
    $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo "<p class='status' style='color: #f48771;'>✗ FAIL - User not found</p>";
        echo "<p>The username '{$username}' does not exist in the database</p>";
    } else {
        echo "<p style='color: #4ec9b0;'>✓ User found in database</p>";
        echo "<p>DB Password: <code style='background:#3e3e42;padding:4px;'>{$user['password']}</code> ({$_POST['password']} chars)</p>";
        echo "<p>Input Password: <code style='background:#3e3e42;padding:4px;'>{$password}</code> (" . strlen($password) . " chars)</p>";
        
        // Test exact match
        $exact_match = ($user['password'] === $password);
        $trimmed_match = (trim($user['password']) === $password);
        
        echo "<p>Exact Match: " . ($exact_match ? '✓ Yes' : '✗ No') . "</p>";
        echo "<p>Trimmed Match: " . ($trimmed_match ? '✓ Yes' : '✗ No') . "</p>";
        
        if ($trimmed_match) {
            echo "<p class='status' style='color: #4ec9b0;'>✓ PASS - Password correct!</p>";
            
            // Set session
            $_SESSION['authenticated'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['last_activity'] = time();
            
            echo "<p style='color: #4ec9b0; font-size: 18px;'>✓✓✓ SESSION SET - You are now logged in</p>";
            echo "<p>Session data:</p>";
            echo "<pre>";
            print_r($_SESSION);
            echo "</pre>";
            
            echo "<p style='margin-top: 20px;'><a href='index.php' style='background: #4ec9b0; color: #1e1e1e; padding: 15px 30px; text-decoration: none; font-size: 18px; border-radius: 5px; display: inline-block;'>GO TO INDEX.PHP</a></p>";
        } else {
            echo "<p class='status' style='color: #f48771;'>✗ FAIL - Password incorrect</p>";
            
            // Show byte-by-byte comparison
            echo "<p>Byte comparison:</p>";
            echo "<pre>";
            echo "DB  Password bytes: ";
            for ($i = 0; $i < strlen($user['password']); $i++) {
                printf("%02X ", ord($user['password'][$i]));
            }
            echo "\nYour Password bytes: ";
            for ($i = 0; $i < strlen($password); $i++) {
                printf("%02X ", ord($password[$i]));
            }
            echo "</pre>";
        }
    }
    echo "</div>";
}

// Show login form
echo "<h2>TEST 8: Try Login</h2>";
echo "<div class='test'>";
echo "<form method='POST'>";
echo "<p><input type='text' name='username' placeholder='Username' required style='padding: 10px; width: 300px; background: #3e3e42; border: 1px solid #007acc; color: #d4d4d4;'></p>";
echo "<p><input type='password' name='password' placeholder='Password' required style='padding: 10px; width: 300px; background: #3e3e42; border: 1px solid #007acc; color: #d4d4d4;'></p>";
echo "<p><button type='submit' style='padding: 12px 30px; background: #007acc; color: white; border: none; cursor: pointer; font-size: 16px;'>TEST LOGIN</button></p>";
echo "</form>";
echo "</div>";

// SUMMARY
echo "<hr>";
echo "<h2>📊 DIAGNOSTIC SUMMARY</h2>";
echo "<div class='test'>";
echo "<table style='width: 100%;'>";
echo "<tr><td style='padding: 8px;'>Database Connection:</td><td style='padding: 8px;'>" . ($results['db'] ? '✓ Working' : '✗ Failed') . "</td></tr>";
echo "<tr><td style='padding: 8px;'>Users Table:</td><td style='padding: 8px;'>" . ($results['users_table'] ? '✓ Exists' : '✗ Missing/Empty') . "</td></tr>";
echo "<tr><td style='padding: 8px;'>Session Writable:</td><td style='padding: 8px;'>" . ($results['session_writable'] ? '✓ Yes' : '✗ No') . "</td></tr>";
echo "<tr><td style='padding: 8px;'>Session Works:</td><td style='padding: 8px;'>" . ($results['session_works'] === true ? '✓ Yes' : ($results['session_works'] === 'unknown' ? '⚠ Test needed' : '✗ No')) . "</td></tr>";
echo "</table>";
echo "</div>";

echo "<hr>";
echo "<p><a href='login.php' style='color: #569cd6;'>→ Back to login.php</a></p>";
?>

</body>
</html>

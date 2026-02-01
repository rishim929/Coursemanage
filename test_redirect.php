<!DOCTYPE html>
<html>
<head>
    <title>Session Redirect Test</title>
</head>
<body>
    <h1>Testing Session Redirect</h1>
    <?php
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
    
    echo "<h2>After Login (Simulated):</h2>";
    
    if (!isset($_SESSION['test_login_done'])) {
        // Simulate login
        $_SESSION['authenticated'] = true;
        $_SESSION['user_id'] = 999;
        $_SESSION['username'] = 'testuser';
        $_SESSION['last_activity'] = time();
        $_SESSION['test_login_done'] = true;
        
        session_write_close();
        session_start();
        
        echo "<p style='color: green;'>✓ Session variables set</p>";
        echo "<p>Session ID: " . session_id() . "</p>";
        echo "<pre>";
        print_r($_SESSION);
        echo "</pre>";
        
        echo "<p><a href='test_redirect.php' style='background: blue; color: white; padding: 10px 20px; text-decoration: none; display: inline-block;'>Test Redirect to Same Page</a></p>";
    } else {
        echo "<p style='color: green; font-size: 18px;'>✓✓ Session PERSISTED after redirect!</p>";
        echo "<p>Session ID: " . session_id() . "</p>";
        echo "<pre>";
        print_r($_SESSION);
        echo "</pre>";
        
        if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
            echo "<p style='color: green; font-weight: bold;'>✓✓✓ AUTHENTICATED = TRUE</p>";
            echo "<p>Now try accessing index.php:</p>";
            echo "<p><a href='index.php?debug=1' style='background: green; color: white; padding: 10px 20px; text-decoration: none; display: inline-block;'>GO TO INDEX.PHP</a></p>";
        }
        
        echo "<hr>";
        echo "<p><a href='?reset=1'>Reset Test</a></p>";
    }
    
    if (isset($_GET['reset'])) {
        session_destroy();
        header("Location: test_redirect.php");
        exit;
    }
    ?>
</body>
</html>

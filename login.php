<?php
// Start output buffering to prevent header issues
ob_start();

// Set cookie params to match other pages
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/OnlineCourseManagementSystem',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// If user is already logged in, redirect to home
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    ob_end_clean();
    header("Location: index.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'db.php';
    
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    // Temporary debugging - remove after testing
    $debug_info = [];
    $debug_info['username_entered'] = $username;
    $debug_info['password_length'] = strlen($password);
    
    if (empty($username) || empty($password)) {
        $error = 'Username and password are required';
    } else {
        try {
            // Query user from database
            $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Debugging
            $debug_info['user_found'] = $user ? 'YES' : 'NO';
            if ($user) {
                $debug_info['db_password_length'] = strlen($user['password']);
                $debug_info['passwords_match'] = (trim($user['password']) === $password) ? 'YES' : 'NO';
            }
            
            // Check if user exists and password matches - trim database password too
            if ($user && trim($user['password']) === $password) {
                // Regenerate session ID first for security (before setting sensitive data)
                session_regenerate_id(true);
                
                // Now set session variables in the new session
                $_SESSION['authenticated'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['login_time'] = time();
                $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
                $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
                $_SESSION['last_activity'] = time();
                
                // Force session save
                session_write_close();
                session_start();
                
                // Verify session was saved (temporary debug)
                if ($_SESSION['authenticated'] === true) {
                    // Clear output buffer and redirect
                    ob_end_clean();
                    header("Location: index.php");
                    exit;
                } else {
                    $error = 'Session failed to save - please contact administrator';
                }
            } else {
                $error = 'Invalid username or password';
                // Show debug info
                $error .= '<br><small>Debug: User found: ' . ($user ? 'Yes' : 'No');
                if ($user) {
                    $error .= ' | DB pwd len: ' . strlen($user['password']) . ' | Input pwd len: ' . strlen($password);
                    $error .= ' | Match: ' . ((trim($user['password']) === $password) ? 'Yes' : 'No');
                }
                $error .= '</small>';
            }
        } catch (Exception $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Online Course Management System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 5px;
        }
        
        .login-header p {
            color: #666;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
        
        .login-button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .login-button:active {
            transform: translateY(0);
        }
        
        .demo-credentials {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 13px;
            color: #666;
        }
        
        .demo-credentials h4 {
            margin-bottom: 10px;
            color: #333;
        }
        
        .demo-credentials p {
            margin: 5px 0;
        }
        
        .demo-credentials code {
            background-color: #f5f5f5;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Course Management</h1>
            <p>Online Course Management System</p>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    required 
                    placeholder="Enter your username"
                    autofocus
                >
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required 
                    placeholder="Enter your password"
                >
            </div>
            
            <button type="submit" class="login-button">Login</button>
        </form>
    </div>
</body>
</html>

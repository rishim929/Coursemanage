<?php
/**
 * Session Debug Script - Traces session data across page loads
 */

// Use same cookie params as login.php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/OnlineCourseManagementSystem',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

$debug_info = [
    'timestamp' => date('Y-m-d H:i:s'),
    'session_id' => session_id(),
    'session_name' => session_name(),
    'phpsessid_cookie' => $_COOKIE[session_name()] ?? 'NOT SET',
    'session_file' => session_save_path() . '/' . 'sess_' . session_id(),
    'session_file_exists' => file_exists(session_save_path() . '/' . 'sess_' . session_id()),
    'session_status' => session_status(),
    'session_data' => $_SESSION,
    'remote_addr' => $_SERVER['REMOTE_ADDR'],
    'http_user_agent' => substr($_SERVER['HTTP_USER_AGENT'], 0, 50),
    'request_method' => $_SERVER['REQUEST_METHOD'],
    'current_url' => $_SERVER['REQUEST_URI'],
];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Session Debug</title>
    <style>
        body { font-family: monospace; margin: 20px; background: #f5f5f5; }
        .container { background: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .debug-box { background: #f9f9f9; padding: 15px; margin: 10px 0; border-left: 4px solid #4CAF50; }
        .label { font-weight: bold; color: #333; }
        .value { color: #666; word-break: break-all; }
        .status-ok { color: green; }
        .status-error { color: red; }
        pre { background: #f0f0f0; padding: 10px; border-radius: 4px; overflow-x: auto; }
        a { display: inline-block; margin-top: 20px; padding: 10px 15px; background: #4CAF50; color: white; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Session Debug Information</h1>
        
        <div class="debug-box">
            <div class="label">Session ID:</div>
            <div class="value <?php echo session_id() ? 'status-ok' : 'status-error'; ?>">
                <?php echo session_id() ?: 'NO SESSION ID'; ?>
            </div>
        </div>

        <div class="debug-box">
            <div class="label">PHPSESSID Cookie:</div>
            <div class="value <?php echo isset($_COOKIE[session_name()]) ? 'status-ok' : 'status-error'; ?>">
                <?php echo $_COOKIE[session_name()] ?? 'NOT SET IN COOKIE'; ?>
            </div>
        </div>

        <div class="debug-box">
            <div class="label">Session File Path:</div>
            <div class="value"><?php echo $debug_info['session_file']; ?></div>
            <div class="value <?php echo $debug_info['session_file_exists'] ? 'status-ok' : 'status-error'; ?>">
                File Exists: <?php echo $debug_info['session_file_exists'] ? 'YES ✓' : 'NO ✗'; ?>
            </div>
        </div>

        <div class="debug-box">
            <div class="label">Authenticated:</div>
            <div class="value <?php echo isset($_SESSION['authenticated']) && $_SESSION['authenticated'] ? 'status-ok' : 'status-error'; ?>">
                <?php echo isset($_SESSION['authenticated']) && $_SESSION['authenticated'] ? 'YES ✓' : 'NO ✗'; ?>
            </div>
        </div>

        <div class="debug-box">
            <div class="label">Last Activity:</div>
            <div class="value">
                <?php 
                if (isset($_SESSION['last_activity'])) {
                    $ago = time() - $_SESSION['last_activity'];
                    echo $_SESSION['last_activity'] . " (" . $ago . " seconds ago)";
                } else {
                    echo "NOT SET";
                }
                ?>
            </div>
        </div>

        <div class="debug-box">
            <div class="label">Remote Address:</div>
            <div class="value"><?php echo $_SERVER['REMOTE_ADDR']; ?></div>
            <div class="value">
                Stored: <?php echo $_SESSION['user_ip'] ?? 'NOT SET'; ?>
                <?php if (isset($_SESSION['user_ip']) && $_SESSION['user_ip'] === $_SERVER['REMOTE_ADDR']) { ?>
                    <span class="status-ok">✓ MATCH</span>
                <?php } elseif (isset($_SESSION['user_ip'])) { ?>
                    <span class="status-error">✗ MISMATCH</span>
                <?php } ?>
            </div>
        </div>

        <div class="debug-box">
            <div class="label">Full Session Data:</div>
            <pre><?php echo json_encode($_SESSION, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); ?></pre>
        </div>

        <div class="debug-box">
            <div class="label">Request Method:</div>
            <div class="value"><?php echo $_SERVER['REQUEST_METHOD']; ?></div>
        </div>

        <div class="debug-box">
            <div class="label">Current URL:</div>
            <div class="value"><?php echo $_SERVER['REQUEST_URI']; ?></div>
        </div>

        <a href="index.php">Back to Courses</a>
        <a href="add.php">Test: Go to Add Course</a>
        <a href="session_debug.php?refresh=1">Refresh Debug Info</a>
    </div>
</body>
</html>

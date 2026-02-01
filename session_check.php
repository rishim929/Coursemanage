<?php
session_start();

// Check if user is authenticated
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header("Location: login.php");
    exit;
}

// Verify session hasn't been hijacked - only check IP
if (isset($_SESSION['user_ip'])) {
    // Allow some flexibility with IP checking (in case of proxy or network changes)
    $current_ip = $_SERVER['REMOTE_ADDR'];
    if ($current_ip != $_SESSION['user_ip']) {
        // Log out only if IP is completely different, not just different part
        session_destroy();
        header("Location: login.php");
        exit;
    }
}

// Session timeout (30 minutes)
$timeout = 1800;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    session_destroy();
    header("Location: login.php?timeout=1");
    exit;
}

$_SESSION['last_activity'] = time();
?>

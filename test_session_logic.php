<?php
// Simple test of session validation logic

echo "=== Session Validation Test ===\n\n";

// Test 1: Authentication check
echo "Test 1: Authentication check\n";
$_SESSION = ['authenticated' => true, 'last_activity' => time()];
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    echo "  FAIL: Should be authenticated\n";
} else {
    echo "  PASS: Authenticated correctly\n";
}

// Test 2: Timeout check (should pass - just started)
echo "\nTest 2: Timeout check (fresh session)\n";
$timeout = 1800;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    echo "  FAIL: Should not timeout fresh session\n";
} else {
    echo "  PASS: Session not timed out\n";
}

// Test 3: Timeout check (should fail - 31 minutes)
echo "\nTest 3: Timeout check (31 minutes old)\n";
$_SESSION['last_activity'] = time() - 1860; // 31 minutes ago
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    echo "  PASS: Session correctly detected as timed out\n";
} else {
    echo "  FAIL: Should have detected timeout\n";
}

// Test 4: Not authenticated
echo "\nTest 4: Not authenticated check\n";
$_SESSION = ['authenticated' => false];
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    echo "  PASS: Correctly rejected non-authenticated session\n";
} else {
    echo "  FAIL: Should reject non-authenticated\n";
}

echo "\n=== All tests complete ===\n";
?>

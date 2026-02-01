<?php
/**
 * Course Management Add Error Diagnostic
 * This script helps identify why adding courses isn't working
 */

// No session needed for diagnostics - just checking system state
echo "<h1>📋 Course Management - Add Course Diagnostic</h1>";
echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
.container { background: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
.check { margin: 15px 0; padding: 15px; border-left: 4px solid #4CAF50; background: #f0f8f0; }
.error { border-left-color: #dc3545; background: #ffcccc; }
.warning { border-left-color: #ffc107; background: #fffacd; }
.ok { border-left-color: #4CAF50; }
.title { font-weight: bold; font-size: 16px; margin-bottom: 5px; }
.detail { font-size: 13px; color: #666; margin-top: 5px; }
code { background: #f0f0f0; padding: 2px 6px; border-radius: 3px; }
table { width: 100%; border-collapse: collapse; margin-top: 10px; }
th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
th { background: #4CAF50; color: white; }
.icon-ok { color: green; font-weight: bold; }
.icon-err { color: red; font-weight: bold; }
a { display: inline-block; margin-top: 10px; padding: 10px 15px; background: #4CAF50; color: white; text-decoration: none; border-radius: 4px; }
</style>";

echo "<div class='container'>";

// Check 1: Database Connection
echo "<div class='check ok'>";
echo "<div class='title'><span class='icon-ok'>✓</span> 1. Database Connection</div>";
try {
    $host = "localhost";
    $dbname = "np02cs4a240094";
    $user = "np02cs4a240094";
    $pass = "cTed1NUw6x";
    
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "<div class='detail'><span class='icon-ok'>✓</span> Database connection: SUCCESS</div>";
    echo "<div class='detail'>Host: $host | Database: $dbname</div>";
} catch (PDOException $e) {
    echo "<div class='check error'>";
    echo "<div class='title'><span class='icon-err'>✗</span> Database Connection FAILED</div>";
    echo "<div class='detail'><strong>Error:</strong> " . $e->getMessage() . "</div>";
    echo "<div class='detail'><strong>Solution:</strong> Check if MySQL is running and credentials are correct in <code>db.php</code></div>";
    echo "</div>";
    die("Cannot continue without database connection.");
}
echo "</div>";

// Check 2: Courses Table
echo "<div class='check'>";
try {
    $result = $pdo->query("SELECT COUNT(*) FROM courses");
    $count = $result->fetchColumn();
    echo "<div class='check ok'>";
    echo "<div class='title'><span class='icon-ok'>✓</span> 2. Courses Table</div>";
    echo "<div class='detail'><span class='icon-ok'>✓</span> Table exists with $count courses</div>";
} catch (PDOException $e) {
    echo "<div class='check error'>";
    echo "<div class='title'><span class='icon-err'>✗</span> Courses Table NOT FOUND</div>";
    echo "<div class='detail'><strong>Error:</strong> " . $e->getMessage() . "</div>";
    echo "<div class='detail'><strong>Solution:</strong> Create the courses table using this SQL:</div>";
    echo "<pre style='background: #f0f0f0; padding: 10px;'>
CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(100),
    level VARCHAR(50),
    instructor_id INT,
    FOREIGN KEY (instructor_id) REFERENCES instructors(id)
);
</pre>";
}
echo "</div>";

// Check 3: Instructors Table and Count
echo "<div class='check'>";
try {
    $result = $pdo->query("SELECT COUNT(*) FROM instructors");
    $inst_count = $result->fetchColumn();
    
    if ($inst_count == 0) {
        echo "<div class='check warning'>";
        echo "<div class='title'><span class='icon-err'>⚠</span> 3. Instructors Table - EMPTY</div>";
        echo "<div class='detail'><span class='icon-err'>✗</span> No instructors found!</div>";
        echo "<div class='detail'><strong>Problem:</strong> You cannot add a course without an instructor.</div>";
        echo "<div class='detail'><strong>Solution:</strong> <a href='add_instructor.php' style='display:inline; padding: 5px 10px; font-size: 12px;'>Add Instructor First</a></div>";
    } else {
        echo "<div class='check ok'>";
        echo "<div class='title'><span class='icon-ok'>✓</span> 3. Instructors Table</div>";
        echo "<div class='detail'><span class='icon-ok'>✓</span> Table exists with $inst_count instructor(s)</div>";
    }
} catch (PDOException $e) {
    echo "<div class='check error'>";
    echo "<div class='title'><span class='icon-err'>✗</span> Instructors Table NOT FOUND</div>";
    echo "<div class='detail'><strong>Error:</strong> " . $e->getMessage() . "</div>";
}
echo "</div>";

// Check 4: Courses Table Structure
echo "<div class='check ok'>";
echo "<div class='title'><span class='icon-ok'>✓</span> 4. Courses Table Structure</div>";
try {
    $result = $pdo->query("DESCRIBE courses");
    $columns = $result->fetchAll(PDO::FETCH_ASSOC);
    echo "<table>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td><code>" . $col['Field'] . "</code></td>";
        echo "<td>" . $col['Type'] . "</td>";
        echo "<td>" . $col['Null'] . "</td>";
        echo "<td>" . $col['Key'] . "</td>";
        echo "<td>" . ($col['Default'] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (PDOException $e) {
    echo "<div class='detail' style='color: red;'><strong>Error:</strong> " . $e->getMessage() . "</div>";
}
echo "</div>";

// Check 5: Test INSERT
echo "<div class='check'>";
echo "<div class='title'><span class='icon-ok'>✓</span> 5. Test INSERT Operation</div>";
try {
    // Get first instructor
    $stmt = $pdo->query("SELECT id, name FROM instructors LIMIT 1");
    $instructor = $stmt->fetch();
    
    if (!$instructor) {
        echo "<div class='detail warning'><span class='icon-err'>⚠</span> No instructor available for test</div>";
    } else {
        // Try to insert
        $test_title = "Test Course " . date('H:i:s');
        $stmt = $pdo->prepare("INSERT INTO courses (title, category, level, instructor_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$test_title, 'Test', 'Beginner', $instructor['id']]);
        
        echo "<div class='check ok'>";
        echo "<div class='detail'><span class='icon-ok'>✓</span> INSERT test: SUCCESS</div>";
        echo "<div class='detail'>Test course '<code>$test_title</code>' inserted successfully</div>";
        echo "<div class='detail'>Course ID: " . $pdo->lastInsertId() . " | Instructor: " . $instructor['name'] . "</div>";
        echo "</div>";
    }
} catch (PDOException $e) {
    echo "<div class='check error'>";
    echo "<div class='detail'><span class='icon-err'>✗</span> INSERT test FAILED</div>";
    echo "<div class='detail'><strong>Error:</strong> " . $e->getMessage() . "</div>";
    echo "<div class='detail'><strong>Possible causes:</strong>";
    echo "<ul>";
    echo "<li>Foreign key constraint violated (instructor doesn't exist)</li>";
    echo "<li>Missing columns in table</li>";
    echo "<li>Database permissions issue</li>";
    echo "</ul></div>";
    echo "</div>";
}
echo "</div>";

// Check 6: Session Setup
echo "<div class='check ok'>";
echo "<div class='title'><span class='icon-ok'>✓</span> 6. Session Management</div>";
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/OnlineCourseManagementSystem',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    echo "<div class='detail'><span class='icon-ok'>✓</span> Session status: AUTHENTICATED</div>";
    echo "<div class='detail'>Username: " . ($_SESSION['username'] ?? 'Unknown') . "</div>";
    echo "<div class='detail'>Session ID: " . session_id() . "</div>";
} else {
    echo "<div class='check warning'>";
    echo "<div class='detail'><span class='icon-err'>⚠</span> Not authenticated - Log in first</div>";
    echo "<div class='detail'><a href='login.php' style='display:inline; padding: 5px 10px; font-size: 12px;'>Go to Login</a></div>";
}
echo "</div>";

// Check 7: Form Submission
echo "<div class='check ok'>";
echo "<div class='title'><span class='icon-ok'>✓</span> 7. Testing Course Add Form</div>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_add'])) {
    $title = trim($_POST['test_title'] ?? '');
    $category = trim($_POST['test_category'] ?? '');
    $level = trim($_POST['test_level'] ?? '');
    $instructor = trim($_POST['test_instructor'] ?? '');
    
    if (empty($title) || empty($category) || empty($level) || empty($instructor)) {
        echo "<div class='detail' style='color: red;'>Please fill in all fields</div>";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO courses (title, category, level, instructor_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $category, $level, $instructor]);
            echo "<div class='check ok'>";
            echo "<div class='detail'><span class='icon-ok'>✓</span> Course added successfully!</div>";
            echo "<div class='detail'>Title: <code>$title</code></div>";
            echo "<div class='detail'>Category: <code>$category</code> | Level: <code>$level</code></div>";
            echo "<div class='detail'><a href='index.php' style='display:inline; padding: 5px 10px; font-size: 12px;'>Go to Courses</a></div>";
            echo "</div>";
        } catch (PDOException $e) {
            echo "<div class='check error'>";
            echo "<div class='detail'><span class='icon-err'>✗</span> Error: " . $e->getMessage() . "</div>";
            echo "</div>";
        }
    }
}

// Get instructors for dropdown
$instructors_list = [];
try {
    $stmt = $pdo->query("SELECT id, name FROM instructors ORDER BY name");
    $instructors_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Silent fail
}

echo "<form method='POST' style='margin-top: 15px;'>";
echo "<input type='hidden' name='test_add' value='1'>";
echo "<input type='text' name='test_title' placeholder='Course Title' required style='padding: 8px; margin-right: 10px; width: 150px;'>";
echo "<input type='text' name='test_category' placeholder='Category' required style='padding: 8px; margin-right: 10px; width: 120px;'>";
echo "<select name='test_level' required style='padding: 8px; margin-right: 10px;'>";
echo "<option value=''>Select Level</option>";
echo "<option value='Beginner'>Beginner</option>";
echo "<option value='Intermediate'>Intermediate</option>";
echo "<option value='Advanced'>Advanced</option>";
echo "</select>";
echo "<select name='test_instructor' required style='padding: 8px; margin-right: 10px;'>";
echo "<option value=''>Select Instructor</option>";
foreach ($instructors_list as $inst) {
    echo "<option value='" . $inst['id'] . "'>" . htmlspecialchars($inst['name']) . "</option>";
}
echo "</select>";
echo "<button type='submit' style='padding: 8px 15px; background: #4CAF50; color: white; border: none; cursor: pointer; border-radius: 4px;'>Test Add</button>";
echo "</form>";

echo "</div>";

echo "</div><!-- end container -->";
echo "<hr>";
echo "<p><a href='index.php'>Back to Course Management</a> | <a href='login.php'>Back to Login</a></p>";
?>

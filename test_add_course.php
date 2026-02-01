<?php
// Test database connection and courses table
$host = "localhost";
$dbname = "np02cs4a240094";
$user = "np02cs4a240094";
$pass = "cTed1NUw6x";

echo "<h1>Database Connection Test</h1>";
echo "<style>body { font-family: Arial; margin: 20px; }</style>";

// Test 1: Connection
try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "✓ <strong>Database connection:</strong> SUCCESS<br>";
} catch (PDOException $e) {
    echo "✗ <strong>Database connection FAILED:</strong><br>";
    echo "<pre style='background: #ffcccc; padding: 10px; border: 1px solid red;'>";
    echo $e->getMessage();
    echo "</pre>";
    exit;
}

// Test 2: Check if courses table exists
try {
    $result = $pdo->query("SELECT COUNT(*) FROM courses");
    $count = $result->fetchColumn();
    echo "✓ <strong>Courses table:</strong> EXISTS with $count rows<br>";
} catch (PDOException $e) {
    echo "✗ <strong>Courses table:</strong> NOT FOUND<br>";
    echo "<pre style='background: #ffcccc; padding: 10px; border: 1px solid red;'>";
    echo $e->getMessage();
    echo "</pre>";
}

// Test 3: Check instructors table
try {
    $result = $pdo->query("SELECT COUNT(*) FROM instructors");
    $count = $result->fetchColumn();
    echo "✓ <strong>Instructors table:</strong> EXISTS with $count rows<br>";
} catch (PDOException $e) {
    echo "✗ <strong>Instructors table:</strong> NOT FOUND<br>";
    echo "<pre style='background: #ffcccc; padding: 10px; border: 1px solid red;'>";
    echo $e->getMessage();
    echo "</pre>";
}

// Test 4: Check courses table structure
try {
    $result = $pdo->query("DESCRIBE courses");
    $columns = $result->fetchAll(PDO::FETCH_ASSOC);
    echo "✓ <strong>Courses table structure:</strong><br>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td>" . $col['Field'] . "</td>";
        echo "<td>" . $col['Type'] . "</td>";
        echo "<td>" . $col['Null'] . "</td>";
        echo "<td>" . $col['Key'] . "</td>";
        echo "</tr>";
    }
    echo "</table><br>";
} catch (PDOException $e) {
    echo "✗ <strong>Error describing courses table:</strong><br>";
    echo "<pre style='background: #ffcccc; padding: 10px; border: 1px solid red;'>";
    echo $e->getMessage();
    echo "</pre>";
}

// Test 5: Try to insert a test course
try {
    echo "<h2>Testing INSERT Operation</h2>";
    
    // First get an instructor
    $stmt = $pdo->query("SELECT id FROM instructors LIMIT 1");
    $instructor = $stmt->fetch();
    
    if (!$instructor) {
        echo "✗ <strong>No instructors found!</strong> Add an instructor first before adding courses.<br>";
    } else {
        $instructor_id = $instructor['id'];
        echo "✓ Found instructor with ID: $instructor_id<br>";
        
        // Try insert
        $stmt = $pdo->prepare("INSERT INTO courses (title, category, level, instructor_id) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Test Course ' . time(), 'Test Category', 'Beginner', $instructor_id]);
        
        echo "✓ <strong>INSERT test:</strong> SUCCESS<br>";
        echo "Last inserted course ID: " . $pdo->lastInsertId() . "<br>";
    }
} catch (PDOException $e) {
    echo "✗ <strong>INSERT test FAILED:</strong><br>";
    echo "<pre style='background: #ffcccc; padding: 10px; border: 1px solid red;'>";
    echo $e->getMessage();
    echo "</pre>";
}

echo "<hr>";
echo "<p><a href='index.php'>Back to Course Management</a></p>";
?>

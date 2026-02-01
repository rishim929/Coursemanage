<?php
// Set cookie params to match login.php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/OnlineCourseManagementSystem',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax',
]);
// Check session at the very start
session_start();
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header("Location: login.php");
    exit;
}

// Check session timeout (30 minutes)
$timeout = 1800;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    session_destroy();
    header("Location: login.php?timeout=1");
    exit;
}

// Update last activity
$_SESSION['last_activity'] = time();

require "db.php";

$q = "%".$_GET['q']."%";
$stmt = $pdo->prepare("
    SELECT courses.*, instructors.name AS instructor
    FROM courses
    LEFT JOIN instructors ON courses.instructor_id = instructors.id
    WHERE courses.category LIKE ? OR courses.level LIKE ? OR instructors.name LIKE ?
");
$stmt->execute([$q, $q, $q]);

while ($row = $stmt->fetch()) {
    echo "<tr>
    <td>".htmlspecialchars($row['title'])."</td>
    <td>".htmlspecialchars($row['category'])."</td>
    <td>".htmlspecialchars($row['level'])."</td>
    <td>".htmlspecialchars($row['instructor'])."</td>
    <td>
      <a href='edit.php?id=". $row['id'] ."'>Edit</a>
      <a href='delete.php?id=". $row['id'] ."' onclick='return confirm(\"Delete this course?\")'>Delete</a>
    </td>
    </tr>";
}
?>

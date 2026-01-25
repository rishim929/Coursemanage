<!DOCTYPE html>
<html>
<head><title>Test DB</title></head>
<body>
<?php
require "db.php";
echo "Connection OK<br>";
$query = $pdo->query("SELECT COUNT(*) FROM courses");
$count = $query->fetchColumn();
echo "Courses count: $count<br>";
?>
</body>
</html>
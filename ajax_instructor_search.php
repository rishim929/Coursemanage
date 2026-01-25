<?php
require "db.php";

$q = "%".$_GET['q']."%";
$stmt = $pdo->prepare("SELECT id, name FROM instructors WHERE name LIKE ? LIMIT 10");
$stmt->execute([$q]);

while ($row = $stmt->fetch()) {
    echo "<div onclick=\"selectInstructor(". $row['id'] .", '". htmlspecialchars($row['name']) ."')\">". htmlspecialchars($row['name']) ."</div>";
}
?>
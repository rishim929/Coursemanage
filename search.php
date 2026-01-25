<?php
require "db.php";

$keyword = "%".$_GET['q']."%";

$stmt = $pdo->prepare("
    SELECT * FROM courses
    WHERE title LIKE ? OR category LIKE ? OR level LIKE ?
");
$stmt->execute([$keyword, $keyword, $keyword]);

while ($row = $stmt->fetch()) {
    echo "<p>".htmlspecialchars($row['title'])."</p>";
}
?>

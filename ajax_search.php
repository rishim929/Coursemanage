<?php
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

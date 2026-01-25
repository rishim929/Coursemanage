<?php
require 'db.php';
try {
    $stmt = $pdo->query('SELECT * FROM instructors');
    $instructors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Instructors:\n";
    foreach ($instructors as $inst) {
        echo $inst['id'] . ': ' . $inst['name'] . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
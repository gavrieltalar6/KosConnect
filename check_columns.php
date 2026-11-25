<?php
require __DIR__.'/vendor/autoload.php';

$pdo = new PDO(
    'mysql:host=127.0.0.1;dbname=kosconnectAI;charset=utf8',
    'root',
    '',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$stmt = $pdo->query('DESCRIBE damage_reports');
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Columns in damage_reports table:\n";
foreach ($columns as $column) {
    echo "- {$column['Field']}\n";
}
?>
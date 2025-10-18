<?php
// Simple helper to list columns of the 'doctors' table in the project's sqlite DB
$dbPath = __DIR__ . '/../database/database.sqlite';
if (!file_exists($dbPath)) {
    echo "database file not found: $dbPath\n";
    exit(1);
}
try {
    $db = new PDO('sqlite:' . $dbPath);
    $stmt = $db->query("PRAGMA table_info('doctors')");
    $cols = [];
    foreach ($stmt as $row) {
        $cols[] = $row['name'];
    }
    echo implode("\n", $cols) . "\n";
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . "\n";
    exit(1);
}

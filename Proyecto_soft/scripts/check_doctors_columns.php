<?php
$db = __DIR__ . '/../database/database.sqlite';
if (!file_exists($db)) {
    echo "Database file not found: $db\n";
    exit(1);
}
$pdo = new PDO('sqlite:' . $db);
$cols = $pdo->query("PRAGMA table_info(doctors);")->fetchAll(PDO::FETCH_ASSOC);
if (!$cols) {
    echo "Table 'doctors' not found or no columns returned.\n";
    exit(1);
}
foreach ($cols as $c) {
    echo $c['cid'] . "\t" . $c['name'] . "\t" . $c['type'] . "\n";
}

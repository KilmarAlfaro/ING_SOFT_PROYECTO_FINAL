<?php
$db = __DIR__ . '/../database/database.sqlite';
$pdo = new PDO('sqlite:' . $db);
$cols = $pdo->query("PRAGMA table_info(users);")->fetchAll(PDO::FETCH_ASSOC);
if (!$cols) {
    echo "Table 'users' not found or no columns returned.\n";
    exit(1);
}
foreach ($cols as $c) {
    echo $c['cid'] . "\t" . $c['name'] . "\t" . $c['type'] . "\n";
}

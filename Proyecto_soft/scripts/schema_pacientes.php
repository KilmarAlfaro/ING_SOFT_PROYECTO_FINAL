<?php
$db = __DIR__ . '/../database/database.sqlite';
$pdo = new PDO('sqlite:' . $db);
$cols = $pdo->query("PRAGMA table_info(pacientes)")->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $c) {
    echo $c['cid'], "\t", $c['name'], "\t", $c['type'], "\n";
}

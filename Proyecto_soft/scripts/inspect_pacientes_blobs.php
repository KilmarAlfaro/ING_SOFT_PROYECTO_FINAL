<?php
$db = __DIR__ . '/../database/database.sqlite';
if (!file_exists($db)) {
    echo "DB not found: $db\n";
    exit(1);
}
$pdo = new PDO('sqlite:' . $db);
$sql = "SELECT id, length(foto_perfil_blob) AS len, foto_perfil_mime AS mime FROM pacientes ORDER BY id DESC LIMIT 10";
$rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    printf("id=%s\tlen=%s\tmime=%s\n", $r['id'], $r['len'] ?? 'NULL', $r['mime'] ?? 'NULL');
}

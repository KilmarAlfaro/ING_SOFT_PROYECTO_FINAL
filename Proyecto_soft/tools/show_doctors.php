<?php
$root = __DIR__ . '/../';
$dbFile = $root . 'database/database.sqlite';
if (!file_exists($dbFile)) {
    echo "MISSING_DB|" . $dbFile . PHP_EOL;
    exit(1);
}
try {
    $pdo = new PDO('sqlite:' . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->query('SELECT id, nombre, apellido, correo, sexo, created_at FROM doctors ORDER BY id DESC LIMIT 10');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!$rows) {
        echo "NO_ROWS" . PHP_EOL;
        exit(0);
    }
    foreach ($rows as $r) {
        echo implode('|', [$r['id'], $r['nombre'], $r['apellido'], $r['correo'], $r['sexo'], $r['created_at']]) . PHP_EOL;
    }
} catch (Exception $e) {
    echo "ERROR|" . $e->getMessage() . PHP_EOL;
    exit(2);
}

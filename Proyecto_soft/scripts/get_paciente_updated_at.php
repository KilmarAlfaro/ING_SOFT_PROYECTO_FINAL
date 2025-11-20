<?php
$db = __DIR__ . '/../database/database.sqlite';
$pdo = new PDO('sqlite:' . $db);
$id = (int)($argv[1] ?? 11);
$row = $pdo->query("SELECT id, updated_at FROM pacientes WHERE id = $id")->fetch(PDO::FETCH_ASSOC);
var_export($row);

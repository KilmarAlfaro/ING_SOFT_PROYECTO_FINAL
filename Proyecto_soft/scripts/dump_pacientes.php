<?php
$db = __DIR__ . '/../database/database.sqlite';
$pdo = new PDO('sqlite:' . $db);
$sql = 'SELECT id, nombre, apellido, foto_perfil, length(foto_perfil_blob) AS len, foto_perfil_mime AS mime FROM pacientes ORDER BY id';
$rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    printf("id=%s\tname=%s %s\tfile=%s\tblob_len=%s\tmime=%s\n",
        $r['id'], $r['nombre'], $r['apellido'], $r['foto_perfil'] ?? 'NULL', $r['len'] ?? 'NULL', $r['mime'] ?? 'NULL');
}

<?php
$db = __DIR__ . '/../database/database.sqlite';
$pdo = new PDO('sqlite:' . $db);
$email = 'testdoctor@example.local';
// Check if users table has role column
$cols = $pdo->query("PRAGMA table_info(users);")->fetchAll(PDO::FETCH_ASSOC);
$hasRole = false;
foreach ($cols as $c) {
    if ($c['name'] === 'role') $hasRole = true;
}
if (!$hasRole) {
    echo "La tabla users no tiene columna 'role'.\n";
    exit(1);
}
// Update role for the test user
$stmt = $pdo->prepare('UPDATE users SET role = :role WHERE email = :email');
$stmt->execute([':role' => 'doctor', ':email' => $email]);
$affected = $stmt->rowCount();
if ($affected) {
    echo "Usuario $email actualizado a role=doctor (filas afectadas: $affected)\n";
} else {
    echo "No se actualizó ningún usuario para $email (quizá no existe).\n";
}
// List users
$rows = $pdo->query('SELECT id,name,email,role FROM users')->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo implode(' | ', [$r['id'],$r['name'],$r['email'],$r['role']]) . "\n";
}

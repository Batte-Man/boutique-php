<?php
/**
 * Crée ou met à jour un compte administrateur (à lancer en ligne de commande).
 *   php database/create_admin.php <login> <gerant|assistant>
 * Le mot de passe est demandé au clavier et stocké avec password_hash().
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
[$script, $login, $role] = $argv + [null, null, null];
if (!$login || !in_array($role, ['gerant', 'assistant'], true)) {
    fwrite(STDERR, "Usage : php database/create_admin.php <login> <gerant|assistant>\n");
    exit(1);
}
echo 'Mot de passe : ';
$password = trim((string) fgets(STDIN));
if (strlen($password) < 8) {
    fwrite(STDERR, "Le mot de passe doit faire au moins 8 caractères.\n");
    exit(1);
}

require __DIR__ . '/../db.php';
$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare(
    'INSERT INTO admin (login, mot_de_passe_hash, role) VALUES (?, ?, ?)
     ON DUPLICATE KEY UPDATE mot_de_passe_hash = VALUES(mot_de_passe_hash), role = VALUES(role)'
);
$stmt->bind_param('sss', $login, $hash, $role);
$stmt->execute();
echo "Compte « $login » ($role) enregistré.\n";

<?php
/**
 * Configuration de l'application.
 *
 * Les valeurs sont lues, dans l'ordre :
 *   1. dans config.local.php (non versionné, voir config.local.example.php) ;
 *   2. dans les variables d'environnement ;
 *   3. sinon, la valeur par défaut (développement local uniquement).
 * Aucun secret ne doit être écrit dans ce fichier.
 */

function config(string $key, ?string $default = null): ?string
{
    static $local = null;
    if ($local === null) {
        $file = __DIR__ . '/config.local.php';
        $local = is_file($file) ? (require $file) : [];
    }
    if (array_key_exists($key, $local)) {
        return (string) $local[$key];
    }
    $env = getenv($key);
    return $env !== false ? $env : $default;
}

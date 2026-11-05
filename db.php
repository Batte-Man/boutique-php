<?php
require_once __DIR__ . '/config.php';

$conn = new mysqli(
    config('DB_HOST', 'localhost'),
    config('DB_USER', 'root'),
    config('DB_PASS', ''),
    config('DB_NAME', 'it_shop')
);

if ($conn->connect_error) {
    // Le détail de l'erreur ne doit pas être montré aux visiteurs.
    error_log('Connexion MySQL impossible : ' . $conn->connect_error);
    http_response_code(500);
    exit('Service momentanément indisponible.');
}
$conn->set_charset('utf8mb4');

require __DIR__ . '/vendor/autoload.php';

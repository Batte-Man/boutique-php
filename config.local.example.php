<?php
// Copier ce fichier en config.local.php (ignoré par Git) et y mettre les vraies valeurs.
return [
    'DB_HOST' => 'localhost',
    'DB_USER' => 'root',
    'DB_PASS' => '',
    'DB_NAME' => 'it_shop',

    'SMTP_HOST'     => 'smtp.example.com',
    'SMTP_PORT'     => '587',
    'SMTP_USER'     => 'contact@example.com',
    'SMTP_PASS'     => '',
    'CONTACT_EMAIL' => 'contact@example.com', // destinataire des messages du formulaire
];

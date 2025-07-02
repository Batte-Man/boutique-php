<?php
session_start();
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$name    = trim($_POST['name']    ?? '');
$email   = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$message = trim($_POST['message'] ?? '');

$errors = [];
if ($name === '')  $errors[] = 'Le nom est requis.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Adresse e-mail invalide.';
if ($message === '') $errors[] = 'Le message ne peut pas être vide.';

if ($errors) {
    $_SESSION['contact_errors'] = $errors;
    header('Location: index.php#contact');
    exit;
}

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.example.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'contact@example.com';
    $mail->Password   = 'REDACTED';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('contact@example.com', 'IT Shop');
    $mail->addAddress('contact@example.com');
    $mail->addReplyTo($email, $name);

    $mail->Subject = "Nouveau message de contact de $name";
    $body = "Nom    : $name\n";
    $body .= "Email  : $email\n\n";
    $body .= "Message:\n$message";
    $mail->Body = $body;

    $mail->send();
    $_SESSION['contact_success'] = 'Votre message a bien été envoyé !';
} catch (Exception $e) {
    $_SESSION['contact_errors'] = ["Erreur d'envoi : {$mail->ErrorInfo}"];
}

header('Location: index.php#contact');

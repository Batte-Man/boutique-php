<?php
session_start();
require __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$name    = trim($_POST['name']    ?? '');
$email   = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
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
    $mail->Host       = config('SMTP_HOST');
    $mail->SMTPAuth   = true;
    $mail->Username   = config('SMTP_USER');
    $mail->Password   = config('SMTP_PASS');
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = (int) config('SMTP_PORT', '587');
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom(config('SMTP_USER'), 'IT Shop');
    $mail->addAddress(config('CONTACT_EMAIL', config('SMTP_USER')));
    $mail->addReplyTo($email, $name);

    $mail->Subject = "Nouveau message de contact de $name";
    $body = "Nom    : $name\n";
    $body .= "Email  : $email\n\n";
    $body .= "Message:\n$message";
    $mail->Body = $body;

    $mail->send();
    $_SESSION['contact_success'] = 'Votre message a bien été envoyé !';
} catch (Exception $e) {
    error_log('Envoi du formulaire de contact impossible : ' . $mail->ErrorInfo);
    $_SESSION['contact_errors'] = ["Le message n'a pas pu être envoyé. Réessayez plus tard."];
}

header('Location: index.php#contact');

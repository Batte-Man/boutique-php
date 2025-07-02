<?php
session_start();
require_once 'db.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: admin.php');
    exit;
}

$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion – IT Shop Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">
<nav class="navbar navbar-light bg-white border-bottom">
    <div class="container justify-content-center">
        <a class="navbar-brand" href="index.php">
            <img src="img/logo/Logo.png" height="40" alt="IT Shop">
        </a>
    </div>
</nav>
<main class="container mt-5" style="max-width: 400px;">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h4 mb-3 text-center">Connexion Admin</h1>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES) ?></div>
            <?php endif; ?>
            <form method="post" action="auth.php">
                <div class="mb-3">
                    <label for="login" class="form-label">Login</label>
                    <input type="text" id="login" name="login" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <button class="btn btn-primary w-100" type="submit">Se connecter</button>
            </form>
        </div>
    </div>
</main>

<footer class="text-white py-3">
    <div class="container text-center">
        <small>&copy; <?= date('Y') ?> IT Shop. Tous droits réservés.</small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
require_once 'db.php';

$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
    exit('ID invalide');
}

$stmt = $conn->prepare('SELECT * FROM produits WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$p = $stmt->get_result()->fetch_assoc();
if (!$p) {
    exit('Produit introuvable');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($p['nom'], ENT_QUOTES) ?> – IT Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <img src="img/logo/Logo.png" alt="IT Shop" height="40">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                <li class="nav-item"><a class="nav-link" href="produits.php">Tous nos produits</a></li>
                <li class="nav-item"><a class="nav-link" href="produits.php">Produits</a></li>
                <li class="nav-item"><a class="nav-link" href="login.php">Connexion</a></li>
            </ul>
        </div>
    </div>
</nav>

<main class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card">
                <h1 class="card-title text-center"><?= htmlspecialchars($p['nom'], ENT_QUOTES) ?></h1>

                <img src="img/<?= htmlspecialchars($p['photo_produit'], ENT_QUOTES) ?>" class="card-img-top" alt="<?= htmlspecialchars($p['nom'], ENT_QUOTES) ?>">
                <div class="card-body">
                    <p class="card-text"><?= nl2br(htmlspecialchars($p['description'], ENT_QUOTES)) ?></p>
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item"><strong>Prix :</strong> <?= number_format($p['prix'], 2, ',', ' ') ?> €</li>
                        <li class="list-group-item"><strong>Stock :</strong> <?= htmlspecialchars((string) $p['quantite_en_stock'], ENT_QUOTES) ?></li>
                        <li class="list-group-item"><strong>Marque :</strong> <?= htmlspecialchars($p['marque'], ENT_QUOTES) ?></li>
                        <li class="list-group-item"><strong>Catégorie :</strong> <?= htmlspecialchars($p['categorie'] ?? '', ENT_QUOTES) ?></li>
                        <li class="list-group-item"><strong>Note moyenne :</strong> <?= round($p['evaluation_moyenne'], 1) ?>/5</li>
                    </ul>
                    <a href="index.php" class="btn btn-secondary w-100">Retour</a>
                </div>
            </div>
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

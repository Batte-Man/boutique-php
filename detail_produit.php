<?php
session_start();
require_once 'db.php';
require_once 'auth.php';

if (empty($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['gerant','assistant'], true)) {
    header('Location: login.php');
    exit;
}

if (empty($_POST['id']) || !ctype_digit($_POST['id'])) {
    exit('Identifiant manquant ou invalide');
}
$id = (int) $_POST['id'];

$stmt = $conn->prepare(
    'SELECT id, nom, description, prix, marque, quantite_en_stock AS stock, statut, date_ajout,evaluation_moyenne, categorie, photo_produit AS photo
     FROM produits WHERE id = ?'
);
$stmt->bind_param('i', $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
if (!$product) {
    exit('Produit introuvable');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Détail – <?= htmlspecialchars($product['nom'], ENT_QUOTES) ?> (Admin)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white border-bottom">
    <div class="container">
        <a class="navbar-brand" href="index.php"><img src="img/logo/Logo.png" height="40" alt="IT Shop Admin"></a>
        <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
            <li class="nav-item"><a class="nav-link" href="produits.php">Tous nos produits</a></li>
            <li class="nav-item"><a class="nav-link" href="admin.php">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link text-danger" href="auth.php?action=logout">Déconnexion</a></li>
        </ul>
    </div>
</nav>
<main class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8">
            <div class="card">
                <h1 class="mb-4 text-center card-title"><?= htmlspecialchars($product['nom'], ENT_QUOTES) ?></h1>
                <img src="img/<?= htmlspecialchars($product['photo'] ?? '', ENT_QUOTES) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['nom'] ?? '', ENT_QUOTES) ?>">
                <div class="card-body">
                    <p class="card-text mb-3"><strong>Description :</strong> <?= nl2br(htmlspecialchars($product['description'], ENT_QUOTES)) ?></p>
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item"><strong>Prix :</strong> <?= number_format($product['prix'], 2, ',', ' ') ?> €</li>
                        <li class="list-group-item"><strong>Marque :</strong> <?= htmlspecialchars($product['marque'] ?? '', ENT_QUOTES) ?></li>
                        <li class="list-group-item"><strong>Stock :</strong> <?= htmlspecialchars((string) $product['stock'], ENT_QUOTES) ?></li>
                        <li class="list-group-item"><strong>Statut :</strong> <?= htmlspecialchars($product['statut'] ?? '', ENT_QUOTES) ?></li>
                        <li class="list-group-item"><strong>Date d'ajout :</strong> <?= htmlspecialchars((string) $product['date_ajout'], ENT_QUOTES) ?></li>
                        <li class="list-group-item"><strong>Evaluation moyenne :</strong> <?= htmlspecialchars($product['evaluation_moyenne'] ?? '', ENT_QUOTES) ?></li>
                        <li class="list-group-item"><strong>Catégorie :</strong> <?= htmlspecialchars($product['categorie'] ?? '', ENT_QUOTES) ?></li>
                    </ul>
                    <form method="post" action="edit_produit.php" class="d-inline">
                        <input type="hidden" name="id" value="<?= htmlspecialchars((string) $product['id'], ENT_QUOTES) ?>">
                        <button class="btn btn-sm btn-primary">Editer</button>
                    </form>
                    <a href="admin.php" class="btn btn-secondary w-100">Retour au dashboard</a>
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

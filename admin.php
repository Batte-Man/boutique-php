<?php

session_start();
require_once 'db.php';
require_once 'auth.php';

if (empty($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['gerant','assistant'], true)) {
    header('Location: login.php');
    exit;
}

$col   = $_GET['col'] ?? 'date_ajout';
$dir   = ($_GET['dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
$valid = ['id','nom','prix','quantite_en_stock','statut', 'evaluation_moyenne', 'categorie', 'marque','date_ajout'];
if (!in_array($col, $valid, true)) $col = 'date_ajout';
$next  = $dir === 'asc' ? 'desc' : 'asc';
$order = "$col $dir";

$stmt = $conn->prepare(
    "SELECT id, nom, prix, quantite_en_stock, statut, evaluation_moyenne, categorie, marque, date_ajout".
    " FROM produits ORDER BY $order"
);
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin – Tableau de bord</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white border-bottom">
    <div class="container">
        <a class="navbar-brand" href="index.php"><img src="img/logo/Logo.png" height="40" alt="IT Shop"></a>
        <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
            <li class="nav-item"><a class="nav-link" href="produits.php">Tous nos produits</a></li>
            <li class="nav-item"><a class="nav-link active" href="admin.php">Dashboard</a></li>
            <?php if ($_SESSION['user_role']==='gerant'): ?>
                <li class="nav-item"><a class="nav-link" href="add_produit.php">Ajouter</a></li>
            <?php endif; ?>
            <li class="nav-item"><a class="nav-link text-danger" href="auth.php?action=logout">Déconnexion</a></li>
        </ul>
    </div>
</nav>
<main class="container py-4">
    <h1 class="mb-4">Tableau de bord</h1>
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead class="table-primary">
            <tr>
                <th><a href="?col=id&dir=<?= $next ?>">ID</a></th>
                <th><a href="?col=nom&dir=<?= $next ?>">Nom</a></th>
                <th><a href="?col=prix&dir=<?= $next ?>">Prix</a></th>
                <th><a href="?col=quantite_en_stock&dir=<?= $next ?>">Stock</a></th>
                <th><a href="?col=statut&dir=<?= $next ?>">Statut</a></th>
                <th><a href="?col=evaluation_moyenne&dir=<?= $next ?>">Evaluation Moyenne</a></th>
                <th><a href="?col=categorie&dir=<?= $next ?>">Catégorie</a></th>
                <th><a href="?col=marque&dir=<?= $next ?>">Marque</a></th>
                <th><a href="?col=date_ajout&dir=<?= $next ?>">Date</a></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($products as $p): ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td><?= htmlspecialchars($p['nom'], ENT_QUOTES) ?></td>
                <td><?= number_format($p['prix'],2,',',' ') ?> €</td>
                <td><?= $p['quantite_en_stock'] ?></td>
                <td><?= htmlspecialchars($p['statut'], ENT_QUOTES) ?></td>
                <td><?= $p['evaluation_moyenne'] ?>/5</td>
                <td><?= $p['categorie'] ?></td>
                <td><?= $p['marque'] ?></td>
                <td><?= $p['date_ajout'] ?></td>
                <td>
                    <form method="post" action="detail_produit.php" class="d-inline">
                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                        <button class="btn btn-sm btn-primary">Détail</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
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

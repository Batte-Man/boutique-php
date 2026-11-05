<?php
require_once 'db.php';

$page = max(1, intval($_GET['page'] ?? 1));
$limit = 12;
$offset = ($page - 1) * $limit;

$stmt = $conn->prepare(
    "SELECT id, nom, photo_produit, prix, evaluation_moyenne
     FROM produits
     WHERE statut = 'disponible'
     ORDER BY date_ajout DESC
     LIMIT ?, ?"
);
$stmt->bind_param('ii', $offset, $limit);
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// SQL_CALC_FOUND_ROWS / FOUND_ROWS() sont dépréciés depuis MySQL 8.0.17
$total = (int) $conn->query("SELECT COUNT(*) AS t FROM produits WHERE statut = 'disponible'")->fetch_assoc()['t'];
$totalPages = ceil($total / $limit);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IT Shop - Produits</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="index.php"><img src="img/logo/Logo.png" alt="IT Shop" height="40"></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                <li class="nav-item"><a class="nav-link active" href="produits.php">Tous nos produits</a></li>
                <li class="nav-item"><a class="nav-link" href="login.php">Connexion</a></li>
            </ul>
        </div>
    </div>
</nav>

<main class="container py-4">
    <h1 class="text-center mb-4">Tous nos produits</h1>
    <div class="row g-4">
        <?php foreach ($products as $p): ?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card h-100">
                    <img src="img/<?= htmlspecialchars($p['photo_produit'], ENT_QUOTES) ?>" class="card-img-top" alt="<?= htmlspecialchars($p['nom'], ENT_QUOTES) ?>">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($p['nom'], ENT_QUOTES) ?></h5>
                        <p class="card-text flex-grow-1"><?= number_format($p['prix'], 2, ',', ' ') ?> €</p>
                        <p>Note : <?= round($p['evaluation_moyenne'], 1) ?>/5</p>
                        <form method="post" action="produit.php" class="mt-auto">
                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
                            <button type="submit" class="btn btn-primary w-100">Voir</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <nav class="mt-4">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</main>

<footer class="text-white py-3">
    <div class="container text-center">
        <small>&copy; <?= date('Y') ?> IT Shop. Tous droits réservés.</small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

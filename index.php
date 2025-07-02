<?php
require_once 'db.php';

$stmt = $conn->prepare(
    "SELECT id, nom, photo_produit, prix, evaluation_moyenne
     FROM produits
     WHERE statut = 'disponible'
     ORDER BY date_ajout DESC
     LIMIT 6"
);
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IT Shop - Accueil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="index.php"><img src="img/logo/Logo.png" alt="logo IT Shop" height="40"></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link active" href="index.php">Accueil</a></li>
                <li class="nav-item"><a class="nav-link" href="#about">Nous connaître</a></li>
                <li class="nav-item"><a class="nav-link" href="#nouveautés">Nos nouveautés</a></li>
                <li class="nav-item"><a class="nav-link" href="produits.php">Tous nos produits</a></li>
                <li class="nav-item"><a class="nav-link" href="#contact">Contactez-nous</a></li>
                <li class="nav-item"><a class="nav-link" href="login.php">Connexion</a></li>
            </ul>
        </div>
    </div>
</nav>

<main class="container py-4">
    <section id="about">
        <div class="mb-5 text-center">
            <h1>Bienvenue chez IT Shop</h1>
            <p class="lead">
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Assumenda eaque facere, impedit inventore ipsa
                labore laudantium magni minima modi odio officia porro quae quas quasi, quidem quisquam sit sunt tempora,
                ullam voluptate. Quo, reprehenderit, voluptatum. Animi aspernatur autem corporis dignissimos distinctio
                doloremque explicabo facilis, id ipsam maiores necessitatibus totam unde!</p>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ab architecto at cumque dolore dolorem enim esse
                explicabo fugiat iste iusto minima, odit, officia optio perferendis quae quas quidem quisquam unde? Beatae
                eum fugiat natus nesciunt quasi repellat reprehenderit? Ab aliquam cupiditate eius laudantium odio odit quae
                reiciendis reprehenderit tempora voluptas.</p>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eveniet ipsa magnam officiis. Aut deleniti, enim
                error fugit illum iure minus, nesciunt numquam placeat quae qui quisquam quo, ratione recusandae voluptatum?
                Accusantium adipisci asperiores atque delectus deserunt dolore fugiat illum in ipsa ipsum iusto laborum
                libero nihil nobis odit sint, vitae?</p>

            </p>
        </div>
    </section>
    <section id="nouveautés" class="py-5">
        <div class="row g-4">
            <h1 class="text-center">Nos nouveautés</h1>
            <?php foreach ($products as $p): ?>
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="card h-100">
                        <img src="img/<?= htmlspecialchars($p['photo_produit'], ENT_QUOTES) ?>" class="card-img-top" alt="IT shop <?= htmlspecialchars($p['nom'], ENT_QUOTES) ?>">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($p['nom'], ENT_QUOTES) ?></h5>
                            <p class="card-text flex-grow-1"><?= number_format($p['prix'], 2, ',', ' ') ?> €</p>
                            <p class="mb-2">Note : <?= round($p['evaluation_moyenne'], 1) ?>/5</p>
                            <form method="post" action="produit.php" class="mt-auto">
                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                <button type="submit" class="btn btn-primary w-100">Voir</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <section id="contact" class="container py-5">
        <h2 class="mb-4 text-center">Contactez-nous</h2>
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">

                <?php if (!empty($_SESSION['contact_errors'])): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($_SESSION['contact_errors'] as $err): ?>
                                <li><?= htmlspecialchars($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php unset($_SESSION['contact_errors']); ?>
                <?php endif; ?>

                <?php if (!empty($_SESSION['contact_success'])): ?>
                    <div class="alert alert-success">
                        <?= htmlspecialchars($_SESSION['contact_success']) ?>
                    </div>
                    <?php unset($_SESSION['contact_success']); ?>
                <?php endif; ?>

                <form method="post" action="contact.php">
                    <div class="mb-3">
                        <label for="name" class="form-label">Votre nom</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Votre e-mail</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Votre message</label>
                        <textarea id="message" name="message" rows="5" class="form-control" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Envoyer</button>
                </form>
            </div>
        </div>
    </section>

    <section id="social" class="py-4">
        <div class="container text-center">
            <h5>Suivez-nous sur</h5>

            <a href="https://www.facebook.com/" class="mx-2"><img src="img/logo/facebook.png" alt="Facebook" height="32"></a>
            <a href="https://twitter.com" class="mx-2"><img src="img/logo/twitter.png" alt="Twitter" height="32"></a>
            <a href="https://instagram.com" class="mx-2"><img src="img/logo/instagram.png" alt="Instagram" height="32"></a>
            <a href="https://linkedin.com" class="mx-2"><img src="img/logo/linkedin.png" alt="LinkedIn" height="32"></a>
        </div>
    </section>
</main>

<footer class="text-white py-3">
    <div class="container text-center">
        <small>&copy; <?= date('Y') ?> IT Shop. Tous droits réservés.</small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

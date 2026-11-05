<?php
session_start();
require_once 'db.php';
require_once 'auth.php';
require_once 'upload.php';

if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'gerant') {
    header('Location: login.php');
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom    = trim($_POST['nom'] ?? '');
    $desc   = trim($_POST['description'] ?? '');
    $prix   = floatval($_POST['prix'] ?? 0);
    $stock  = intval($_POST['quantite_en_stock'] ?? 0);
    $marque = trim($_POST['marque'] ?? '');
    $eval   = floatval($_POST['evaluation_moyenne'] ?? 0);
    $statut = in_array($_POST['statut'] ?? '', ['disponible','indisponible'], true)
        ? $_POST['statut'] : 'disponible';

    [$filename, $erreurPhoto] = enregistrer_photo($_FILES['photo'] ?? [], __DIR__ . '/img');
    if ($erreurPhoto !== null) {
        $errors[] = $erreurPhoto;
    }

    if ($nom === '')    $errors[] = 'Le nom est requis.';
    if ($desc === '')   $errors[] = 'La description est requise.';
    if ($marque === '') $errors[] = 'La marque est requise.';
    if ($prix <= 0)     $errors[] = 'Le prix doit être supérieur à 0.';
    if ($stock < 0)     $errors[] = 'La quantité en stock doit être ≥ 0.';
    if ($eval < 0 || $eval > 5) $errors[] = 'L\'évaluation doit être entre 0 et 5.';

    if (empty($errors)) {
        $stmt = $conn->prepare(
            'INSERT INTO produits
             (nom, description, prix, quantite_en_stock, marque, evaluation_moyenne, statut, categorie, photo_produit, date_ajout)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())'
        );
        $stmt->bind_param(
            'ssdissss',
            $nom,
            $desc,
            $prix,
            $stock,
            $marque,
            $eval,
            $categorie,
            $statut,
            $filename
        );
        if ($stmt->execute()) {
            header('Location: admin.php');
            exit;
        } else {
            $errors[] = 'Erreur lors de l\'enregistrement.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ajouter un produit – Admin</title>
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
            <li class="nav-item"><a class="nav-link" href="admin.php">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link text-danger" href="auth.php?action=logout">Déconnexion</a></li>
        </ul>
    </div>
</nav>
<main class="container py-4">
    <h1 class="mb-4">Ajouter un produit</h1>
    <?php if ($errors): ?>
        <div class="alert alert-danger"><ul class="mb-0">
                <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e, ENT_QUOTES) ?></li><?php endforeach; ?>
            </ul></div>
    <?php endif; ?>
    <form method="post" action="" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Nom</label>
            <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($_POST['nom'] ?? '', ENT_QUOTES) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4" required><?= htmlspecialchars($_POST['description'] ?? '', ENT_QUOTES) ?></textarea>
        </div>
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Prix (€)</label>
                <input type="number" step="0.01" name="prix" class="form-control" value="<?= htmlspecialchars($_POST['prix'] ?? '', ENT_QUOTES) ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Stock</label>
                <input type="number" name="quantite_en_stock" class="form-control" value="<?= htmlspecialchars($_POST['quantite_en_stock'] ?? '', ENT_QUOTES) ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Marque</label>
                <input type="text" name="marque" class="form-control" value="<?= htmlspecialchars($_POST['marque'] ?? '', ENT_QUOTES) ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Catégorie</label>
                <input type="text" name="categorie" class="form-control" value="<?= htmlspecialchars($_POST['categorie'] ?? '', ENT_QUOTES) ?>" required>
            </div>
        </div>
        <div class="row g-3 mt-3">
            <div class="col-md-4">
                <label class="form-label">Evaluation moyenne</label>
                <input type="number" step="0.1" name="evaluation_moyenne" class="form-control" value="<?= htmlspecialchars($_POST['evaluation_moyenne'] ?? '', ENT_QUOTES) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Statut</label>
                <select name="statut" class="form-select">
                    <option value="disponible" <?= (($_POST['statut'] ?? '') === 'disponible') ? 'selected' : '' ?>>Disponible</option>
                    <option value="indisponible" <?= (($_POST['statut'] ?? '') === 'indisponible') ? 'selected' : '' ?>>Indisponible</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Photo du produit</label>
                <input type="file" name="photo" accept="image/*" class="form-control" required>
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="admin.php" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</main>

<footer class="text-white py-3">
    <div class="container text-center">
        <small>&copy; <?= date('Y') ?> IT Shop. Tous droits réservés.</small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

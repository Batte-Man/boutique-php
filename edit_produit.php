<?php
session_start();
require_once 'db.php';
require_once 'auth.php';

if (empty($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['gerant','assistant'], true)) {
    header('Location: login.php'); exit;
}

if (empty($_POST['id']) || !ctype_digit($_POST['id'])) {
    http_response_code(400); exit('ID manquant ou invalide');
}
$id = (int) $_POST['id'];

$stmt = $conn->prepare('SELECT * FROM produits WHERE id = ?');
$stmt->bind_param('i',$id);
$stmt->execute();
$p = $stmt->get_result()->fetch_assoc();
if (!$p) { http_response_code(404); exit('Produit introuvable'); }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action']==='update') {
    csrf_verify();

    $nom   = trim($_POST['nom'] ?? '');
    $desc  = trim($_POST['description'] ?? '');
    $prix  = floatval($_POST['prix'] ?? 0);
    $stock = intval($_POST['quantite_en_stock'] ?? 0);
    $statut = $_SESSION['user_role']==='assistant'
        ? $p['statut']
        : (in_array($_POST['statut'], ['disponible','indisponible'], true) ? $_POST['statut'] : $p['statut']);
    $marque = trim($_POST['marque'] ?? '');
    $eval   = floatval($_POST['evaluation_moyenne'] ?? 0);
    $categorie = trim($_POST['categorie'] ?? '');

    if ($nom==='')    $errors[] = 'Le nom est requis.';
    if ($desc==='')   $errors[] = 'La description est requise.';
    if ($prix<=0)     $errors[] = 'Le prix doit être supérieur à 0.';
    if ($stock<0)     $errors[] = 'Le stock doit être ≥ 0.';
    if ($eval < 0 || $eval > 5) $errors[] = 'L\'évaluation doit être entre 0 et 5.';

    if (!$errors) {
        if ($_SESSION['user_role']==='assistant') {
            $upd = $conn->prepare('UPDATE produits SET description = ? WHERE id = ?');
            $upd->bind_param('si',$desc,$id);
        } else {
            $upd = $conn->prepare(
                'UPDATE produits SET nom=?, description=?, prix=?, quantite_en_stock=?, statut=?, categorie=?, marque=?, evaluation_moyenne=? WHERE id=?'
            );
            $upd->bind_param('ssdisssdi',$nom,$desc,$prix,$stock,$statut,$categorie,$marque,$eval,$id);
        }
        if ($upd->execute()) {
            header('Location: admin.php'); exit;
        } else {
            $errors[] = 'Erreur lors de la mise à jour.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Modifier – <?= htmlspecialchars($p['nom'], ENT_QUOTES) ?> (Admin)</title>
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
    <h1 class="mb-4 text-center">Modifier le produit</h1>
    <?php if ($errors): ?>
        <div class="alert alert-danger"><ul class="mb-0">
                <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e,ENT_QUOTES) ?></li><?php endforeach; ?>
            </ul></div>
    <?php endif; ?>
    <form method="post" action="edit_produit.php">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= $id ?>">
        <input type="hidden" name="action" value="update">
        <div class="mb-3">
            <label class="form-label">Nom</label>
            <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($p['nom'],ENT_QUOTES) ?>" <?= $_SESSION['user_role']==='assistant'?'readonly':'' ?> required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4" required><?= htmlspecialchars($p['description'],ENT_QUOTES) ?></textarea>
        </div>
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Prix (€)</label>
                <input type="number" step="0.01" name="prix" class="form-control" value="<?= htmlspecialchars((string) $p['prix'], ENT_QUOTES) ?>" <?= $_SESSION['user_role']==='assistant'?'readonly':'' ?> required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Stock</label>
                <input type="number" name="quantite_en_stock" class="form-control" value="<?= htmlspecialchars((string) $p['quantite_en_stock'], ENT_QUOTES) ?>" <?= $_SESSION['user_role']==='assistant'?'readonly':'' ?> required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Marque</label>
                <input type="text" name="marque" class="form-control" value="<?= htmlspecialchars((string) $p['marque'], ENT_QUOTES) ?>" <?= $_SESSION['user_role']==='assistant'?'readonly':'' ?> required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Catégorie</label>
                <input type="text" name="categorie" class="form-control" value="<?= htmlspecialchars((string) $p['categorie'], ENT_QUOTES) ?>" <?= $_SESSION['user_role']==='assistant'?'readonly':'' ?> required>
            </div>

        </div>
        <div class="row g-3 mt-3">
            <div class="col-md-4">
                <label class="form-label">Evaluation moyenne</label>
                <input type="number" step="0.1" name="evaluation_moyenne" class="form-control" value="<?= htmlspecialchars((string) $p['evaluation_moyenne'], ENT_QUOTES) ?>" <?= $_SESSION['user_role']==='assistant'?'readonly':'' ?> required>
            </div>
            <?php if($_SESSION['user_role']==='gerant'): ?>
                <div class="col-md-4">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-select">
                        <option value="disponible" <?= $p['statut']==='disponible'?'selected':'' ?>>Disponible</option>
                        <option value="indisponible" <?= $p['statut']==='indisponible'?'selected':'' ?>>Indisponible</option>
                    </select>
                </div>
            <?php endif; ?>
            <div class="col-md-4">
                <label class="form-label">Photo du produit</label>

                <?php if (!empty($p['photo_produit'])): ?>
                    <div class="mb-2 text-center">
                        <img
                                src="img/<?= htmlspecialchars($p['photo_produit'], ENT_QUOTES) ?>"
                                alt="<?= htmlspecialchars($p['nom'], ENT_QUOTES) ?>"
                                class="img-thumbnail"
                                style="max-height: 150px;"
                        >
                    </div>
                <?php endif; ?>

                <input
                        type="file"
                        name="photo"
                        accept="image/*"
                        class="form-control"
                    <?= $_SESSION['user_role']==='assistant' ? 'disabled' : '' ?>
                >
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

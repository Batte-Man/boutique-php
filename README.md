# IT Shop – boutique en ligne en PHP

Site vitrine d'une boutique high-tech avec espace d'administration, réalisé pour l'examen
certificatif de Projet de développement web (Bachelier en informatique de gestion, EAFC, juillet 2025).

**Technologies :** PHP 8 (sans framework), MySQL / MariaDB (mysqli, requêtes préparées),
Bootstrap 5, PHPMailer (Composer).

## Fonctionnalités

**Partie publique**
- Accueil avec les 6 derniers produits disponibles, catalogue paginé, fiche produit
- Formulaire de contact envoyé par SMTP (PHPMailer)

**Administration** (connexion requise)
- Deux rôles : **gérant** (tous les droits) et **assistant** (modification de la description seulement)
- Tableau de bord triable par colonne, détail, ajout et modification de produits, upload de photo

**Sécurité**
- Requêtes préparées pour toute donnée utilisateur, liste blanche pour le tri (`ORDER BY`)
- Mots de passe `password_hash()` / `password_verify()`, régénération de l'identifiant de session
- Jetons CSRF sur les formulaires qui modifient des données
- Upload : type vérifié sur le contenu du fichier, extension et nom imposés, scripts interdits dans `img/`
- Aucun secret dans le code : configuration dans `config.local.php` (non versionné) ou variables d'environnement

## Installation (XAMPP, WAMP ou équivalent)

```bash
composer install
cp config.local.example.php config.local.php   # puis renseigner la base et le SMTP
```

1. Importer `database/schema.sql` (phpMyAdmin ou `mysql -u root < database/schema.sql`).
2. Créer un compte : `php database/create_admin.php gerant1 gerant` (le mot de passe est demandé au clavier).
3. Placer le dossier dans `htdocs` et ouvrir `http://localhost/boutique-php/`.

## Historique

Le tag [`examen-juillet-2025`](../../tree/examen-juillet-2025) correspond au code rendu, avec les identifiants
SMTP masqués. Corrections faites lors de la mise sur GitHub (détail dans les commits) :

- **sécurité** : upload exploitable pour exécuter du code PHP, mots de passe en SHA-256 sans sel,
  identifiants SMTP dans le code, absence de jetons CSRF, quelques champs affichés sans échappement ;
- **bugs** : l'ajout de produit échouait toujours (erreur sur `bind_param`), la marque et l'évaluation
  n'étaient pas enregistrées à la modification ;
- le script SQL d'origine n'ayant pas été conservé, `database/schema.sql` a été reconstitué à partir du code.

---
Martin Buchet

<?php
/**
 * Validation et enregistrement d'une photo produit envoyée par formulaire.
 *
 * Le type est déterminé à partir du CONTENU du fichier (finfo) et l'extension
 * est imposée par ce type : ni le type MIME annoncé par le navigateur ni le nom
 * du fichier envoyé ne sont pris en compte, car ils sont contrôlés par l'utilisateur.
 *
 * @return array{0: ?string, 1: ?string} [nom du fichier enregistré, message d'erreur]
 */
function enregistrer_photo(array $file, string $dossier, int $tailleMax = 2_000_000): array
{
    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png'];

    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return [null, 'La photo est requise.'];
    }
    if (($file['size'] ?? 0) > $tailleMax) {
        return [null, 'La photo dépasse 2 Mo.'];
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!isset($extensions[$mime]) || @getimagesize($file['tmp_name']) === false) {
        return [null, 'Format de photo invalide (jpg, png).'];
    }

    $nom = 'prod_' . bin2hex(random_bytes(8)) . '.' . $extensions[$mime];
    if (!move_uploaded_file($file['tmp_name'], rtrim($dossier, '/') . '/' . $nom)) {
        return [null, "Erreur lors de l'enregistrement de la photo."];
    }
    return [$nom, null];
}

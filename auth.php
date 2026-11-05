<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once 'db.php';

/** Jeton anti-CSRF lié à la session, à inclure dans chaque formulaire qui modifie des données. */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
}

/** Arrête la requête si le jeton envoyé ne correspond pas à celui de la session. */
function csrf_verify(): void
{
    if (!hash_equals(csrf_token(), (string) ($_POST['csrf'] ?? ''))) {
        http_response_code(403);
        exit('Requête refusée (jeton de sécurité invalide). Rechargez la page.');
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $_SESSION = [];
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
    session_destroy();
    header('Location: login.php');
    exit;
}

if (basename($_SERVER['SCRIPT_NAME']) === 'auth.php'
    && $_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $login    = trim($_POST['login'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($login === '' || $password === '') {
        $_SESSION['error'] = 'Veuillez remplir tous les champs.';
        header('Location: login.php');
        exit;
    }

    $stmt = $conn->prepare(
        'SELECT id, login, mot_de_passe_hash AS hash, role FROM admin WHERE login = ?'
    );
    $stmt->bind_param('s', $login);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $hash = $user['hash'];

        $valide = password_verify($password, $hash);
        // Compatibilité : anciens comptes hachés en SHA-256 sans sel.
        // Le hash est remplacé par un hash bcrypt dès la première connexion réussie.
        $ancienFormat = !$valide && preg_match('/^[0-9a-f]{64}$/', $hash)
            && hash_equals($hash, hash('sha256', $password));

        if ($valide || $ancienFormat) {
            if ($ancienFormat || password_needs_rehash($hash, PASSWORD_DEFAULT)) {
                $nouveau = password_hash($password, PASSWORD_DEFAULT);
                $upd = $conn->prepare('UPDATE admin SET mot_de_passe_hash = ? WHERE id = ?');
                $upd->bind_param('si', $nouveau, $user['id']);
                $upd->execute();
            }
            session_regenerate_id(true); // empêche la fixation de session
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['user_login'] = $user['login'];
            $_SESSION['user_role']  = $user['role'];
            header('Location: admin.php');
            exit;
        }
    }
    $_SESSION['error'] = 'Le nom ou le mot de passe est incorrect.';
    header('Location: login.php');
    exit;
}
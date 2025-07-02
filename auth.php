<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once 'db.php';

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
        if (hash('sha256', $password) === $user['hash']) {
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
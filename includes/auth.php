<?php
/**
 * Controle de sessão do painel de admin.
 * Inclua este arquivo no topo de toda página que só o admin pode ver.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Bloqueia o acesso e redireciona pro login se não houver
 * um admin autenticado na sessão atual.
 */
function exigirLogin(): void
{
    if (empty($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Tenta autenticar um admin pelo e-mail e senha informados.
 * Retorna true se deu certo (e já grava a sessão), false caso contrário.
 */
function tentarLogin(PDO $pdo, string $email, string $senha): bool
{
    $stmt = $pdo->prepare('SELECT id, senha_hash FROM admin_usuarios WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($senha, $admin['senha_hash'])) {
        // Regenera o ID de sessão ao logar — evita fixação de sessão.
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_email'] = $email;
        return true;
    }

    return false;
}

function fazerLogout(): void
{
    $_SESSION = [];
    session_destroy();
}

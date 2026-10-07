<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

// Se já está logado, manda direto pro painel.
if (!empty($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($email === '' || $senha === '') {
        $erro = 'Preencha e-mail e senha.';
    } else {
        $pdo = getConexao();
        if (tentarLogin($pdo, $email, $senha)) {
            header('Location: dashboard.php');
            exit;
        } else {
            $erro = 'E-mail ou senha incorretos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — ImpressMaker3D</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .login-box {
            max-width: 360px;
            margin: 10vh auto;
            padding: 2rem;
            border-radius: 12px;
            background: #14101f;
            color: #fff;
        }
        .login-box h1 { font-size: 1.3rem; margin-bottom: 1.5rem; }
        .login-box label { display: block; margin-bottom: .3rem; font-size: .9rem; }
        .login-box input {
            width: 100%; padding: .6rem; margin-bottom: 1rem;
            border-radius: 6px; border: 1px solid #444; background: #0a0714; color: #fff;
        }
        .login-box button {
            width: 100%; padding: .7rem; border: none; border-radius: 6px;
            background: #9B3FF0; color: #fff; font-weight: bold; cursor: pointer;
        }
        .login-box .erro { color: #ff6b6b; margin-bottom: 1rem; font-size: .9rem; }
    </style>
</head>
<body>
    <div class="login-box">
        <h1>Painel ImpressMaker3D</h1>

        <?php if ($erro): ?>
            <p class="erro"><?= htmlspecialchars($erro) ?></p>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" required autofocus>

            <label for="senha">Senha</label>
            <input type="password" id="senha" name="senha" required>

            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>

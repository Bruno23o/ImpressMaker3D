<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

exigirLogin();

$pdo = getConexao();
$produtos = buscarProdutos($pdo);

// Mensagem de sucesso vinda de produto-novo.php, produto-editar.php etc.
$mensagem = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel — ImpressMaker3D</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-body">
    <header class="admin-header">
        <h1>Painel ImpressMaker3D</h1>
        <div class="admin-header-actions">
            <span>Olá, <?= htmlspecialchars($_SESSION['admin_email']) ?></span>
            <a href="logout.php" class="btn-sair">Sair</a>
        </div>
    </header>

    <main class="admin-main">
        <div class="admin-toolbar">
            <h2>Produtos (<?= count($produtos) ?>)</h2>
            <a href="produto-form.php" class="btn-primario">+ Novo produto</a>
        </div>

        <?php if ($mensagem): ?>
            <p class="admin-sucesso"><?= htmlspecialchars($mensagem) ?></p>
        <?php endif; ?>

        <?php if (empty($produtos)): ?>
            <p class="admin-vazio">Nenhum produto cadastrado ainda. Clique em "Novo produto" pra começar.</p>
        <?php else: ?>
            <table class="admin-tabela">
                <thead>
                    <tr>
                        <th>Imagem</th>
                        <th>Nome</th>
                        <th>Preço</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produtos as $produto): ?>
                        <tr>
                            <td>
                                <?php if ($produto['imagem_principal']): ?>
                                    <img src="../<?= htmlspecialchars($produto['imagem_principal']) ?>"
                                         alt="<?= htmlspecialchars($produto['nome']) ?>" class="admin-thumb">
                                <?php else: ?>
                                    <span class="admin-sem-imagem">Sem foto</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($produto['nome']) ?></td>
                            <td><?= formatarPreco((float) $produto['preco']) ?></td>
                            <td>
                                <?php if ($produto['ativo']): ?>
                                    <span class="status-ativo">Ativo</span>
                                <?php else: ?>
                                    <span class="status-inativo">Oculto</span>
                                <?php endif; ?>
                            </td>
                            <td class="admin-acoes">
                                <a href="produto-form.php?id=<?= $produto['id'] ?>">Editar</a>
                                <form action="produto-excluir.php" method="POST"
                                      onsubmit="return confirm('Tem certeza que deseja excluir este produto?');">
                                    <input type="hidden" name="id" value="<?= $produto['id'] ?>">
                                    <button type="submit" class="btn-excluir">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>
</body>
</html>

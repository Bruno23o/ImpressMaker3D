<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

exigirLogin();

$pdo = getConexao();

$editando = isset($_GET['id']);
$produto = ['nome' => '', 'descricao' => '', 'detalhes_material' => '', 'preco' => '', 'ativo' => 1, 'imagens' => []];
$erro = '';

if ($editando) {
    $produtoExistente = buscarProdutoPorId($pdo, (int) $_GET['id']);
    if (!$produtoExistente) {
        header('Location: dashboard.php?msg=' . urlencode('Produto não encontrado.'));
        exit;
    }
    $produto = $produtoExistente;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $detalhesMaterial = trim($_POST['detalhes_material'] ?? '');
    $preco = str_replace(',', '.', $_POST['preco'] ?? '');
    $ativo = isset($_POST['ativo']) ? 1 : 0;

    if ($nome === '' || $descricao === '' || !is_numeric($preco)) {
        $erro = 'Preencha nome, descrição e um preço válido.';
    } else {
        try {
            if ($editando) {
                $stmt = $pdo->prepare('
                    UPDATE produtos
                    SET nome = :nome, descricao = :descricao, detalhes_material = :detalhes,
                        preco = :preco, ativo = :ativo
                    WHERE id = :id
                ');
                $stmt->execute([
                    'nome' => $nome,
                    'descricao' => $descricao,
                    'detalhes' => $detalhesMaterial,
                    'preco' => $preco,
                    'ativo' => $ativo,
                    'id' => $produto['id'],
                ]);
                $produtoId = (int) $produto['id'];
            } else {
                $stmt = $pdo->prepare('
                    INSERT INTO produtos (nome, descricao, detalhes_material, preco, ativo)
                    VALUES (:nome, :descricao, :detalhes, :preco, :ativo)
                ');
                $stmt->execute([
                    'nome' => $nome,
                    'descricao' => $descricao,
                    'detalhes' => $detalhesMaterial,
                    'preco' => $preco,
                    'ativo' => $ativo,
                ]);
                $produtoId = (int) $pdo->lastInsertId();
            }

            // Upload de nova imagem (opcional — soma às existentes).
            $caminhoImagem = salvarImagemProduto('imagem');
            if ($caminhoImagem !== null) {
                $stmtImg = $pdo->prepare('
                    INSERT INTO produto_imagens (produto_id, caminho_arquivo, texto_alternativo, ordem)
                    VALUES (:produto_id, :caminho, :alt, :ordem)
                ');
                $stmtImg->execute([
                    'produto_id' => $produtoId,
                    'caminho' => $caminhoImagem,
                    'alt' => $nome,
                    'ordem' => count($produto['imagens']),
                ]);
            }

            $msg = $editando ? 'Produto atualizado com sucesso.' : 'Produto criado com sucesso.';
            header('Location: dashboard.php?msg=' . urlencode($msg));
            exit;

        } catch (Exception $e) {
            $erro = $e->getMessage();
        }
    }

    // Em caso de erro, mantém os dados digitados na tela.
    $produto = array_merge($produto, [
        'nome' => $nome, 'descricao' => $descricao,
        'detalhes_material' => $detalhesMaterial, 'preco' => $preco, 'ativo' => $ativo,
    ]);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $editando ? 'Editar' : 'Novo' ?> produto — ImpressMaker3D</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-body">
    <header class="admin-header">
        <h1><?= $editando ? 'Editar produto' : 'Novo produto' ?></h1>
        <a href="dashboard.php" class="btn-voltar">&larr; Voltar</a>
    </header>

    <main class="admin-main">
        <?php if ($erro): ?>
            <p class="admin-erro"><?= htmlspecialchars($erro) ?></p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="admin-form">
            <label for="nome">Nome do produto</label>
            <input type="text" id="nome" name="nome" required value="<?= htmlspecialchars($produto['nome']) ?>">

            <label for="descricao">Descrição</label>
            <textarea id="descricao" name="descricao" rows="3" required><?= htmlspecialchars($produto['descricao']) ?></textarea>

            <label for="detalhes_material">Detalhes do material</label>
            <textarea id="detalhes_material" name="detalhes_material" rows="2"><?= htmlspecialchars($produto['detalhes_material'] ?? '') ?></textarea>

            <label for="preco">Preço (R$)</label>
            <input type="text" id="preco" name="preco" required placeholder="Ex: 89,90"
                   value="<?= htmlspecialchars((string) $produto['preco']) ?>">

            <label class="admin-checkbox">
                <input type="checkbox" name="ativo" <?= $produto['ativo'] ? 'checked' : '' ?>>
                Produto visível no site
            </label>

            <?php if (!empty($produto['imagens'])): ?>
                <p class="admin-label-imagens">Imagens atuais:</p>
                <div class="admin-imagens-atuais">
                    <?php foreach ($produto['imagens'] as $img): ?>
                        <img src="../<?= htmlspecialchars($img['caminho_arquivo']) ?>" alt="">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <label for="imagem">
                <?= !empty($produto['imagens']) ? 'Adicionar outra imagem (opcional)' : 'Imagem do produto' ?>
            </label>
            <input type="file" id="imagem" name="imagem" accept=".jpg,.jpeg,.png,.webp">

            <button type="submit" class="btn-primario"><?= $editando ? 'Salvar alterações' : 'Criar produto' ?></button>
        </form>
    </main>
</body>
</html>

<?php
/**
 * Funções auxiliares usadas pelo painel de admin.
 */

/**
 * Busca todos os produtos com sua imagem principal (a de menor "ordem").
 */
function buscarProdutos(PDO $pdo, bool $apenasAtivos = false): array
{
    $sql = '
        SELECT p.*,
               (SELECT caminho_arquivo FROM produto_imagens pi
                WHERE pi.produto_id = p.id
                ORDER BY pi.ordem ASC LIMIT 1) AS imagem_principal
        FROM produtos p
    ';

    if ($apenasAtivos) {
        $sql .= ' WHERE p.ativo = 1';
    }

    $sql .= ' ORDER BY p.criado_em DESC';

    return $pdo->query($sql)->fetchAll();
}

/**
 * Busca um produto específico pelo ID, junto com todas as suas imagens.
 */
function buscarProdutoPorId(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM produtos WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $produto = $stmt->fetch();

    if (!$produto) {
        return null;
    }

    $stmtImg = $pdo->prepare('SELECT * FROM produto_imagens WHERE produto_id = :id ORDER BY ordem ASC');
    $stmtImg->execute(['id' => $id]);
    $produto['imagens'] = $stmtImg->fetchAll();

    return $produto;
}

/**
 * Faz o upload de uma imagem enviada por formulário (campo $_FILES[$campo])
 * e devolve o caminho relativo salvo (ex: assets/img/products/abc123.jpg),
 * ou null se nenhum arquivo foi enviado.
 *
 * Lança Exception com mensagem amigável se o arquivo for inválido.
 */
function salvarImagemProduto(string $campo): ?string
{
    if (empty($_FILES[$campo]['name']) || $_FILES[$campo]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($_FILES[$campo]['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Erro ao enviar a imagem. Tente novamente.');
    }

    $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];
    $extensao = strtolower(pathinfo($_FILES[$campo]['name'], PATHINFO_EXTENSION));

    if (!in_array($extensao, $extensoesPermitidas, true)) {
        throw new Exception('Formato de imagem não permitido. Use JPG, PNG ou WEBP.');
    }

    // Limite de 5MB por imagem.
    if ($_FILES[$campo]['size'] > 5 * 1024 * 1024) {
        throw new Exception('A imagem deve ter no máximo 5MB.');
    }

    $pastaDestino = __DIR__ . '/../assets/img/products/';
    if (!is_dir($pastaDestino)) {
        mkdir($pastaDestino, 0755, true);
    }

    $nomeArquivo = bin2hex(random_bytes(8)) . '.' . $extensao;
    $caminhoCompleto = $pastaDestino . $nomeArquivo;

    if (!move_uploaded_file($_FILES[$campo]['tmp_name'], $caminhoCompleto)) {
        throw new Exception('Não foi possível salvar a imagem no servidor.');
    }

    // Caminho relativo salvo no banco (usado no <img src="...">).
    return 'assets/img/products/' . $nomeArquivo;
}

function formatarPreco(float $preco): string
{
    return 'R$ ' . number_format($preco, 2, ',', '.');
}

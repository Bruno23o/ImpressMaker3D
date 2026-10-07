<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

exigirLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {
    $pdo = getConexao();
    // produto_imagens tem ON DELETE CASCADE, então as imagens do produto
    // são removidas automaticamente do banco (os arquivos físicos continuam
    // no servidor — tudo bem para o escopo atual do projeto).
    $stmt = $pdo->prepare('DELETE FROM produtos WHERE id = :id');
    $stmt->execute(['id' => (int) $_POST['id']]);
}

header('Location: dashboard.php?msg=' . urlencode('Produto excluído.'));
exit;

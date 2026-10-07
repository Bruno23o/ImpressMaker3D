-- Banco de dados ImpressMaker3D
-- Rode este arquivo uma vez no phpMyAdmin (ou via linha de comando)
-- para criar as tabelas usadas pelo site e pelo painel de admin.

CREATE DATABASE IF NOT EXISTS impressmaker3d
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE impressmaker3d;

-- ------------------------------------------------------
-- Tabela de produtos
-- ------------------------------------------------------
CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    descricao TEXT NOT NULL,
    detalhes_material TEXT NULL,
    preco DECIMAL(10,2) NOT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,   -- permite "esconder" um produto sem apagar
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------
-- Imagens de cada produto (suporta o carrossel: várias fotos por produto)
-- ------------------------------------------------------
CREATE TABLE IF NOT EXISTS produto_imagens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    caminho_arquivo VARCHAR(255) NOT NULL,   -- ex: assets/img/products/xyz.jpg
    texto_alternativo VARCHAR(255) NULL,     -- alt text da imagem
    ordem INT NOT NULL DEFAULT 0,            -- define a ordem no carrossel
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------
-- Usuários administradores (login do painel)
-- ------------------------------------------------------
CREATE TABLE IF NOT EXISTS admin_usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------
-- Usuário admin inicial de exemplo
-- Senha: admin123  (TROQUE depois de logar pela primeira vez!)
-- O hash abaixo é um bcrypt válido para "admin123", compatível com
-- password_verify() do PHP. Se preferir gerar o seu próprio, rode no
-- terminal com PHP instalado: php -r "echo password_hash('sua_senha', PASSWORD_DEFAULT);"
-- ------------------------------------------------------
INSERT INTO admin_usuarios (email, senha_hash) VALUES
('admin@impressmaker3d.com', '$2b$10$nqKy/bhVVYzyIsQm0U2R9OloNZUW3whr6ot0r09KD.uQ4SXU78.qi');

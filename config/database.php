<?php
/**
 * Conexão com o banco de dados usando PDO.
 *
 * IMPORTANTE: preencha os dados abaixo com as credenciais que sua
 * hospedagem fornecer (host, nome do banco, usuário e senha).
 * Nunca deixe esses dados de produção dentro de um repositório público —
 * se o projeto for pro GitHub, mova este arquivo para .gitignore e crie
 * uma cópia "database.example.php" sem os valores reais.
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'impressmaker3d');
define('DB_USER', 'root');
define('DB_PASS', '');

function getConexao(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            // Em produção, não exiba o erro real pro visitante — apenas registre.
            error_log('Erro de conexão com o banco: ' . $e->getMessage());
            die('Não foi possível conectar ao banco de dados. Tente novamente mais tarde.');
        }
    }

    return $pdo;
}

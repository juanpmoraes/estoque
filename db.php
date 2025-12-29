<?php
/**
 * Arquivo de Conexão com Banco de Dados
 * db.php
 */

// Configurações do banco de dados
$host = 'localhost';
$dbname = 'seu_banco_de_dados';
$username = 'seu_usuario';
$password = 'sua_senha';
$charset = 'utf8mb4';

// Configurar o DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

// Opções de configuração do PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,    // Lança exceções em caso de erro
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,          // Retorna arrays associativos
    PDO::ATTR_EMULATE_PREPARES   => false,                     // Usa prepared statements reais (segurança contra SQL injection)
    PDO::ATTR_PERSISTENT         => false,                     // Não usa conexões persistentes
];

// Criar conexão
try {
    $pdo = new PDO($dsn, $username, $password, $options);
    // echo "Conexão estabelecida com sucesso!"; // Descomente para testar
} catch (PDOException $e) {
    // Em produção, não exiba detalhes do erro para o usuário
    error_log("Erro de conexão: " . $e->getMessage());
    die("Erro ao conectar ao banco de dados. Por favor, tente novamente mais tarde.");
}

return $pdo;
?>

<?php
$host = 'squarre-cloud-db-45ee9918389d44b3937fdb8e41f83048.squareweb.app';
$db   = 'estoque';
$user = 'squarecloud';
$pass = 'INNbhFE8vqhXzKTHUHLhLFWk';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES => false,
];
try {
  $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
  http_response_code(500);
  echo "Erro de conexão: " . htmlspecialchars($e->getMessage());
  exit;
}

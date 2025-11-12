<?php
require __DIR__ . '/db.php';
$codigo = trim($_POST['codigo'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');
$qtd = max(1, (int)($_POST['quantidade'] ?? 1));
if ($codigo === '') { header('Location: nova_caixa.php'); exit; }

$stmt = $pdo->prepare("INSERT INTO caixa (codigo, descricao, quantidade) VALUES (?,?,?)");
$stmt->execute([$codigo, $descricao, $qtd]);
header('Location: index.php');

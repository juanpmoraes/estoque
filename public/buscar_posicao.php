<?php
require __DIR__.'/db.php';
$codigo = trim($_GET['codigo'] ?? '');
$stmt = $pdo->prepare("
  SELECT p.rua, p.prateleira, p.coluna, o.slot
  FROM ocupacao o
  JOIN posicao p ON p.id=o.posicao_id
  JOIN caixa c ON c.id=o.caixa_id
  WHERE c.codigo = ?
  LIMIT 1
");
$stmt->execute([$codigo]);
$pos = $stmt->fetch();
header('Content-Type: application/json');
echo json_encode($pos ?: []);

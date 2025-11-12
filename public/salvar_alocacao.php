<?php
require __DIR__ . '/db.php';
$posicao_id = (int)($_POST['posicao_id'] ?? 0);
$caixa_id = (int)($_POST['caixa_id'] ?? 0);
$slot = (int)($_POST['slot'] ?? 0);
if ($posicao_id<=0 || $caixa_id<=0 || $slot<1 || $slot>3) { http_response_code(400); exit('Dados inválidos'); }

$pdo->beginTransaction();
try {
  $qtd = $pdo->prepare("SELECT COUNT(*) FROM ocupacao WHERE posicao_id=?");
  $qtd->execute([$posicao_id]);
  if ((int)$qtd->fetchColumn() >= 3) { throw new RuntimeException('Coluna cheia'); }
  $chk = $pdo->prepare("SELECT 1 FROM ocupacao WHERE posicao_id=? AND slot=?");
  $chk->execute([$posicao_id, $slot]);
  if ($chk->fetch()) { throw new RuntimeException('Slot já ocupado'); }

  $ins = $pdo->prepare("INSERT INTO ocupacao (posicao_id, caixa_id, slot) VALUES (?,?,?)");
  $ins->execute([$posicao_id, $caixa_id, $slot]);

  $ruaprat = $pdo->prepare("SELECT rua, prateleira FROM posicao WHERE id=?");
  $ruaprat->execute([$posicao_id]);
  $meta = $ruaprat->fetch();

  $pdo->commit();
  header('Location: index.php?rua='.$meta['rua'].'&prateleira='.$meta['prateleira']);
} catch (Throwable $e) {
  $pdo->rollBack();
  http_response_code(409);
  echo "Erro: " . htmlspecialchars($e->getMessage());
}

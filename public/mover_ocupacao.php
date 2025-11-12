<?php
require __DIR__ . '/db.php';
$ocupacao_id = (int)($_POST['ocupacao_id'] ?? 0);
$rua = (int)($_POST['rua'] ?? 0);
$prateleira = (int)($_POST['prateleira'] ?? 0);
$coluna = (int)($_POST['coluna'] ?? 0);
$slot = (int)($_POST['slot'] ?? 0);
if ($ocupacao_id<=0 || $rua<1||$rua>14 || $prateleira<1||$prateleira>4 || $coluna<1||$coluna>18 || $slot<1||$slot>3) {
  http_response_code(400); exit('Dados inválidos');
}

$pdo->beginTransaction();
try {
  $posSel = $pdo->prepare("SELECT id FROM posicao WHERE rua=? AND prateleira=? AND coluna=?");
  $posSel->execute([$rua,$prateleira,$coluna]);
  $pos = $posSel->fetchColumn();
  if (!$pos) throw new RuntimeException('Posição destino inexistente');

  $count = $pdo->prepare("SELECT COUNT(*) FROM ocupacao WHERE posicao_id=?");
  $count->execute([$pos]);
  if ((int)$count->fetchColumn() >= 3) throw new RuntimeException('Coluna destino cheia');

  $slotLivre = $pdo->prepare("SELECT 1 FROM ocupacao WHERE posicao_id=? AND slot=?");
  $slotLivre->execute([$pos,$slot]);
  if ($slotLivre->fetch()) throw new RuntimeException('Slot destino ocupado');

  $upd = $pdo->prepare("UPDATE ocupacao SET posicao_id=?, slot=? WHERE id=?");
  $upd->execute([$pos, $slot, $ocupacao_id]);

  $pdo->commit();
  header('Location: index.php?rua='.$rua.'&prateleira='.$prateleira);
} catch (Throwable $e) {
  $pdo->rollBack();
  http_response_code(409);
  echo "Erro: " . htmlspecialchars($e->getMessage());
}

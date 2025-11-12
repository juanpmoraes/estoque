<?php
require __DIR__ . '/db.php';
$pos = (int)($_GET['pos'] ?? 0);
if ($pos<=0) { http_response_code(400); exit('Posição inválida'); }

$info = $pdo->prepare("SELECT * FROM posicao WHERE id=?");
$info->execute([$pos]);
$posicao = $info->fetch();
if (!$posicao) { http_response_code(404); exit('Posição não encontrada'); }

$itens = $pdo->prepare("SELECT o.id as ocupacao_id, o.slot, c.id as caixa_id, c.codigo, c.descricao
                        FROM ocupacao o JOIN caixa c ON c.id=o.caixa_id
                        WHERE o.posicao_id=? ORDER BY o.slot");
$itens->execute([$pos]);
$itens = $itens->fetchAll();
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title>Gerir coluna</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <h2>Gerir — Rua <?= $posicao['rua'] ?> · Prateleira <?= $posicao['prateleira'] ?> · Col <?= $posicao['coluna'] ?></h2>
  <?php if (!$itens): ?>
    <p>Sem caixas na coluna.</p>
  <?php else: ?>
    <ul>
      <?php foreach ($itens as $it): ?>
        <li>
          Slot <?= $it['slot'] ?> — <?= htmlspecialchars($it['codigo']) ?> (<?= htmlspecialchars($it['descricao'] ?? '') ?>)
          <form method="post" action="remover_ocupacao.php" style="display:inline">
            <input type="hidden" name="ocupacao_id" value="<?= $it['ocupacao_id'] ?>">
            <button>Remover</button>
          </form>
          <form method="post" action="mover_ocupacao.php" style="display:inline">
            <input type="hidden" name="ocupacao_id" value="<?= $it['ocupacao_id'] ?>">
            Nova rua: <input type="number" name="rua" min="1" max="14" style="width:60px" required>
            Prat: <input type="number" name="prateleira" min="1" max="4" style="width:50px" required>
            Col: <input type="number" name="coluna" min="1" max="18" style="width:50px" required>
            Slot: <input type="number" name="slot" min="1" max="3" style="width:50px" required>
            <button>Mover</button>
          </form>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
  <p><a href="index.php?rua=<?= $posicao['rua'] ?>&prateleira=<?= $posicao['prateleira'] ?>">Voltar</a></p>
</body>
</html>

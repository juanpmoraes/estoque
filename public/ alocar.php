<?php
require __DIR__ . '/db.php';
$pos = (int)($_GET['pos'] ?? 0);
if ($pos <= 0) { http_response_code(400); exit('Posição inválida'); }

$posInfo = $pdo->prepare("SELECT p.*, (SELECT COUNT(*) FROM ocupacao o WHERE o.posicao_id = p.id) AS ocupados FROM posicao p WHERE p.id = ?");
$posInfo->execute([$pos]);
$posicao = $posInfo->fetch();
if (!$posicao) { http_response_code(404); exit('Posição não encontrada'); }

$caixas = $pdo->query("SELECT id, codigo FROM caixa ORDER BY id DESC LIMIT 200")->fetchAll();
$slotsOcup = $pdo->prepare("SELECT slot FROM ocupacao WHERE posicao_id = ? ORDER BY slot");
$slotsOcup->execute([$pos]);
$ocupadosArr = $slotsOcup->fetchAll(PDO::FETCH_COLUMN);
$livres = array_values(array_diff([1,2,3], array_map('intval',$ocupadosArr)));
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title>Alocar caixa</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <h2>Alocar — Rua <?= $posicao['rua'] ?> · Prateleira <?= $posicao['prateleira'] ?> · Col <?= $posicao['coluna'] ?></h2>
  <?php if (count($ocupadosArr) >= 3): ?>
    <p>Coluna cheia (3/3). Remova uma caixa antes.</p>
    <p><a href="index.php?rua=<?= $posicao['rua'] ?>&prateleira=<?= $posicao['prateleira'] ?>">Voltar</a></p>
  <?php else: ?>
    <form method="post" action="salvar_alocacao.php" class="controls">
      <input type="hidden" name="posicao_id" value="<?= $pos ?>">
      <label>Caixa:
        <select name="caixa_id" required>
          <?php foreach ($caixas as $c): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['codigo']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Slot:
        <select name="slot" required>
          <?php foreach ($livres as $s): ?>
            <option value="<?= $s ?>">Slot <?= $s ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <button class="primary">Alocar</button>
      <a href="index.php?rua=<?= $posicao['rua'] ?>&prateleira=<?= $posicao['prateleira'] ?>"><button type="button">Cancelar</button></a>
    </form>
  <?php endif; ?>
</body>
</html>

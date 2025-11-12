<?php
require __DIR__ . '/db.php';

$rua = isset($_GET['rua']) ? max(1, min(14, (int)$_GET['rua'])) : 1;
$prateleira = isset($_GET['prateleira']) ? max(1, min(4, (int)$_GET['prateleira'])) : 1;

$stmt = $pdo->prepare("
  SELECT p.id AS posicao_id, p.coluna,
         GROUP_CONCAT(CONCAT(o.slot,':',c.codigo) ORDER BY o.slot SEPARATOR '|') AS slots
  FROM posicao p
  LEFT JOIN ocupacao o ON o.posicao_id = p.id
  LEFT JOIN caixa c ON c.id = o.caixa_id
  WHERE p.rua = ? AND p.prateleira = ?
  GROUP BY p.id, p.coluna
  ORDER BY p.coluna ASC
");
$stmt->execute([$rua, $prateleira]);
$posicoes = $stmt->fetchAll();

function slotsArray($s) {
  $arr = ['1'=>null,'2'=>null,'3'=>null];
  if (!$s) return $arr;
  foreach (explode('|', $s) as $part) {
    if ($part === '') continue;
    [$slot,$codigo] = explode(':', $part);
    $arr[$slot] = $codigo;
  }
  return $arr;
}
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title>Estoque — Rua <?= htmlspecialchars($rua) ?> · Prateleira <?= htmlspecialchars($prateleira) ?></title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="header">
    <h2>Rua <span class="badge"><?= htmlspecialchars($rua) ?></span> · Prateleira <span class="badge"><?= htmlspecialchars($prateleira) ?></span></h2>
  </div>

  <div class="controls">
    <form method="get" id="navForm">
      <label>Rua:
        <select name="rua" onchange="this.form.submit()">
          <?php for ($r=1;$r<=14;$r++): ?>
            <option value="<?= $r ?>" <?= $r===$rua?'selected':'' ?>><?= $r ?></option>
          <?php endfor; ?>
        </select>
      </label>
      <label>Prateleira:
        <select name="prateleira" onchange="this.form.submit()">
          <?php for ($p=1;$p<=4;$p++): ?>
            <option value="<?= $p ?>" <?= $p===$prateleira?'selected':'' ?>><?= $p ?></option>
          <?php endfor; ?>
        </select>
      </label>
      <a href="nova_caixa.php"><button type="button" class="primary">Nova caixa</button></a>
    </form>

    <form id="formBusca" style="display:flex; gap:8px; align-items:center;">
      <input name="rua" type="number" min="1" max="14" placeholder="Rua" value="<?= $rua ?>" required>
      <input name="coluna" type="number" min="1" max="18" placeholder="Coluna" required>
      <input name="linha" type="number" min="1" max="3" placeholder="Linha" required>
      <button type="submit" class="primary">Ir</button>
    </form>

    <form id="formCodigo" style="display:flex; gap:8px; align-items:center;">
      <input name="codigo" placeholder="Código da caixa">
      <button class="primary" type="submit">Localizar</button>
    </form>
  </div>

  <div class="viewport" id="viewport">
    <div class="scene" id="scene">
      <div class="grid-prateleira">
        <?php
          $byCol = [];
          foreach ($posicoes as $p) $byCol[(int)$p['coluna']] = $p;
          for ($col=1; $col<=18; $col++):
            $p = $byCol[$col] ?? null;
            $slots = $p ? slotsArray($p['slots']) : ['1'=>null,'2'=>null,'3'=>null];
            $vazia = !$slots['1'] && !$slots['2'] && !$slots['3'];
            $codigoPos = sprintf('R%02d-%02d', $rua, $col);
        ?>
          <div class="celula <?= $vazia?'vazia':'' ?>"
               id="<?= $codigoPos ?>"
               data-rua="<?= $rua ?>"
               data-prateleira="<?= $prateleira ?>"
               data-coluna="<?= $col ?>">
            <?php for ($s=1;$s<=3;$s++): ?>
              <?php if ($slots[(string)$s]): ?>
                <div class="slot" data-slot="<?= $s ?>" title="<?= $codigoPos ?>-<?= sprintf('%02d',$s) ?>">
                  <?= htmlspecialchars($slots[(string)$s]) ?>
                </div>
              <?php endif; ?>
            <?php endfor; ?>
            <?php if ($vazia): ?>
              <div style="font-size:12px;">Col <?= $col ?> · vazia</div>
            <?php endif; ?>
            <div class="foot">
              <span>Col <?= $col ?></span>
              <?php if ($p): ?>
              <div class="actions">
                <a href="alocar.php?pos=<?= $p['posicao_id'] ?>"><button type="button">Alocar</button></a>
                <a href="gerir_coluna.php?pos=<?= $p['posicao_id'] ?>"><button type="button">Gerir</button></a>
              </div>
              <?php endif; ?>
            </div>
          </div>
        <?php endfor; ?>
      </div>
    </div>
  </div>

  <script>
    const viewport = document.getElementById('viewport');
    const scene = document.getElementById('scene');
    const formBusca = document.getElementById('formBusca');
    const formCodigo = document.getElementById('formCodigo');

    const wait = (ms) => new Promise(r => setTimeout(r, ms));

    function fmt(rua, coluna, linha) {
      return `R${String(rua).padStart(2,'0')}-${String(coluna).padStart(2,'0')}-${String(linha).padStart(2,'0')}`;
    }

    async function cameraTo(rua, coluna, linha) {
      const colId = `R${String(rua).padStart(2,'0')}-${String(coluna).padStart(2,'0')}`;
      const cel = document.getElementById(colId);
      if (!cel) return;

      document.querySelectorAll('.celula.destino').forEach(n => n.classList.remove('destino'));

      scene.style.transition = 'transform 350ms ease';
      scene.style.scale = '0.9';
      scene.style.translate = '0 0';
      await wait(380);

      cel.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'center' });

      await wait(650);

      scene.style.transition = 'transform 600ms ease';
      scene.style.scale = '1.15';
      await wait(620);

      cel.classList.add('destino');

      let label = document.querySelector('.label-pos');
      if (!label) {
        label = document.createElement('div');
        label.className = 'label-pos';
        viewport.appendChild(label);
      }
      label.textContent = fmt(rua, coluna, linha);

      const rect = cel.getBoundingClientRect();
      const vpRect = viewport.getBoundingClientRect();
      const cx = rect.left + rect.width/2 - vpRect.left + viewport.scrollLeft;
      const cy = rect.top + 4 - vpRect.top + viewport.scrollTop;
      label.style.left = `${cx}px`;
      label.style.top = `${cy}px`;

      setTimeout(() => {
        scene.style.scale = '1';
      }, 2500);
    }

    formBusca.addEventListener('submit', (e) => {
      e.preventDefault();
      const rua = parseInt(formBusca.rua.value, 10);
      const coluna = parseInt(formBusca.coluna.value, 10);
      const linha = parseInt(formBusca.linha.value, 10);
      cameraTo(rua, coluna, linha);
    });

    formCodigo.addEventListener('submit', async (e) => {
      e.preventDefault();
      const codigo = e.target.codigo.value.trim();
      if (!codigo) return;
      const res = await fetch('buscar_posicao.php?codigo=' + encodeURIComponent(codigo));
      const pos = await res.json();
      if (pos && pos.rua) {
        // se a rua/prateleira diferirem da atual, recarrega a página nessa visão e depois anima
        const atualRua = <?= (int)$rua ?>;
        const atualPrat = <?= (int)$prateleira ?>;
        if (pos.rua != atualRua || pos.prateleira != atualPrat) {
          window.location.href = `index.php?rua=${pos.rua}&prateleira=${pos.prateleira}&go=${pos.rua}-${pos.coluna}-${pos.slot}`;
        } else {
          cameraTo(pos.rua, pos.coluna, pos.slot);
        }
      } else {
        alert('Caixa não encontrada.');
      }
    });

    // Se veio com parâmetro go na URL, anima após load
    (function initFromURL(){
      const params = new URLSearchParams(window.location.search);
      const go = params.get('go'); // formato: rua-coluna-slot
      if (!go) return;
      const [r,c,s] = go.split('-').map(Number);
      setTimeout(() => cameraTo(r,c,s), 400);
    })();

    // Clique em célula também pilota a câmera
    document.addEventListener('click', (ev) => {
      const cel = ev.target.closest('.celula');
      if (!cel) return;
      const rua = parseInt(cel.dataset.rua, 10);
      const coluna = parseInt(cel.dataset.coluna, 10);
      cameraTo(rua, coluna, 1);
    });
  </script>
</body>
</html>

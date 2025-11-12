<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title>Nova caixa</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <h2>Cadastrar nova caixa</h2>
  <form method="post" action="salvar_caixa.php" class="controls">
    <label>Código: <input name="codigo" required></label>
    <label>Descrição: <input name="descricao"></label>
    <label>Quantidade: <input type="number" name="quantidade" min="1" value="1" required></label>
    <button class="primary">Salvar</button>
    <a href="index.php"><button type="button">Voltar</button></a>
  </form>
</body>
</html>

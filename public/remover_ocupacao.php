<?php
require __DIR__ . '/db.php';
$ocupacao_id = (int)($_POST['ocupacao_id'] ?? 0);
if ($ocupacao_id<=0) { http_response_code(400); exit('Inválido'); }
$pdo->prepare("DELETE FROM ocupacao WHERE id=?")->execute([$ocupacao_id]);
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));

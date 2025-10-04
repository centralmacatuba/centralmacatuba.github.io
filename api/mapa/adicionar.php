<?php
// mapa/adicionar.php
header('Content-Type: application/json');
include(__DIR__ . "/../config/db_mapa.php");

$SECRET_KEY = 'MACATUBAAPI-mapaSEGREDO';
$input = json_decode(file_get_contents('php://input'), true);

if(!isset($input['segredo']) || $input['segredo'] !== $SECRET_KEY){
    http_response_code(401);
    echo json_encode(['status'=>'erro','mensagem'=>'Segredo inválido']);
    exit;
}

$nome = $input['nome'] ?? '';
$tipo = $input['tipo'] ?? '';
$latitude = $input['latitude'] ?? '';
$longitude = $input['longitude'] ?? '';
$criador = $input['criador'] ?? '';
$descricao = $input['descricao'] ?? '';
$verificado = $input['verificado'] ?? 'nao';
$feito = $input['feito'] ?? null;

if(!$nome || !$tipo || !$latitude || !$longitude || !$criador){
    echo json_encode(['status'=>'erro','mensagem'=>'Campos obrigatórios não preenchidos']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO pontos (nome, tipo, latitude, longitude, criador, descricao, verificado, feito) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssddssss", $nome, $tipo, $latitude, $longitude, $criador, $descricao, $verificado, $feito);

if($stmt->execute()){
    echo json_encode(['status'=>'sucesso','mensagem'=>'Ponto adicionado','id'=>$conn->insert_id]);
} else {
    echo json_encode(['status'=>'erro','mensagem'=>'Erro: '.$conn->error]);
}
?>
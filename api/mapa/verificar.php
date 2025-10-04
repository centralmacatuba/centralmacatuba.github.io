<?php
// mapa/verificar.php
header('Content-Type: application/json');
include(__DIR__ . "/../config/db_mapa.php");

$SECRET_KEY = 'MACATUBAAPI-mapaSEGREDO';
$segredo = $_GET['segredo'] ?? '';
if($segredo !== $SECRET_KEY){
    http_response_code(401);
    echo json_encode(['status'=>'erro','mensagem'=>'Segredo inválido']);
    exit;
}

$sql = "SELECT * FROM pontos ORDER BY data_criacao DESC";
$result = $conn->query($sql);
$dados = [];
while($row = $result->fetch_assoc()){
    $dados[] = $row;
}
echo json_encode(['status'=>'sucesso','cidade'=>'Macatuba','dados'=>$dados], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
?>
<?php
// mapa/pesquisar.php
header("Content-Type: application/json; charset=UTF-8");
include(__DIR__ . "/../config/db_mapa.php");

$SECRET_KEY = "MACATUBAAPI-mapaSEGREDO";
$segredo = $_REQUEST['segredo'] ?? "";
$q       = trim($_REQUEST['q'] ?? "");
$filtro  = $_REQUEST['filtro'] ?? "tudo";

if ($segredo !== $SECRET_KEY) {
    echo json_encode(["erro" => "Acesso negado"]);
    exit;
}
if ($q === "") {
    echo json_encode(["erro" => "Parâmetro de pesquisa vazio"]);
    exit;
}

switch ($filtro) {
    case "nome":
        $sql = "SELECT * FROM pontos WHERE nome LIKE ?";
        break;
    case "tipo":
        $sql = "SELECT * FROM pontos WHERE tipo LIKE ?";
        break;
    case "criador":
        $sql = "SELECT * FROM pontos WHERE criador LIKE ?";
        break;
    case "descricao":
        $sql = "SELECT * FROM pontos WHERE descricao LIKE ?";
        break;
    default:
        $sql = "SELECT * FROM pontos WHERE nome LIKE ? OR tipo LIKE ? OR criador LIKE ? OR descricao LIKE ?";
}

$stmt = $conn->prepare($sql);
$like = "%$q%";
if ($filtro === "tudo") {
    $stmt->bind_param("ssss", $like, $like, $like, $like);
} else {
    $stmt->bind_param("s", $like);
}
$stmt->execute();
$res = $stmt->get_result();

$resultados = [];
while ($row = $res->fetch_assoc()) {
    $resultados[] = $row;
}

echo json_encode([
    "sucesso" => true,
    "pesquisa" => $q,
    "filtro" => $filtro,
    "quantidade" => count($resultados),
    "resultados" => $resultados
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
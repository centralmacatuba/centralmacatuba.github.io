<?php
// jornal/pesquisar.php
header("Content-Type: application/json; charset=UTF-8");
include(__DIR__ . "/../config/db_jornal.php");

$SECRET_KEY = "MACATUBAAPI-jornalSEGREDO";
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
    case "criador":
        $sql = "SELECT * FROM noticias WHERE criador LIKE ?";
        break;
    case "titulo":
        $sql = "SELECT * FROM noticias WHERE titulo LIKE ?";
        break;
    case "subtitulo":
        $sql = "SELECT * FROM noticias WHERE subtitulo LIKE ?";
        break;
    case "conteudo":
        $sql = "SELECT * FROM noticias WHERE conteudo LIKE ?";
        break;
    default:
        $sql = "SELECT * FROM noticias WHERE criador LIKE ? OR titulo LIKE ? OR subtitulo LIKE ? OR conteudo LIKE ?";
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
    $resultados[] = [
        "id"        => $row["id"],
        "titulo"    => $row["titulo"],
        "subtitulo" => $row["subtitulo"],
        "conteudo"  => $row["conteudo"],
        "criador"   => $row["criador"],
        "data"      => $row["data_criacao"],
        "feito"     => $row["feito"] ?? null,
        "verificado"=> $row["verificado"] ?? null
    ];
}

echo json_encode([
    "sucesso" => true,
    "pesquisa" => $q,
    "filtro" => $filtro,
    "quantidade" => count($resultados),
    "resultados" => $resultados
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
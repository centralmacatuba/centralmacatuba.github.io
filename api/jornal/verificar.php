<?php
// jornal/verificar.php
header("Content-Type: application/json; charset=UTF-8");
include(__DIR__ . "/../config/db_jornal.php");

$SECRET_KEY = "MACATUBAAPI-jornalSEGREDO";
$headers = getallheaders();
$segredo = $headers["X-API-KEY"] ?? "";
if ($segredo !== $SECRET_KEY) {
    echo json_encode(["status" => "erro", "mensagem" => "Segredo inválido"]);
    exit;
}

$funcao = $_GET["funcao"] ?? "todas";
$id     = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

function adicionarSelos($noticia) {
    return array_merge($noticia, [
        'feito' => $noticia['feito'] ?? '',
        'verificado' => $noticia['verificado'] ?? ''
    ]);
}

if ($funcao === "uma" && $id > 0) {
    $stmt = $conn->prepare("SELECT id, titulo, subtitulo, conteudo, data_publicacao, feito, verificado FROM noticias WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $noticia = $result->fetch_assoc();
        echo json_encode(adicionarSelos($noticia), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    } else {
        echo json_encode(["status" => "erro", "mensagem" => "Notícia não encontrada"]);
    }
} else {
    $sql = "SELECT id, titulo, subtitulo, conteudo, data_publicacao, feito, verificado FROM noticias ORDER BY data_publicacao DESC";
    $result = $conn->query($sql);

    $noticias = [];
    while($row = $result->fetch_assoc()) {
        $noticias[] = adicionarSelos($row);
    }
    echo json_encode($noticias, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
?>

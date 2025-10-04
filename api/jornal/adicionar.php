<?php
// jornal/adicionar.php
header("Content-Type: application/json; charset=UTF-8");
include(__DIR__ . "/../config/db_jornal.php");

$SECRET_KEY = "MACATUBAAPI-jornalSEGREDO";
$segredo = $_POST["segredo"] ?? "";
if ($segredo !== $SECRET_KEY) {
    echo json_encode(["status" => "erro", "mensagem" => "Segredo inválido"]);
    exit;
}

$criador   = $_POST["criador"]   ?? "";
$id        = $_POST["id"]        ?? "";
$titulo    = $_POST["titulo"]    ?? "";
$subtitulo = $_POST["subtitulo"] ?? "";
$conteudo  = $_POST["conteudo"]  ?? "";
$feito      = $_POST["feito"] ?? null;
$verificado = $_POST["verificado"] ?? null;

if (empty($criador) || empty($id) || empty($titulo) || empty($conteudo)) {
    echo json_encode(["status" => "erro", "mensagem" => "Campos obrigatórios não preenchidos"]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO noticias (criador, id, titulo, subtitulo, conteudo, feito, verificado) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sisssss", $criador, $id, $titulo, $subtitulo, $conteudo, $feito, $verificado);

if ($stmt->execute()) {
    echo json_encode(["status" => "sucesso", "mensagem" => "Notícia adicionada", "id" => $conn->insert_id]);
} else {
    echo json_encode(["status" => "erro", "mensagem" => "Erro ao inserir: " . $conn->error]);
}
?>
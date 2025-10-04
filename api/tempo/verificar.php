<?php
// tempo/verificar.php
header('Content-Type: application/json');

$envPath = '/etc/macatuba/.env';
if (!file_exists($envPath)) {
    error_log("db_tempo.php: arquivo .env não encontrado em $envPath");
    http_response_code(500);
    die(json_encode(['status'=>'erro','mensagem'=>'Erro interno de configuração']));
}
$env = parse_ini_file($envPath);
if ($env === false) {
    error_log("db_tempo.php: falha ao ler $envPath");
    http_response_code(500);
    die(json_encode(['status'=>'erro','mensagem'=>'Erro interno de configuração']));
}

$SECRET_KEY = $env['API_KEY_TEMPO'] ?? 'MACATUBAAPI-tempoSEGREDO';
$host = $env['DB_TEMPO_HOST'] ?? 'localhost';
$user = $env['DB_TEMPO_USER'] ?? '';
$pass = $env['DB_TEMPO_PASS'] ?? '';
$db   = $env['DB_TEMPO_NAME'] ?? 'tempoEE';

$segredo = $_GET['segredo'] ?? '';
if ($segredo !== $SECRET_KEY) {
    http_response_code(401);
    echo json_encode(['status'=>'erro','mensagem'=>'Segredo inválido']);
    exit();
}

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['status'=>'erro','mensagem'=>'Falha na conexão com o banco']);
    exit();
}

$source = $_GET['source'] ?? null;
$start  = $_GET['start'] ?? null;
$end    = $_GET['end'] ?? null;

$sql = "SELECT * FROM tempoEE";
$where = [];
if ($source) $where[] = "source='". $conn->real_escape_string($source) ."'";
if ($start)  $where[] = "timestamp >= '". $conn->real_escape_string($start) ."'";
if ($end)    $where[] = "timestamp <= '". $conn->real_escape_string($end) ."'";
if (count($where) > 0) $sql .= " WHERE ".implode(" AND ", $where);
$sql .= " ORDER BY timestamp DESC";

$result = $conn->query($sql);
$dados = [];
while ($row = $result->fetch_assoc()) { $dados[] = $row; }

echo json_encode(['status'=>'sucesso','cidade'=>'Macatuba','dados'=>$dados], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
$conn->close();
?>
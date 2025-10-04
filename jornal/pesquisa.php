<?php
$apiUrl = "https://macatuba.eliasempresas.com/api/jornal/pesquisar.php";
$apiKey = "MACATUBAAPI-jornalSEGREDO";

$q = isset($_GET['q']) ? trim($_GET['q']) : "";
$campo = isset($_GET['campo']) ? $_GET['campo'] : "tudo";

$resultados = null;

if($q !== ""){
    $params = http_build_query([
        'q' => $q,
        'filtro' => $campo
    ]);
    $opts = ['http'=>[
        'header'=>"X-API-KEY: $apiKey\r\n"
    ]];
    $json = file_get_contents("$apiUrl?$params", false, stream_context_create($opts));
    $resultados = json_decode($json, true);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pesquisa - Jornal Macatuba</title>
<link rel="stylesheet" href="https://macatuba.eliasempresas.com/assets/jornal/styles/style.css">
<link rel="stylesheet" href="https://macatuba.eliasempresas.com/assets/styles/footer.css">
<style>
.pesquisa-container { max-width:900px; margin:30px auto; padding:20px; background:#fff; border-radius:8px; }
.resultado { border-bottom:1px solid #ddd; padding:15px 0; display:flex; justify-content:space-between; align-items:center; }
.resultado h3 a { color:#f50000; text-decoration:none; }
.resultado h3 a:hover { text-decoration:underline; }
.resultado p { color:#555; }
.selo { height:20px; margin-left:5px; vertical-align:middle; }
</style>
</head>
<body>
<header>
<div class="logo"><h1><a href="index.html">Jornal Macatuba</a></h1></div>

<section class="pesquisa">
<form action="pesquisa.php" method="get" class="pesquisa-form">
  <input type="text" name="q" placeholder="Pesquisar notícias..." value="<?= htmlspecialchars($q) ?>" required>
  <select name="campo">
    <option value="tudo" <?= $campo==="tudo"?"selected":"" ?>>Tudo</option>
    <option value="titulo" <?= $campo==="titulo"?"selected":"" ?>>Título</option>
    <option value="subtitulo" <?= $campo==="subtitulo"?"selected":"" ?>>Subtítulo</option>
    <option value="conteudo" <?= $campo==="conteudo"?"selected":"" ?>>Texto</option>
    <option value="criador" <?= $campo==="criador"?"selected":"" ?>>Criador</option>
  </select>
  <button type="submit">Pesquisar</button>
</form>
</section>

<nav>
<ul>
<li><a href="index.html">Início</a></li>
<li><a href="noticias.php">Notícias</a></li>
<li><a href="enviar.php">Enviar Notícia</a></li>
<li><a href="sobre.html">Sobre</a></li>
<li><a href="contato.html">Contato</a></li>
</ul>
</nav>

<main>
<div class="pesquisa-container">
<?php if($q !== ""): ?>
  <h2>Resultados para: "<?= htmlspecialchars($q) ?>"</h2>
  <?php if($resultados && isset($resultados["resultados"]) && count($resultados["resultados"])>0): ?>
    <?php foreach($resultados["resultados"] as $row): ?>
      <div class="resultado" data-id="<?= $row['id'] ?>">
        <h3>
          <a href="noticias.php?id=<?= $row['id'] ?>"><?= htmlspecialchars($row['titulo']) ?></a>
          <?php if(!empty($row['verificado'])): ?>
            <img src="https://macatuba.eliasempresas.com/ARQUIVOS/IMAGENS/SELO/VERIFICADO/<?= $row['verificado'] ?>.svg" class="selo">
          <?php endif; ?>
          <?php if(!empty($row['feito'])): ?>
            <img src="https://macatuba.eliasempresas.com/ARQUIVOS/IMAGENS/SELO/FEITO/<?= $row['feito'] ?>.svg" class="selo">
          <?php endif; ?>
        </h3>
        <p><em><?= htmlspecialchars($row['subtitulo']) ?></em></p>
        <p><?= htmlspecialchars(mb_strimwidth(strip_tags($row['texto']),0,200,"...")) ?></p>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p>Nenhuma notícia encontrada.</p>
  <?php endif; ?>
<?php else: ?>
  <h2>Digite um termo para pesquisar</h2>
<?php endif; ?>
</div>
</main>

<footer>
<?php echo file_get_contents('https://macatuba.eliasempresas.com/assets/footer.php'); ?>
</footer>

<script>
// --- Script de listas ---
const STORAGE_KEY = "jornal_macatuba";
function getStorage(){ return JSON.parse(localStorage.getItem(STORAGE_KEY)||'{"listas":{}}'); }
function saveStorage(data){ localStorage.setItem(STORAGE_KEY,JSON.stringify(data)); }
function adicionarListaItem(noticia){
    const data=getStorage(); 
    let listaNome=prompt("Digite o nome da lista para salvar a notícia:"); 
    if(!listaNome) return;
    if(!data.listas[listaNome]) data.listas[listaNome]=[];
    if(!data.listas[listaNome].some(i=>i.id==noticia.id)){ 
        data.listas[listaNome].push(noticia); 
        saveStorage(data); 
        alert("Notícia adicionada à lista "+listaNome);
    } else alert("Notícia já está na lista");
}
function adicionarBotoesSalvar(){
    document.querySelectorAll(".resultado").forEach(article=>{
        if(article.querySelector(".btn-salvar")) return;
        const btn=document.createElement("button");
        btn.innerText="Salvar na Lista";
        btn.className="btn-salvar";
        btn.onclick=()=>{
            const noticia={
                id: article.dataset.id,
                titulo: article.querySelector("h3 a")?.innerText||"",
                subtitulo: article.querySelector("em")?.innerText||"",
                criador: "",
                data: ""
            };
            adicionarListaItem(noticia);
        };
        article.appendChild(btn);
    });
}
document.addEventListener("DOMContentLoaded",()=>{adicionarBotoesSalvar()});
</script>

</body>
</html>

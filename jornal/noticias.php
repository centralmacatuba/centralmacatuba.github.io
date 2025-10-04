<?php
$apiUrl = "https://macatuba.eliasempresas.com/api/jornal/verificar.php";
$apiKey = "MACATUBAAPI-jornalSEGREDO";

$id = isset($_GET['id']) ? intval($_GET['id']) : null;

$url = $id ? "$apiUrl?funcao=uma&id=$id" : "$apiUrl?funcao=todas";
$opts = ['http'=>['header'=>"X-API-KEY: $apiKey\r\n"]];
$response = file_get_contents($url, false, stream_context_create($opts));
$dados = json_decode($response, true);

function seloNoticia($noticia){ 
    $base="https://macatuba.eliasempresas.com/ARQUIVOS/IMAGENS/SELO/";
    $selos=[]; 
    if(!empty($noticia['verificado'])) $selos[] = ['url'=>$base."VERIFICADO/".$noticia['verificado'].".svg", 'tooltip'=>"Verificado"];
    if(!empty($noticia['feito'])) $selos[] = ['url'=>$base."FEITO/".$noticia['feito'].".svg", 'tooltip'=>"Feito"];
    return $selos;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Notícias - Jornal Macatuba</title>
<link rel="stylesheet" href="https://macatuba.eliasempresas.com/assets/jornal/styles/style.css">
<link rel="stylesheet" href="https://macatuba.eliasempresas.com/assets/styles/footer.css">
</head>
<body>
<header>
<div class="logo"><h1><a href="index.html">Jornal Macatuba</a></h1></div>
<nav>
<ul>
<li><a href="index.html">Início</a></li>
<li><a href="noticias.php">Notícias</a></li>
<li><a href="enviar.php">Enviar Notícia</a></li>
<li><a href="sobre.html">Sobre</a></li>
<li><a href="contato.html">Contato</a></li>
</ul>
</nav>
</header>

<main>
<?php if($id && isset($dados['id'])): ?>
<article data-id="<?= $dados['id'] ?>">
<h2><?= htmlspecialchars($dados['titulo']) ?></h2>
<h4><?= htmlspecialchars($dados['subtitulo']) ?></h4>
<p><em>Por <?= htmlspecialchars($dados['criador']) ?> em <?= htmlspecialchars($dados['data']) ?></em></p>
<?php foreach(seloNoticia($dados) as $selo): ?>
<img src="<?= $selo['url'] ?>" title="<?= $selo['tooltip'] ?>" style="height:24px; margin-right:5px;">
<?php endforeach; ?>
<div><?= $dados['texto'] ?></div>
<br>
<a href="noticias.php">← Voltar para todas as notícias</a>
</article>
<?php elseif(is_array($dados)): ?>
<section id="lista-noticias">
<?php foreach($dados as $noticia): ?>
<article data-id="<?= $noticia['id'] ?>">
<h3><a href="noticias.php?id=<?= $noticia['id'] ?>"><?= htmlspecialchars($noticia['titulo']) ?></a></h3>
<h4><?= htmlspecialchars($noticia['subtitulo']) ?></h4>
<p><em>Por <?= htmlspecialchars($noticia['criador']) ?> em <?= htmlspecialchars($noticia['data']) ?></em></p>
<?php foreach(seloNoticia($noticia) as $selo): ?>
<img src="<?= $selo['url'] ?>" title="<?= $selo['tooltip'] ?>" style="height:16px; margin-right:3px;">
<?php endforeach; ?>
<p><?= mb_strimwidth(strip_tags($noticia['texto']),0,150,"...") ?></p>
<a href="noticias.php?id=<?= $noticia['id'] ?>">Leia mais</a>
</article>
<?php endforeach; ?>
</section>
<?php else: ?>
<p>Erro ao carregar notícias.</p>
<?php endif; ?>
</main>

<footer>
<?php echo file_get_contents('https://macatuba.eliasempresas.com/assets/footer.php'); ?>
</footer>

<script>
// --- Script de listas (mesmo do index.html) ---
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
    document.querySelectorAll("article").forEach(article=>{
        if(article.querySelector(".btn-salvar")) return;
        const btn=document.createElement("button");
        btn.innerText="Salvar na Lista";
        btn.className="btn-salvar";
        btn.onclick=()=>{
            const noticia={
                id: article.dataset.id,
                titulo: article.querySelector("h2, h3")?.innerText||"",
                subtitulo: article.querySelector("h4")?.innerText||"",
                criador: article.querySelector("em")?.innerText||"",
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


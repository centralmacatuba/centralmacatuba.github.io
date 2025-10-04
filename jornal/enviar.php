<?php
$apiUrl = "https://macatuba.eliasempresas.com/api/jornal/adicionar.php";
$apiKey = "MACATUBAAPI-jornalSEGREDO";
$mensagem = "";

if($_SERVER["REQUEST_METHOD"]==="POST"){
    $dados = [
        'titulo'=>$_POST['titulo']??"",
        'subtitulo'=>$_POST['subtitulo']??"",
        'texto'=>$_POST['texto']??"",
        'autor'=>$_POST['autor']??"Anônimo"
    ];

    $opts=['http'=>[
        'method'=>'POST',
        'header'=>"X-API-KEY: $apiKey\r\nContent-type: application/x-www-form-urlencoded\r\n",
        'content'=>http_build_query($dados)
    ]];

    $res = file_get_contents($apiUrl, false, stream_context_create($opts));
    $mensagem = $res ? "Notícia enviada com sucesso!" : "Erro ao enviar notícia.";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Enviar Notícia - Jornal Macatuba</title>
<link rel="stylesheet" href="https://macatuba.eliasempresas.com/assets/jornal/styles/style.css">
<link rel="stylesheet" href="https://macatuba.eliasempresas.com/assets/styles/footer.css">
<style>
.form-container { max-width:800px; margin:30px auto; padding:20px; background:#fff; border-radius:8px; }
.form-container h2 { margin-bottom:20px; color:#f50000; text-align:center; }
.form-container input, .form-container textarea { width:100%; padding:10px; margin:8px 0; border:1px solid #ccc; border-radius:6px; }
.form-container button { background:#f50000; color:#fff; padding:12px 20px; border:none; border-radius:6px; cursor:pointer; }
.form-container button:hover { background:#d40000; }
.mensagem { text-align:center; margin-bottom:20px; font-weight:bold; }
</style>
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
<div class="form-container">
<h2>Envie sua Notícia</h2>
<?php if($mensagem): ?><p class="mensagem"><?= htmlspecialchars($mensagem) ?></p><?php endif; ?>
<form method="POST" action="enviar.php">
<input type="text" name="autor" placeholder="Seu nome (opcional)">
<input type="text" name="titulo" placeholder="Título da notícia" required>
<input type="text" name="subtitulo" placeholder="Subtítulo (opcional)">
<textarea name="texto" rows="8" placeholder="Escreva sua notícia aqui..." required></textarea>
<button type="submit">Enviar Notícia</button>
</form>
</div>
</main>

<footer>
<?php echo file_get_contents('https://macatuba.eliasempresas.com/assets/footer.php'); ?>
</footer>

</body>
</html>
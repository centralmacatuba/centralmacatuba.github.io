<?php
function climaIcon($condicao) {
    $cond = strtolower($condicao);
    if(strpos($cond,'sol')!==false) return '☀️';
    if(strpos($cond,'nublado')!==false) return '☁️';
    if(strpos($cond,'chuva')!==false) return '🌧️';
    if(strpos($cond,'trovoada')!==false) return '⛈️';
    if(strpos($cond,'neve')!==false) return '❄️';
    return '🌡️';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tempo Macatuba</title>
<link rel="stylesheet" href="https://macatuba.eliasempresas.com/assets/styles/footer.css">
<style>
body { font-family: Arial, sans-serif; margin:0; padding:0; background:#f0f0f0; }
header { background:#f50000; color:#fff; padding:20px; text-align:center; }
header h1 { margin:0; font-size:28px; }
nav ul { list-style:none; display:flex; justify-content:center; padding:0; margin:10px 0 0 0; flex-wrap:wrap; }
nav ul li { margin:0 10px; }
nav ul li a { color:#fff; text-decoration:none; font-weight:bold; }
nav ul li a:hover { text-decoration:underline; }
main { max-width:1000px; margin:20px auto; padding:0 10px; display:grid; grid-template-columns: repeat(auto-fill,minmax(180px,1fr)); gap:15px; }
.tempo-card { background:#fff; border-radius:10px; padding:15px; text-align:center; box-shadow:0 3px 8px rgba(0,0,0,0.1); transition: transform 0.2s; }
.tempo-card:hover { transform: translateY(-3px); }
.tempo-card h3 { margin:5px 0; color:#f50000; font-size:16px; }
.tempo-card p { margin:3px 0; font-size:14px; }
.tempo-card .icon { font-size:28px; }
.btn-salvar { margin-top:5px; background:#007bff; color:#fff; border:none; padding:5px 10px; border-radius:4px; cursor:pointer; }
.btn-salvar:hover { background:#0056b3; }
</style>
</head>
<body>
<header>
<h1>Tempo Macatuba</h1>
<nav>
<ul>
<li><a href="index.php">Início</a></li>
<li><a href="sobre.html">Sobre</a></li>
</ul>
</nav>
</header>

<main id="tempo-container">
<p>Carregando dados...</p>
</main>

<script>
const API_KEY = "MACATUBAAPI-tempoSEGREDO";
const STORAGE_KEY = "tempo_macatuba";

// Funções para salvar em lista
function getStorage(){ return JSON.parse(localStorage.getItem(STORAGE_KEY)||'{"listas":{}}'); }
function saveStorage(data){ localStorage.setItem(STORAGE_KEY, JSON.stringify(data)); }
function adicionarListaItem(dado){
    const data = getStorage();
    let listaNome = prompt("Digite o nome da lista para salvar o dado do tempo:");
    if(!listaNome) return;
    if(!data.listas[listaNome]) data.listas[listaNome] = [];
    if(!data.listas[listaNome].some(i=>i.source==dado.source && i.timestamp==dado.timestamp)){
        data.listas[listaNome].push(dado);
        saveStorage(data);
        alert("Dado adicionado à lista "+listaNome);
    } else alert("Dado já está na lista");
}

// Função para carregar dados do tempo
async function carregarTempo(source=null, start=null, end=null) {
    const params = new URLSearchParams();
    if(source) params.append('source', source);
    if(start) params.append('start', start);
    if(end) params.append('end', end);

    try {
        const res = await fetch(`https://macatuba.eliasempresas.com/api/tempo/verificar.php?${params}`, { headers: { "X-API-KEY": API_KEY } });
        const data = await res.json();
        const container = document.getElementById('tempo-container');
        container.innerHTML = '';

        if(!data.dados || data.dados.length===0){ container.innerHTML='<p>Nenhum dado disponível no momento.</p>'; return; }

        data.dados.forEach(d => {
            const div = document.createElement('div');
            div.className='tempo-card';
            div.innerHTML = `
                <div class="icon">${climaIcon(d.condicao)}</div>
                <h3>${d.source}</h3>
                <p><strong>${d.timestamp}</strong></p>
                <p>🌡 ${d.temperatura}°C</p>
                <p>💧 ${d.humidade}%</p>
                <p>${d.condicao}</p>
                <button class="btn-salvar" onclick='adicionarListaItem(${JSON.stringify(d)})'>Salvar na Lista</button>
            `;
            container.appendChild(div);
        });
    } catch(err){ console.error(err); }
}

carregarTempo();

function climaIcon(condicao){
    condicao = condicao.toLowerCase();
    if(condicao.includes('sol')) return '☀️';
    if(condicao.includes('nublado')) return '☁️';
    if(condicao.includes('chuva')) return '🌧️';
    if(condicao.includes('trovoada')) return '⛈️';
    if(condicao.includes('neve')) return '❄️';
    return '🌡️';
}
</script>

<footer>
<?php echo file_get_contents('https://macatuba.eliasempresas.com/assets/footer.php'); ?>
</footer>

</body>
</html>


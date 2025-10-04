<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mapa Macatuba</title>
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<link rel="stylesheet" href="https://macatuba.eliasempresas.com/assets/styles/footer.css">
<style>
body, html { margin:0; padding:0; height:100%; font-family: Arial, sans-serif; }
#map { width: 100%; height: 60vh; }
.marker-popup { max-width: 250px; }
header { padding: 10px; background: #f50000; color: #fff; text-align:center; }
nav ul { list-style:none; display:flex; justify-content:center; gap:15px; padding:0; margin:10px 0; }
nav ul li a { color:#fff; text-decoration:none; font-weight:bold; }
nav ul li a:hover { text-decoration:underline; }
.form-container { max-width: 600px; margin: 20px auto; padding: 15px; background: #fff; border-radius: 8px; }
.form-container input, .form-container select, .form-container textarea, .form-container button {
  width: 100%; padding: 10px; margin-bottom: 10px; border-radius: 6px; border:1px solid #ccc; font-size: 14px; box-sizing:border-box;
}
.form-container button { background: #f50000; color: #fff; border:none; cursor:pointer; }
.form-container button:hover { background: #d40000; }
.btn-salvar { margin-top:5px; background:#007bff; color:#fff; border:none; padding:5px 10px; border-radius:4px; cursor:pointer; }
.btn-salvar:hover { background:#0056b3; }
</style>
</head>
<body>

<header>
  <h1>Mapa Macatuba</h1>
  <nav>
    <ul>
      <li><a href="index.php">Inicio</a></li>
      <li><a href="sobre.html">Sobre</a></li>
    </ul>
  </nav>
</header>

<div id="map"></div>

<div class="form-container">
  <h2>Adicionar novo ponto</h2>
  <form id="form-ponto">
    <input type="text" name="nome" placeholder="Nome do ponto" required>
    <input type="text" name="tipo" placeholder="Tipo (ex: restaurante, escola)" required>
    <textarea name="descricao" placeholder="Descrição (opcional)"></textarea>
    <input type="number" name="latitude" step="any" placeholder="Latitude" required>
    <input type="number" name="longitude" step="any" placeholder="Longitude" required>
    <input type="text" name="criador" placeholder="Seu nome" required>
    <button type="submit">Adicionar ponto</button>
  </form>
</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
const SECRET_KEY = "MACATUBAAPI-mapaSEGREDO";
const map = L.map('map').setView([-22.5420, -48.5420], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap', maxZoom: 19 }).addTo(map);

// --- Função de listas ---
const STORAGE_KEY = "mapa_macatuba";
function getStorage(){ return JSON.parse(localStorage.getItem(STORAGE_KEY)||'{"listas":{}}'); }
function saveStorage(data){ localStorage.setItem(STORAGE_KEY,JSON.stringify(data)); }
function adicionarListaItem(ponto){
    const data = getStorage();
    let listaNome = prompt("Digite o nome da lista para salvar o ponto:");
    if(!listaNome) return;
    if(!data.listas[listaNome]) data.listas[listaNome] = [];
    if(!data.listas[listaNome].some(i=>i.nome==ponto.nome && i.latitude==ponto.latitude && i.longitude==ponto.longitude)){
        data.listas[listaNome].push(ponto);
        saveStorage(data);
        alert("Ponto adicionado à lista "+listaNome);
    } else {
        alert("Ponto já está na lista");
    }
}

async function carregarPontos() {
    try {
        const res = await fetch('https://macatuba.eliasempresas.com/api/mapa/verificar.php', { headers: { "X-API-KEY": SECRET_KEY } });
        const data = await res.json();
        if(data.status !== 'sucesso') return;

        data.dados.forEach(p => {
            const marker = L.marker([p.latitude, p.longitude]).addTo(map);
            marker.bindPopup(`
                <div class="marker-popup">
                    <strong>${p.nome}</strong><br>
                    <em>${p.tipo}</em><br>
                    ${p.descricao ? p.descricao+'<br>' : ''}
                    Criado por: ${p.criador}<br>
                    Data: ${new Date(p.data_criacao).toLocaleString()}<br>
                    <button class="btn-salvar" onclick='adicionarListaItem(${JSON.stringify(p)})'>Salvar na Lista</button>
                </div>
            `);
        });
    } catch(err) { console.error(err); alert('Erro ao carregar pontos.'); }
}

document.getElementById('form-ponto').addEventListener('submit', async e => {
    e.preventDefault();
    const formData = new FormData(e.target);
    try {
        const res = await fetch('https://macatuba.eliasempresas.com/api/mapa/adicionar.php', { method:'POST', headers:{ "X-API-KEY": SECRET_KEY }, body: formData });
        const data = await res.json();
        if(data.status === 'sucesso') { alert('Ponto adicionado!'); e.target.reset(); carregarPontos(); }
        else alert('Erro: '+data.mensagem);
    } catch(err){ console.error(err); alert('Erro ao enviar ponto.'); }
});

carregarPontos();
</script>

<footer>
<?php echo file_get_contents('https://macatuba.eliasempresas.com/assets/footer.php'); ?>
</footer>

</body>
</html>

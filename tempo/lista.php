<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Listas - Tempo Macatuba</title>
<link rel="stylesheet" href="https://macatuba.eliasempresas.com/assets/styles/footer.css">
<style>
body { font-family: Arial, sans-serif; margin:0; padding:0; background:#f4f4f4; }
header { background:#f50000; color:#fff; padding:10px; text-align:center; }
nav ul { list-style:none; display:flex; justify-content:center; gap:15px; padding:0; margin:10px 0; }
nav ul li a { color:#fff; text-decoration:none; font-weight:bold; }
nav ul li a:hover { text-decoration:underline; }
.container { max-width:900px; margin:20px auto; padding:20px; background:#fff; border-radius:8px; }
.lista { border-bottom:1px solid #ddd; padding:10px 0; }
.lista h3 { margin:0 0 5px 0; }
.lista button { margin-right:5px; margin-bottom:5px; }
</style>
</head>
<body>

<header>
<h1>Listas - Tempo Macatuba</h1>
<nav>
<ul>
<li><a href="index.php">Tempo</a></li>
<li><a href="sobre.html">Sobre</a></li>
</ul>
</nav>
</header>

<div class="container">
<h2>Suas listas</h2>
<div id="listas-container">Carregando...</div>
<hr>
<button onclick="exportarTodas()">Exportar todas as listas</button>
<input type="file" id="importar-arquivo" accept=".json">
<button onclick="importarTodas()">Importar arquivo</button>
</div>

<script>
const STORAGE_KEY = "tempo_macatuba";

function getStorage() {
    return JSON.parse(localStorage.getItem(STORAGE_KEY)||'{"listas":{}}');
}

function saveStorage(data) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
}

function renderListas() {
    const data = getStorage();
    const container = document.getElementById("listas-container");
    container.innerHTML = "";

    const listas = data.listas;
    if(Object.keys(listas).length === 0){
        container.innerHTML = "<p>Nenhuma lista criada.</p>";
        return;
    }

    Object.keys(listas).forEach(nome => {
        const div = document.createElement("div");
        div.className = "lista";
        div.innerHTML = `<h3>${nome} (${listas[nome].length} itens)</h3>`;
        
        listas[nome].forEach((item, i)=>{
            const p = document.createElement("p");
            p.innerHTML = `${item.source} - ${item.timestamp} - 🌡 ${item.temperatura}°C <button onclick='removerItem("${nome}",${i})'>Remover</button>`;
            div.appendChild(p);
        });

        const btnExport = document.createElement("button");
        btnExport.textContent = "Exportar lista";
        btnExport.onclick = ()=>exportarLista(nome);
        div.appendChild(btnExport);

        container.appendChild(div);
    });
}

function removerItem(listaNome, index) {
    const data = getStorage();
    data.listas[listaNome].splice(index, 1);
    saveStorage(data);
    renderListas();
}

function exportarLista(nome) {
    const data = getStorage();
    const blob = new Blob([JSON.stringify({[nome]: data.listas[nome]}, null, 2)], {type:'application/json'});
    const a = document.createElement("a");
    a.href = URL.createObjectURL(blob);
    a.download = `${nome}.json`;
    a.click();
}

function exportarTodas() {
    const data = getStorage();
    const blob = new Blob([JSON.stringify(data, null, 2)], {type:'application/json'});
    const a = document.createElement("a");
    a.href = URL.createObjectURL(blob);
    a.download = `listas_tempo_macatuba.json`;
    a.click();
}

function importarTodas() {
    const input = document.getElementById("importar-arquivo");
    if(input.files.length === 0){ alert("Selecione um arquivo JSON para importar."); return; }
    const reader = new FileReader();
    reader.onload = e=>{
        try{
            const importData = JSON.parse(e.target.result);
            const data = getStorage();
            data.listas = {...data.listas, ...importData};
            saveStorage(data);
            renderListas();
            alert("Importação concluída.");
        } catch(err){ alert("Arquivo inválido."); console.error(err); }
    };
    reader.readAsText(input.files[0]);
}

document.addEventListener("DOMContentLoaded", renderListas);
</script>

<footer>
<?php echo file_get_contents('https://macatuba.eliasempresas.com/assets/footer.php'); ?>
</footer>

</body>
</html>
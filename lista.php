<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Listas - Central Macatuba</title>
<link rel="stylesheet" href="https://macatuba.eliasempresas.com/assets/styles/footer.css">
<style>
body { font-family: Arial, sans-serif; margin:0; padding:0; background:#f4f4f4; }
header { background:#f50000; color:#fff; padding:10px; text-align:center; }
nav ul { list-style:none; display:flex; justify-content:center; gap:15px; padding:0; margin:10px 0; flex-wrap:wrap; }
nav ul li a { color:#fff; text-decoration:none; font-weight:bold; }
nav ul li a:hover { text-decoration:underline; }
.container { max-width:1000px; margin:20px auto; padding:20px; background:#fff; border-radius:8px; }
.servico { margin-bottom:30px; }
.servico h2 { border-bottom:2px solid #f50000; padding-bottom:5px; }
.lista { border-bottom:1px solid #ddd; padding:10px 0; }
.lista h3 { margin:0 0 5px 0; }
.lista button { margin-right:5px; margin-bottom:5px; }
</style>
</head>
<body>

<header>
<h1>Listas - Central Macatuba</h1>
<nav>
<ul>
<li><a href="index.php">Início</a></li>
<li><a href="sobre.html">Sobre</a></li>
</ul>
</nav>
</header>

<div class="container" id="container-listas">
<p>Carregando listas de todos os serviços...</p>
<hr>
<button onclick="exportarTodas()">Exportar todas as listas</button>
<input type="file" id="importar-arquivo" accept=".json">
<button onclick="importarTodas()">Importar arquivo</button>
</div>

<script>
const SERVICOS = [
    {nome:"Jornal Macatuba", key:"jornal_macatuba"},
    {nome:"Mapa Macatuba", key:"mapa_macatuba"},
    {nome:"Tempo Macatuba", key:"tempo_macatuba"},
    {nome:"Informações Macatuba", key:"informacoes_macatuba"},
    {nome:"SOS Macatuba", key:"sos_macatuba"}
];

function getStorage(key){ return JSON.parse(localStorage.getItem(key)||'{"listas":{}}'); }
function saveStorage(key,data){ localStorage.setItem(key, JSON.stringify(data)); }

function renderListas() {
    const container = document.getElementById("container-listas");
    container.innerHTML = "";

    SERVICOS.forEach(servico=>{
        const data = getStorage(servico.key);
        const listas = data.listas;
        const divServ = document.createElement("div");
        divServ.className = "servico";
        divServ.innerHTML = `<h2>${servico.nome}</h2>`;

        if(Object.keys(listas).length===0){
            divServ.innerHTML += "<p>Nenhuma lista criada.</p>";
        } else {
            Object.keys(listas).forEach(nome=>{
                const divLista = document.createElement("div");
                divLista.className = "lista";
                divLista.innerHTML = `<h3>${nome} (${listas[nome].length} itens)</h3>`;
                
                listas[nome].forEach((item,i)=>{
                    const p = document.createElement("p");
                    p.textContent = JSON.stringify(item);
                    const btnRemove = document.createElement("button");
                    btnRemove.textContent = "Remover";
                    btnRemove.onclick = ()=>removerItem(servico.key,nome,i);
                    p.appendChild(btnRemove);
                    divLista.appendChild(p);
                });

                const btnExport = document.createElement("button");
                btnExport.textContent = "Exportar lista";
                btnExport.onclick = ()=>exportarLista(servico.key,nome);
                divLista.appendChild(btnExport);

                divServ.appendChild(divLista);
            });
        }

        container.appendChild(divServ);
    });
}

function removerItem(servicoKey, listaNome, index){
    const data = getStorage(servicoKey);
    data.listas[listaNome].splice(index,1);
    saveStorage(servicoKey,data);
    renderListas();
}

function exportarLista(servicoKey, listaNome){
    const data = getStorage(servicoKey);
    const blob = new Blob([JSON.stringify({[listaNome]: data.listas[listaNome]},null,2)],{type:'application/json'});
    const a = document.createElement("a");
    a.href = URL.createObjectURL(blob);
    a.download = `${servicoKey}_${listaNome}.json`;
    a.click();
}

function exportarTodas(){
    const todas = {};
    SERVICOS.forEach(servico=>{
        todas[servico.key] = getStorage(servico.key).listas;
    });
    const blob = new Blob([JSON.stringify(todas,null,2)],{type:'application/json'});
    const a = document.createElement("a");
    a.href = URL.createObjectURL(blob);
    a.download = `listas_central_macatuba.json`;
    a.click();
}

function importarTodas(){
    const input = document.getElementById("importar-arquivo");
    if(input.files.length===0){ alert("Selecione um arquivo JSON para importar."); return; }
    const reader = new FileReader();
    reader.onload = e=>{
        try{
            const importData = JSON.parse(e.target.result);
            SERVICOS.forEach(servico=>{
                if(importData[servico.key]){
                    const data = getStorage(servico.key);
                    data.listas = {...data.listas,...importData[servico.key]};
                    saveStorage(servico.key,data);
                }
            });
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
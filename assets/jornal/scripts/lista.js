<script>
const STORAGE_KEY = "jornal_macatuba";

function getStorage() {
    return JSON.parse(localStorage.getItem(STORAGE_KEY) || '{"listas":{}}');
}

function saveStorage(data) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
}

function adicionarListaItem(noticia){
    const data = getStorage();
    let listaNome = prompt("Digite o nome da lista para salvar a notícia:");
    if(!listaNome) return;
    if(!data.listas[listaNome]) data.listas[listaNome]=[];
    // Evitar duplicados
    if(!data.listas[listaNome].some(i=>i.id==noticia.id)){
        data.listas[listaNome].push(noticia);
        saveStorage(data);
        alert("Notícia adicionada à lista "+listaNome);
    } else {
        alert("Notícia já está na lista");
    }
}

// Função para adicionar botão de "Salvar para lista" em elementos de notícia
function adicionarBotoesSalvar(){
    document.querySelectorAll("article").forEach(article=>{
        if(article.querySelector(".btn-salvar")) return; // já existe
        const btn = document.createElement("button");
        btn.innerText = "Salvar na Lista";
        btn.className="btn-salvar";
        btn.onclick = ()=>{
            const noticia = {
                id: article.dataset.id || article.querySelector("a")?.href?.split("id=")[1],
                titulo: article.querySelector("h3")?.innerText || article.querySelector("h2")?.innerText,
                subtitulo: article.querySelector("h4")?.innerText || "",
                criador: article.querySelector("em")?.innerText || "",
                data: article.querySelector("small")?.innerText || ""
            };
            adicionarListaItem(noticia);
        };
        article.appendChild(btn);
    });
}

// Executar após carregar a página ou atualizar notícias dinamicamente
document.addEventListener("DOMContentLoaded",()=>{adicionarBotoesSalvar()});
</script>
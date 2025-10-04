// avisos.js
(async function () {
  const SEGREDO = "EEAPI-avisosEESEGREDO";

  // Carregar parser de Markdown (marked.js via CDN)
  const script = document.createElement("script");
  script.src = "https://cdn.jsdelivr.net/npm/marked/marked.min.js";
  document.head.appendChild(script);

  await new Promise(res => script.onload = res);

  // Avisos padrões (textos simples e fáceis de entender)
  const avisosPadroes = [
    "O servidor das **Elias Empresas** é reiniciado todos os dias entre **05:55 e 06:15** e também entre **15:55 e 16:15**. Durante esses horários os serviços podem ficar fora do ar.",
    "As **Elias Empresas** não são uma empresa de verdade. É apenas um apelido usado por uma pessoa que cria, hospeda e mantém todos os serviços."
  ];

  // Estilo global injetado
  const style = document.createElement("style");
  style.textContent = `
    .ee-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.65);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 9999;
    }
    .ee-box {
      background: #fff;
      padding: 24px 32px;
      border-radius: 16px;
      max-width: 600px;
      font-family: Arial, sans-serif;
      font-size: 16px;
      line-height: 1.5;
      color: #222;
      position: relative;
      box-shadow: 0 6px 20px rgba(0,0,0,0.25);
      animation: ee-fadein 0.3s ease-out;
    }
    .ee-box h1, .ee-box h2, .ee-box h3 { margin: 12px 0; }
    .ee-box p { margin: 8px 0; }
    .ee-box strong { color: #c00; }
    .ee-box a {
      color: #0645ad;
      text-decoration: underline;
    }
    .ee-box a:hover {
      color: #c00;
    }
    .ee-close {
      position: absolute;
      top: 10px;
      left: 12px;
      background: transparent;
      border: none;
      font-size: 22px;
      cursor: pointer;
      color: #444;
      transition: transform 0.2s;
    }
    .ee-close:hover {
      transform: scale(1.2);
      color: #000;
    }
    @keyframes ee-fadein {
      from { opacity: 0; transform: scale(0.9); }
      to { opacity: 1; transform: scale(1); }
    }
  `;
  document.head.appendChild(style);

  // Renderizar aviso (HTML + MD)
  function renderAviso(texto) {
    let html = "";
    try {
      html = marked.parse(texto);
    } catch {
      html = texto;
    }

    // Garantir que todos os links abram em nova aba
    const temp = document.createElement("div");
    temp.innerHTML = html;
    temp.querySelectorAll("a").forEach(a => {
      a.setAttribute("target", "_blank");
      a.setAttribute("rel", "noopener noreferrer");
    });
    return temp.innerHTML;
  }

  // Criar aviso
  function criarAviso(texto) {
    const overlay = document.createElement("div");
    overlay.className = "ee-overlay";

    const box = document.createElement("div");
    box.className = "ee-box";
    box.innerHTML = renderAviso(texto);

    const btnFechar = document.createElement("button");
    btnFechar.className = "ee-close";
    btnFechar.innerText = "✕";

    btnFechar.onclick = () => {
      document.body.removeChild(overlay);
      mostrarProximoAviso();
    };

    box.appendChild(btnFechar);
    overlay.appendChild(box);
    document.body.appendChild(overlay);
  }

  // Fila de avisos
  const filaAvisos = [];

  // Buscar avisos extras da API
  try {
    const resp = await fetch("https://api.eliasempresas.com/APIEE/avisosEE/verificar", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ segredo: SEGREDO })
    });

    if (resp.ok) {
      const dados = await resp.json();
      if (Array.isArray(dados?.avisos)) {
        filaAvisos.push(...dados.avisos); // vários avisos
      } else if (dados?.aviso) {
        filaAvisos.push(dados.aviso); // aviso único
      }
    }
  } catch (e) {
    console.error("Erro ao consultar API de avisos:", e);
  }

  // Depois os padrões
  filaAvisos.push(...avisosPadroes);

  // Controle da fila
  let indice = 0;
  function mostrarProximoAviso() {
    if (indice < filaAvisos.length) {
      criarAviso(filaAvisos[indice]);
      indice++;
    }
  }

  // Iniciar
  mostrarProximoAviso();
})();

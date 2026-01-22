# Documentação da API Pública da Central Macatuba

A **API Pública da Central Macatuba** oferece acesso programático a dados abertos e informações em tempo real sobre o município, seguindo a arquitetura modular e orientada a serviços da infraestrutura digital da cidade [1].

## URL Base

Todos os endpoints da API devem ser acessados utilizando o seguinte prefixo:

`https://publica.api.centralmacatuba.eu.org/api`

---

## 1. Jornal (Notícias)

Este endpoint permite a pesquisa de notícias e artigos publicados no Jornal da Central Macatuba.

### Endpoint

`/jornal/pesquisar.php`

### Parâmetros

| Parâmetro | Tipo | Obrigatório | Descrição | Valores Possíveis |
| :--- | :--- | :--- | :--- | :--- |
| `q` | `string` | Sim | Termo de pesquisa. | Qualquer string. |
| `filtro` | `string` | Não | Campo para aplicar o filtro de pesquisa. | `tudo` (padrão), `criador`, `titulo`, `subtitulo`, `conteudo` |

### Exemplo de Requisição

Pesquisar notícias com a palavra "inauguração" no título:

```bash
curl -X GET "https://publica.api.centralmacatuba.eu.org/api/jornal/pesquisar.php?q=inauguração&filtro=titulo"
```

### Exemplo de Resposta (JSON)

```json
{
  "sucesso": true,
  "pesquisa": "inauguração",
  "filtro": "titulo",
  "quantidade": 1,
  "resultados": [
    {
      "id": "123",
      "titulo": "Inauguração da Nova Praça Central",
      "subtitulo": "Evento contou com a presença de autoridades.",
      "conteudo": "Detalhes completos sobre a cerimônia...",
      "criador": "Prefeitura",
      "data": "2026-01-20 10:00:00",
      "feito": "2026-01-20 10:00:00",
      "verificado": "2026-01-20 10:00:00"
    }
  ]
}
```

---

## 2. Tempo (Dados Meteorológicos)

Este endpoint fornece dados meteorológicos históricos e em tempo real para Macatuba.

### Endpoint

`/tempo/verificar.php`

### Parâmetros

| Parâmetro | Tipo | Obrigatório | Descrição |
| :--- | :--- | :--- | :--- |
| `source` | `string` | Não | Filtra os dados por fonte de origem. |
| `start` | `string` | Não | Timestamp de início para o intervalo de dados (formato `YYYY-MM-DD`). |
| `end` | `string` | Não | Timestamp de fim para o intervalo de dados (formato `YYYY-MM-DD`). |

### Exemplo de Requisição

Obter dados de tempo entre 1º e 20 de Janeiro de 2026:

```bash
curl -X GET "https://publica.api.centralmacatuba.eu.org/api/tempo/verificar.php?start=2026-01-01&end=2026-01-20"
```

### Exemplo de Resposta (JSON)

```json
{
  "status": "sucesso",
  "cidade": "Macatuba",
  "total": 2,
  "dados": [
    {
      "id": "1",
      "source": "inmet",
      "temperatura": "28.5",
      "umidade": "65",
      "timestamp": "2026-01-20 15:00:00"
    },
    {
      "id": "2",
      "source": "local",
      "temperatura": "27.0",
      "umidade": "70",
      "timestamp": "2026-01-19 15:00:00"
    }
  ]
}
```

---

## 3. Mapa (Pontos de Interesse)

Este endpoint permite a pesquisa de pontos de interesse (POIs) cadastrados no Mapa da Central Macatuba.

### Endpoint

`/mapa/pesquisar.php`

### Parâmetros

| Parâmetro | Tipo | Obrigatório | Descrição | Valores Possíveis |
| :--- | :--- | :--- | :--- | :--- |
| `q` | `string` | Sim | Termo de pesquisa. | Qualquer string. |
| `filtro` | `string` | Não | Campo para aplicar o filtro de pesquisa. | `tudo` (padrão), `nome`, `tipo`, `criador`, `descricao` |

### Exemplo de Requisição

Pesquisar pontos de interesse do tipo "escola":

```bash
curl -X GET "https://publica.api.centralmacatuba.eu.org/api/mapa/pesquisar.php?q=escola&filtro=tipo"
```

### Exemplo de Resposta (JSON)

```json
{
  "sucesso": true,
  "pesquisa": "escola",
  "filtro": "tipo",
  "quantidade": 1,
  "resultados": [
    {
      "id": "456",
      "nome": "E.M.E.F. Prof. João da Silva",
      "tipo": "escola",
      "latitude": "-22.5000",
      "longitude": "-48.7000",
      "descricao": "Escola municipal de ensino fundamental."
    }
  ]
}
```

---

## 4. SOS (Alertas de Emergência)

Este endpoint lista os alertas de emergência ativos e recentes emitidos pela Central Macatuba.

### Endpoint

`/sos/listar_alertas.php`

### Parâmetros

Não requer parâmetros de entrada.

### Exemplo de Requisição

Listar todos os alertas ativos:

```bash
curl -X GET "https://publica.api.centralmacatuba.eu.org/api/sos/listar_alertas.php"
```

### Exemplo de Resposta (JSON)

```json
{
  "success": true,
  "alertas": [
    {
      "id": "789",
      "nivel": "Alto",
      "titulo": "Alerta de Chuvas Fortes",
      "mensagem": "Previsão de chuva intensa nas próximas 6 horas.",
      "link": "https://centralmacatuba.eu.org/alerta/789",
      "data": "21/01/2026 14:30"
    }
  ],
  "notificacaoAceita": false
}
```

---

## 5. Status (Saúde do Sistema)

Este endpoint permite verificar a saúde e o status operacional dos serviços e sistemas da Central Macatuba.

### Endpoint

`/status/verificar.php`

### Parâmetros

| Parâmetro | Tipo | Obrigatório | Descrição | Valores Possíveis |
| :--- | :--- | :--- | :--- | :--- |
| `tipo` | `string` | Não | Tipo de informação de status a ser retornada. | `tudo` (padrão), `uma`, `avisos` |
| `id` | `integer` | Condicional | ID do item de status, obrigatório se `tipo` for `uma`. | ID de um item de status. |

### Exemplo de Requisição

Obter o status completo de todos os serviços:

```bash
curl -X GET "https://publica.api.centralmacatuba.eu.org/api/status/verificar.php?tipo=tudo"
```

### Exemplo de Resposta (JSON - Estrutura Simplificada)

```json
{
  "status": "ok",
  "ultima_atualizacao": "2026-01-21T14:30:00-03:00",
  "secoes": [
    {
      "id": 1,
      "nome": "Serviços Essenciais",
      "tipo": "grupo",
      "subsecoes": [],
      "itens": [
        {
          "id": 101,
          "nome": "API Jornal",
          "status": "Operacional",
          "latencia": "50ms"
        }
      ]
    }
  ]
}
```

---

## Referências

[1] Central Macatuba. *Versão canônica atualizada da definição da Central Macatuba*. (Informação fornecida pelo usuário).

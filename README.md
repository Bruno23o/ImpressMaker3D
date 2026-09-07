# ImpressMaker3D — Controle de Eventos e Animações com jQuery

Atividade da Unidade de Conhecimento 2: página web de vitrine de produtos da
**ImpressMaker3D** (loja de impressão 3D sob encomenda), construída em
HTML + CSS, com jQuery para eventos e animações.

> ⚠️ Antes de subir para o GitHub, renomeie a pasta raiz deste projeto para
> o seu nome, conforme pedido no enunciado da atividade.

## Estrutura de arquivos

```
impressmaker3d/
├── index.html          # estrutura da página
├── css/
│   └── style.css        # identidade visual (paleta baseada na logo)
├── js/
│   └── script.js         # todos os eventos e animações jQuery
└── assets/
    └── img/
        └── logo.jpg      # logo da marca
```

## Como abrir

Basta abrir o `index.html` no navegador. O jQuery é carregado via CDN
(`code.jquery.com`), então é preciso estar com internet ativa. Se preferir
rodar localmente sem CDN, baixe o `jquery.min.js` e ajuste o `<script src>`
no `index.html`.

## Eventos e animações jQuery utilizados (`js/script.js`)

| # | Recurso                              | Evento          | Animação usada          |
|---|---------------------------------------|-----------------|--------------------------|
| 1 | Cabeçalho muda ao rolar a página      | `scroll`        | `addClass`/`removeClass` |
| 2 | Menu mobile                           | `click`         | `slideToggle`            |
| 3 | Rolagem suave até a seção             | `click`         | `animate(scrollTop)`     |
| 4 | Seções aparecem ao rolar a tela       | `scroll`        | classe + transição CSS   |
| 5 | Detalhes do material do produto       | `click`         | `slideToggle`            |
| 6 | Destaque do card ao passar o mouse    | `hover`         | classe + transição CSS   |
| 7 | Compra pelo WhatsApp                | `click`         | abertura de conversa com o produto |
| 8 | Acordeão de perguntas frequentes      | `click`         | `slideDown` / `slideUp`  |
| 9 | Validação e envio do formulário       | `submit`        | `fadeOut` / `fadeIn`     |
| 10| Botão "voltar ao topo"                | `scroll`/`click`| `fadeIn` / `fadeOut`     |
| 11| Animação do bico da impressora no hero| carregamento    | `animate()` em loop      |

## Sobre o conteúdo

- Produtos exibidos com fotos reais: suporte de controle PS5, chaveiro de
  parede Porsche GT3, suporte para secar cuia e bomba, e dinossauros de
  enfeite. As fotos ficam em `assets/img/products/`.
- Paleta de cores extraída da logo: roxo (`#9B3FF0`), verde neon
  (`#B6FF3B`), ciano (`#57E0FF`) sobre fundo escuro (`#0A0714`).
- Os botões dos produtos abrem o WhatsApp com o nome e o preço do produto
  preenchidos na mensagem.
- As galerias de produtos aceitam várias imagens dentro de `.product-slides`;
  ao adicionar uma segunda imagem, setas, indicadores e gesto de arrastar são
  ativados automaticamente.
- As novas fotos enviadas foram cadastradas como produtos independentes na
  vitrine e já podem ser compradas pelo WhatsApp.
- A vitrine reúne as duas variações do suporte de controle PS5 em um único
  produto com várias fotos, e os dois vasos espirais em um único produto.

## Próximos passos sugeridos

- Adicionar mais produtos ao `.product-grid` conforme o catálogo crescer
  (basta copiar a estrutura de um `<article class="product-card">`).
- Ligar o formulário de contato a um backend real (por enquanto ele só
  simula o envio no front-end).
- Trocar as fotos por versões com fundo mais neutro/uniforme, se possível,
  para deixar a vitrine mais consistente.

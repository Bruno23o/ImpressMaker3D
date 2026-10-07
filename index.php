<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = getConexao();
$produtos = buscarProdutos($pdo, true); // true = só produtos ativos

// Pega todas as imagens de cada produto de uma vez (evita 1 query por produto).
$produtosComImagens = [];
foreach ($produtos as $p) {
    $produtosComImagens[] = buscarProdutoPorId($pdo, (int) $p['id']);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ImpressMaker3D — Impressão 3D sob medida</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- ===================== HEADER ===================== -->
<header class="site-header" id="site-header">
  <div class="header-inner">
    <a href="#topo" class="brand">
      <img src="assets/img/logo.jpg" alt="ImpressMaker3D" class="brand-mark">
      <span class="brand-name">ImpressMaker<em>3D</em></span>
    </a>

    <nav class="main-nav" id="main-nav">
      <a href="#produtos" class="nav-link">Produtos</a>
      <a href="#processo" class="nav-link">Como funciona</a>
      <a href="#duvidas" class="nav-link">Dúvidas</a>
      <a href="#contato" class="nav-link">Contato</a>
    </nav>

    <a href="#contato" class="btn btn-ghost nav-cta">Pedir orçamento</a>

    <button class="nav-toggle" id="nav-toggle" aria-label="Abrir menu" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>

<!-- ===================== HERO ===================== -->
<section class="hero" id="topo">
  <div class="hero-inner">
    <div class="hero-copy reveal">
      <p class="hero-kicker">Impressão 3D por encomenda</p>
      <h1>Seu projeto sai da tela e pousa na sua mesa.</h1>
      <p class="hero-text">
        A ImpressMaker3D transforma arquivos digitais em objetos reais: miniaturas, peças
        de reposição, suportes, presentes e protótipos, impressos camada por camada com
        o material certo para cada ideia.
      </p>
      <div class="hero-actions">
        <a href="#produtos" class="btn btn-primary">Ver categorias</a>
        <a href="#processo" class="btn btn-outline">Como funciona</a>
      </div>
      <dl class="hero-stats">
        <div><dt>0,1 mm</dt><dd>precisão de camada</dd></div>
        <div><dt>+12</dt><dd>materiais disponíveis</dd></div>
        <div><dt>48h</dt><dd>prazo médio de impressão</dd></div>
      </dl>
    </div>

    <div class="hero-visual" aria-hidden="true">
      <div class="glow glow-purple"></div>
      <div class="glow glow-green"></div>
      <svg id="printer-rig" viewBox="0 0 360 460" xmlns="http://www.w3.org/2000/svg">
        <line x1="90" y1="40" x2="270" y2="40" stroke="#5c4a86" stroke-width="4" stroke-linecap="round"/>
        <line x1="90" y1="40" x2="90" y2="120" stroke="#5c4a86" stroke-width="4" stroke-linecap="round"/>
        <line x1="270" y1="40" x2="270" y2="120" stroke="#5c4a86" stroke-width="4" stroke-linecap="round"/>
        <g id="nozzle-head">
          <rect x="150" y="30" width="60" height="34" rx="6" fill="#241a3d" stroke="#8d6fd6" stroke-width="2"/>
          <polygon points="172,64 188,64 180,82" fill="#B6FF3B"/>
        </g>
        <polyline id="print-path" points="120,420 240,420 240,340 120,340 120,300 240,300"
          fill="none" stroke="#57E0FF" stroke-width="2" stroke-dasharray="4 5" opacity="0.5"/>
        <g id="printed-object" transform="translate(180,300)">
          <polygon points="0,-90 34,-70 34,0 0,20 -34,0 -34,-70" fill="none" stroke="#9B3FF0" stroke-width="3" stroke-linejoin="round"/>
          <circle cx="0" cy="-45" r="16" fill="none" stroke="#B6FF3B" stroke-width="3"/>
        </g>
        <ellipse cx="180" cy="428" rx="90" ry="10" fill="#160F26"/>
      </svg>
    </div>
  </div>
  <button class="scroll-cue" id="scroll-cue" aria-label="Rolar para baixo">
    <span></span>
  </button>
</section>

<!-- ===================== PRODUTOS ===================== -->
<section class="section" id="produtos">
  <div class="section-inner">
    <h2 class="reveal">O que sai da nossa impressora</h2>
    <p class="section-lead reveal">
      Uma amostra do que já saiu da nossa impressora — de utilitários do dia a dia a peças
      de decoração e presentes criativos.
    </p>

    <div class="product-grid">

      <?php if (empty($produtosComImagens)): ?>
        <p class="section-lead">Em breve, novos produtos por aqui.</p>
      <?php endif; ?>

      <?php foreach ($produtosComImagens as $produto): ?>
        <article class="product-card reveal">
          <div class="product-visual">
            <div class="product-carousel" aria-label="Fotos do <?= htmlspecialchars($produto['nome']) ?>">
              <div class="product-slides">
                <?php if (empty($produto['imagens'])): ?>
                  <img class="product-slide is-active" src="assets/img/placeholder.jpg" alt="<?= htmlspecialchars($produto['nome']) ?>">
                <?php else: ?>
                  <?php foreach ($produto['imagens'] as $i => $img): ?>
                    <img class="product-slide<?= $i === 0 ? ' is-active' : '' ?>"
                         src="<?= htmlspecialchars($img['caminho_arquivo']) ?>"
                         alt="<?= htmlspecialchars($img['texto_alternativo'] ?: $produto['nome']) ?>">
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
              <button type="button" class="carousel-arrow carousel-prev" aria-label="Foto anterior">&#8249;</button>
              <button type="button" class="carousel-arrow carousel-next" aria-label="Próxima foto">&#8250;</button>
              <div class="carousel-dots" aria-label="Selecionar foto"></div>
            </div>
          </div>
          <div class="product-body">
            <h3><?= htmlspecialchars($produto['nome']) ?></h3>
            <p><?= htmlspecialchars($produto['descricao']) ?></p>
            <?php if (!empty($produto['detalhes_material'])): ?>
              <button type="button" class="card-toggle" aria-expanded="false">Detalhes do material</button>
              <div class="card-details">
                <p><?= htmlspecialchars($produto['detalhes_material']) ?></p>
              </div>
            <?php endif; ?>
            <div class="product-purchase">
              <strong><?= formatarPreco((float) $produto['preco']) ?></strong>
              <button type="button" class="btn btn-primary add-to-cart"
                      data-product-id="<?= $produto['id'] ?>"
                      data-product-name="<?= htmlspecialchars($produto['nome']) ?>"
                      data-product-price="<?= htmlspecialchars((string) $produto['preco']) ?>">Adicionar</button>
            </div>
          </div>
        </article>
      <?php endforeach; ?>

    </div>
  </div>
</section>

<!-- ===================== PROCESSO ===================== -->
<section class="section section-alt" id="processo">
  <div class="section-inner">
    <h2 class="reveal">Do arquivo ao objeto em 4 etapas</h2>

    <ol class="process-list">
      <li class="process-step reveal">
        <span class="step-index">01</span>
        <div>
          <h3>Envie o modelo ou a ideia</h3>
          <p>Mande um arquivo 3D pronto ou descreva o que você precisa — a gente ajuda a desenhar.</p>
        </div>
      </li>
      <li class="process-step reveal">
        <span class="step-index">02</span>
        <div>
          <h3>Escolha material e cor</h3>
          <p>PLA, PETG ou resina, na cor e no acabamento que combinam com o uso da peça.</p>
        </div>
      </li>
      <li class="process-step reveal">
        <span class="step-index">03</span>
        <div>
          <h3>Impressão camada por camada</h3>
          <p>Acompanhe o andamento da impressão e receba fotos antes do envio.</p>
        </div>
      </li>
      <li class="process-step reveal">
        <span class="step-index">04</span>
        <div>
          <h3>Entrega na sua porta</h3>
          <p>Peça revisada, embalada e enviada, com prazo médio combinado desde o orçamento.</p>
        </div>
      </li>
    </ol>
  </div>
</section>

<!-- ===================== DÚVIDAS ===================== -->
<section class="section" id="duvidas">
  <div class="section-inner section-narrow">
    <h2 class="reveal">Perguntas frequentes</h2>

    <div class="faq-list">
      <div class="faq-item reveal">
        <button type="button" class="faq-question" aria-expanded="true">
          Preciso ter um arquivo 3D pronto?
        </button>
        <div class="faq-answer">
          <p>Não. Se você já tem um arquivo STL ou OBJ, ótimo — mas também modelamos a peça a partir de uma descrição, foto de referência ou desenho.</p>
        </div>
      </div>
      <div class="faq-item reveal">
        <button type="button" class="faq-question" aria-expanded="false">
          Qual o prazo de entrega?
        </button>
        <div class="faq-answer">
          <p>Peças simples ficam prontas em até 48h de impressão. Projetos com modelagem própria ou peças grandes têm prazo combinado no orçamento.</p>
        </div>
      </div>
      <div class="faq-item reveal">
        <button type="button" class="faq-question" aria-expanded="false">
          Quais materiais vocês usam?
        </button>
        <div class="faq-answer">
          <p>PLA e PETG para a maioria das peças, e resina para miniaturas e detalhes finos. Indicamos o material ideal conforme o uso da peça.</p>
        </div>
      </div>
      <div class="faq-item reveal">
        <button type="button" class="faq-question" aria-expanded="false">
          Vocês fazem peças grandes?
        </button>
        <div class="faq-answer">
          <p>Sim, dentro do volume de impressão disponível. Para objetos maiores, dividimos o modelo em partes e montamos após a impressão.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ===================== CONTATO ===================== -->
<section class="section section-alt" id="contato">
  <div class="section-inner section-narrow">
    <h2 class="reveal">Peça um orçamento</h2>
    <p class="section-lead reveal">Conte o que você quer imprimir. Respondemos com uma estimativa de material, prazo e valor.</p>

    <form id="contact-form" class="contact-form reveal" novalidate>
      <div class="form-row">
        <label for="nome">Nome</label>
        <input type="text" id="nome" name="nome" required placeholder="Como podemos te chamar?">
        <span class="field-error">Conta pra gente seu nome.</span>
      </div>
      <div class="form-row">
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" required placeholder="voce@email.com">
        <span class="field-error">Digite um e-mail válido.</span>
      </div>
      <div class="form-row">
        <label for="telefone">Telefone</label>
        <input type="tel" id="telefone" name="telefone" required placeholder="(00) 00000-0000" autocomplete="tel">
        <span class="field-error">Digite seu número de telefone.</span>
      </div>
      <div class="form-row">
        <label for="projeto">Descreva o que você quer imprimir</label>
        <textarea id="projeto" name="projeto" rows="4" required placeholder="Tamanho aproximado, material, cor, referência..."></textarea>
        <span class="field-error">Descreva rapidamente o projeto.</span>
      </div>
      <button type="submit" class="btn btn-primary">Enviar pelo WhatsApp</button>
    </form>

    <div class="contact-success" id="contact-success" hidden>
      <h3>Pedido recebido.</h3>
      <p>Sua mensagem já chegou até a ImpressMaker3D. Em breve entramos em contato com uma estimativa.</p>
      <button type="button" class="btn btn-outline" id="contact-reset">Enviar outro pedido</button>
    </div>
  </div>
</section>

<!-- ===================== FOOTER ===================== -->
<footer class="site-footer">
  <div class="footer-inner">
    <a href="#topo" class="brand brand-footer">
      <img src="assets/img/logo.jpg" alt="ImpressMaker3D" class="brand-mark">
      <span class="brand-name">ImpressMaker<em>3D</em></span>
    </a>
    <p class="footer-text">Impressão 3D sob medida, camada por camada.</p>
    <p class="footer-copy">&copy; <span id="footer-year"></span> ImpressMaker3D</p>
  </div>
</footer>

<button type="button" class="to-top" id="to-top" aria-label="Voltar ao topo">&#8593;</button>
<a class="whatsapp-button" href="https://wa.me/5554991809072?text=Ol%C3%A1%2C%20gostaria%20de%20saber%20mais%20sobre%20os%20produtos%20da%20ImpressMaker3D." target="_blank" rel="noopener noreferrer" aria-label="Conversar pelo WhatsApp">
  <span aria-hidden="true">◉</span> WhatsApp
</a>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="js/script.js?v=202609071356"></script>
</body>
</html>

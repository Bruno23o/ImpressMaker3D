/* ==========================================================
   ImpressMaker3D — eventos e animações com jQuery
   Atividade: Controle de eventos e animações com jQuery
   ========================================================== */

$(function () {

  /* ------------------------------------------------------
     1) Cabeçalho: muda de aparência ao rolar a página
     Evento: scroll | Ação: adicionar/remover classe
     ------------------------------------------------------ */
  var $header = $('#site-header');

  function atualizarHeader() {
    if ($(window).scrollTop() > 20) {
      $header.addClass('is-scrolled');
    } else {
      $header.removeClass('is-scrolled');
    }
  }
  atualizarHeader();
  $(window).on('scroll', atualizarHeader);


  /* ------------------------------------------------------
     2) Menu mobile
     Evento: click | Animação: slideToggle
     ------------------------------------------------------ */
  var $navToggle = $('#nav-toggle');
  var $mainNav = $('#main-nav');

  $navToggle.on('click', function () {
    $mainNav.stop(true, true).slideToggle(250);
    $(this).toggleClass('is-open');
    var aberto = $(this).hasClass('is-open');
    $(this).attr('aria-expanded', aberto);
  });

  // fecha o menu mobile ao clicar em um link
  $mainNav.on('click', 'a', function () {
    if ($mainNav.is(':visible') && $(window).width() <= 860) {
      $mainNav.slideUp(200);
      $navToggle.removeClass('is-open').attr('aria-expanded', false);
    }
  });


  /* ------------------------------------------------------
     3) Rolagem suave para os links internos
     Evento: click | Animação: animate(scrollTop)
     ------------------------------------------------------ */
  $('a[href^="#"]').on('click', function (e) {
    var destino = $(this).attr('href');
    if (destino.length > 1 && $(destino).length) {
      e.preventDefault();
      $('html, body').stop().animate(
        { scrollTop: $(destino).offset().top - 70 },
        600,
        'swing'
      );
    }
  });

  $('#scroll-cue').on('click', function () {
    $('html, body').stop().animate({ scrollTop: $('#produtos').offset().top - 70 }, 700);
  });


  /* ------------------------------------------------------
     4) Revelar seções ao rolar a tela (scroll reveal)
     Evento: scroll | Animação: fade + slide via classe CSS
     ------------------------------------------------------ */
  var $reveals = $('.reveal');

  function revelarNaTela() {
    var alturaJanela = $(window).height();
    $reveals.each(function () {
      var $el = $(this);
      if ($el.hasClass('is-visible')) return;
      var topo = $el.offset().top;
      if (topo < $(window).scrollTop() + alturaJanela - 80) {
        $el.addClass('is-visible');
      }
    });
  }
  revelarNaTela();
  $(window).on('scroll resize', revelarNaTela);


  /* ------------------------------------------------------
     5) Cartões de produto: detalhes do material
     Evento: click | Animação: slideDown / slideUp
     ------------------------------------------------------ */
  $('.card-toggle').on('click', function () {
    var $botao = $(this);
    var $detalhes = $botao.siblings('.card-details');
    var aberto = $botao.attr('aria-expanded') === 'true';

    $detalhes.stop(true, true).slideToggle(220);
    $botao.attr('aria-expanded', !aberto);
    $botao.text(aberto ? 'Detalhes do material' : 'Fechar detalhes');
  });

  // Hover: leve destaque no card (evento hover)
  $('.product-card').hover(
    function () { $(this).addClass('is-hovered'); },
    function () { $(this).removeClass('is-hovered'); }
  );

  /* ------------------------------------------------------
     6) Galeria dos produtos
     Evento: click/swipe | Ação: trocar imagem
     ------------------------------------------------------ */
  $('.product-carousel').each(function () {
    var $carousel = $(this);
    var $slides = $carousel.find('.product-slide');
    var $dots = $carousel.find('.carousel-dots');
    var indiceAtual = 0;

    if ($slides.length < 2) return;

    $carousel.addClass('has-multiple');
    $slides.each(function (indice) {
      $dots.append('<button type="button" class="carousel-dot' + (indice === 0 ? ' is-active' : '') + '" aria-label="Ver foto ' + (indice + 1) + '"></button>');
    });

    function mostrarFoto(indice) {
      indiceAtual = (indice + $slides.length) % $slides.length;
      $slides.removeClass('is-active').eq(indiceAtual).addClass('is-active');
      $dots.find('.carousel-dot').removeClass('is-active').eq(indiceAtual).addClass('is-active');
    }

    $carousel.on('click', '.carousel-prev', function () { mostrarFoto(indiceAtual - 1); });
    $carousel.on('click', '.carousel-next', function () { mostrarFoto(indiceAtual + 1); });
    $carousel.on('click', '.carousel-dot', function () { mostrarFoto($(this).index()); });

    var inicioToque = 0;
    $carousel.on('touchstart', function (e) {
      inicioToque = e.originalEvent.touches[0].clientX;
    });
    $carousel.on('touchend', function (e) {
      var distancia = e.originalEvent.changedTouches[0].clientX - inicioToque;
      if (Math.abs(distancia) > 40) mostrarFoto(indiceAtual + (distancia < 0 ? 1 : -1));
    });
  });

  /* ------------------------------------------------------
     7) Carrinho de produtos
     Evento: click | Estado: localStorage
     ------------------------------------------------------ */
  var carrinho = JSON.parse(localStorage.getItem('impressmaker3d-cart') || '[]');
  var $cartOverlay = $('#cart-overlay');
  var $cartItems = $('#cart-items');
  var $checkoutOverlay = $('#checkout-overlay');

  function moeda(valor) {
    return valor.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
  }

  function salvarCarrinho() {
    localStorage.setItem('impressmaker3d-cart', JSON.stringify(carrinho));
  }

  function atualizarCarrinho() {
    var quantidadeTotal = 0;
    var valorTotal = 0;
    $cartItems.empty();

    carrinho.forEach(function (item) {
      quantidadeTotal += item.quantity;
      valorTotal += item.price * item.quantity;
      $cartItems.append(
        '<div class="cart-item" data-product-id="' + item.id + '">' +
          '<div><p class="cart-item-name">' + item.name + '</p><span class="cart-item-price">' + moeda(item.price * item.quantity) + '</span></div>' +
          '<div class="cart-item-controls">' +
            '<button type="button" class="quantity-btn quantity-decrease" aria-label="Diminuir quantidade">−</button>' +
            '<span>' + item.quantity + '</span>' +
            '<button type="button" class="quantity-btn quantity-increase" aria-label="Aumentar quantidade">+</button>' +
            '<button type="button" class="cart-remove">Remover</button>' +
          '</div>' +
        '</div>'
      );
    });

    $('#cart-count').text(quantidadeTotal);
    $('#cart-total').text(moeda(valorTotal));
    $('#cart-empty').toggle(carrinho.length === 0);
    $('#cart-footer').toggle(carrinho.length > 0);
    salvarCarrinho();
  }

  $('.add-to-cart').on('click', function () {
    var $botao = $(this);
    var id = $botao.data('product-id');
    var item = carrinho.find(function (produto) { return produto.id === id; });
    if (item) {
      item.quantity += 1;
    } else {
      carrinho.push({
        id: id,
        name: $botao.data('product-name'),
        price: Number($botao.data('product-price')),
        quantity: 1
      });
    }
    atualizarCarrinho();
    $cartOverlay.removeAttr('hidden');
  });

  $('#cart-trigger').on('click', function () {
    $cartOverlay.removeAttr('hidden');
  });
  $('#cart-close').on('click', function () {
    $cartOverlay.attr('hidden', true);
  });
  $cartOverlay.on('click', function (e) {
    if (e.target === this) $cartOverlay.attr('hidden', true);
  });
  $cartItems.on('click', '.quantity-btn, .cart-remove', function () {
    var id = $(this).closest('.cart-item').data('product-id');
    var item = carrinho.find(function (produto) { return produto.id === id; });
    if ($(this).hasClass('cart-remove') || ($(this).hasClass('quantity-decrease') && item.quantity === 1)) {
      carrinho = carrinho.filter(function (produto) { return produto.id !== id; });
    } else {
      item.quantity += $(this).hasClass('quantity-increase') ? 1 : -1;
    }
    atualizarCarrinho();
  });
  $('#cart-see-products').on('click', function () {
    $cartOverlay.attr('hidden', true);
  });
  $('#cart-checkout').on('click', function () {
    $cartOverlay.attr('hidden', true);
    $('#checkout-form')[0].reset();
    $('#checkout-form .has-error').removeClass('has-error');
    $('#card-fields').prop('hidden', true);
    $('#card-fields input').prop('required', false);
    $('#checkout-success').attr('hidden', true);
    $checkoutOverlay.removeAttr('hidden');
    mostrarEtapaCheckout(1);
  });
  function mostrarEtapaCheckout(etapa) {
    $('.checkout-step').removeClass('is-active');
    $('.checkout-step[data-step="' + etapa + '"]').addClass('is-active');
    $('[data-step-indicator]').removeClass('is-active');
    $('[data-step-indicator="' + etapa + '"]').addClass('is-active');
  }
  $('#checkout-close').on('click', function () { $checkoutOverlay.attr('hidden', true); });
  $checkoutOverlay.on('click', function (e) {
    if (e.target === this) $checkoutOverlay.attr('hidden', true);
  });
  $('#payment-method').on('change', function () {
    var cartao = $(this).val() === 'cartao';
    $('#card-fields').prop('hidden', !cartao);
    $('#card-fields input').prop('required', cartao);
  });
  $('.checkout-next').on('click', function () {
    var $step = $(this).closest('.checkout-step');
    var etapaAtual = Number($step.data('step'));
    var valido = true;
    $step.find('[required]:visible').each(function () {
      var preenchido = $.trim($(this).val()).length > 0;
      $(this).closest('.form-row').toggleClass('has-error', !preenchido);
      if (!preenchido) valido = false;
    });
    if (!valido) return;
    if (etapaAtual === 2) {
      var resumo = carrinho.map(function (item) { return item.quantity + 'x ' + item.name; }).join('<br>');
      $('#checkout-summary').html('<strong>Itens:</strong><br>' + resumo + '<br><br><strong>Total:</strong> ' + $('#cart-total').text() + '<br><br><strong>Entrega:</strong> ' + $('#shipping-address').val() + ', ' + $('#shipping-number').val() + ' - ' + $('#shipping-city').val());
    }
    mostrarEtapaCheckout(etapaAtual + 1);
  });
  $('.checkout-back').on('click', function () {
    mostrarEtapaCheckout(Number($(this).closest('.checkout-step').data('step')) - 1);
  });
  $('#checkout-form').on('submit', function (e) {
    e.preventDefault();
    $('.checkout-step').removeClass('is-active');
    $('#checkout-success').removeAttr('hidden');
    carrinho = [];
    atualizarCarrinho();
  });
  $('#checkout-finish').on('click', function () { $checkoutOverlay.attr('hidden', true); });
  atualizarCarrinho();


  /* ------------------------------------------------------
     7) FAQ — acordeão, um item aberto por vez
     Evento: click | Animação: slideDown / slideUp
     ------------------------------------------------------ */
  var $faqItems = $('.faq-item');

  // estado inicial: primeira pergunta aberta
  $faqItems.eq(0).find('.faq-answer').show();

  $('.faq-question').on('click', function () {
    var $pergunta = $(this);
    var $item = $pergunta.closest('.faq-item');
    var $resposta = $pergunta.siblings('.faq-answer');
    var jaAberta = $pergunta.attr('aria-expanded') === 'true';

    // fecha as outras perguntas abertas
    $faqItems.not($item).each(function () {
      $(this).find('.faq-question').attr('aria-expanded', false);
      $(this).find('.faq-answer').stop(true, true).slideUp(200);
    });

    if (jaAberta) {
      $resposta.stop(true, true).slideUp(200);
      $pergunta.attr('aria-expanded', false);
    } else {
      $resposta.stop(true, true).slideDown(220);
      $pergunta.attr('aria-expanded', true);
    }
  });


  /* ------------------------------------------------------
     7) Formulário de contato
     Evento: submit | Animação: fadeOut / fadeIn
     ------------------------------------------------------ */
  var $form = $('#contact-form');
  var $success = $('#contact-success');

  $form.on('submit', function (e) {
    e.preventDefault();
    var valido = true;

    $form.find('[required]').each(function () {
      var $campo = $(this);
      var $linha = $campo.closest('.form-row');
      var preenchido = $.trim($campo.val()).length > 0;
      var emailOk = true;

      if ($campo.attr('type') === 'email' && preenchido) {
        emailOk = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test($campo.val());
      }

      if (!preenchido || !emailOk) {
        $linha.addClass('has-error');
        valido = false;
      } else {
        $linha.removeClass('has-error');
      }
    });

    if (!valido) {
      // pequena animação de aviso no primeiro campo com erro
      $form.find('.has-error').first().find('input, textarea')
        .stop().animate({ opacity: 0.4 }, 100).animate({ opacity: 1 }, 150);
      return;
    }

    var mensagem = [
      'Olá, gostaria de solicitar um orçamento na ImpressMaker3D.',
      '',
      'Nome: ' + $('#nome').val(),
      'E-mail: ' + $('#email').val(),
      'Telefone: ' + $('#telefone').val(),
      'Projeto: ' + $('#projeto').val()
    ].join('\n');
    var whatsappUrl = 'https://wa.me/5554984332709?text=' + encodeURIComponent(mensagem);

    window.open(whatsappUrl, '_blank', 'noopener,noreferrer');
  });

  $('#contact-reset').on('click', function () {
    $success.fadeOut(200, function () {
      $form[0].reset();
      $form.find('.has-error').removeClass('has-error');
      $form.fadeIn(300);
    });
  });


  /* ------------------------------------------------------
     8) Botão "voltar ao topo"
     Evento: scroll / click | Animação: fadeIn / fadeOut
     ------------------------------------------------------ */
  var $toTop = $('#to-top');

  $(window).on('scroll', function () {
    if ($(window).scrollTop() > 500) {
      if ($toTop.css('display') === 'none') $toTop.fadeIn(200);
    } else {
      if ($toTop.css('display') !== 'none') $toTop.fadeOut(200);
    }
  });

  $toTop.on('click', function () {
    $('html, body').stop().animate({ scrollTop: 0 }, 500);
  });


  /* ------------------------------------------------------
     9) Animação decorativa da impressora no hero
     Evento: carregamento da página | Animação: animate() em loop
     ------------------------------------------------------ */
  var $nozzle = $('#nozzle-head');
  var $object = $('#printed-object');

  function animarBicoImpressora() {
    $nozzle.css('transform', 'translateY(0px)');
    $({ y: 0 }).animate({ y: 14 }, {
      duration: 900,
      easing: 'swing',
      step: function (valor) {
        $nozzle.css('transform', 'translateY(' + valor + 'px)');
      },
      complete: function () {
        $({ y: 14 }).animate({ y: 0 }, {
          duration: 900,
          easing: 'swing',
          step: function (valor) {
            $nozzle.css('transform', 'translateY(' + valor + 'px)');
          },
          complete: animarBicoImpressora
        });
      }
    });
  }
  animarBicoImpressora();

  // objeto "surge" com um fadeIn suave assim que a página carrega
  $object.css({ opacity: 0 }).delay(300).fadeTo(900, 1);


  /* ------------------------------------------------------
     10) Ano atual no rodapé
     ------------------------------------------------------ */
  $('#footer-year').text(new Date().getFullYear());

});

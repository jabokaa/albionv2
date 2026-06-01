@extends('layouts.app')

@section('title', 'AlbionHub — Economia, Builds e Mapas de Albion Online')

@section('content')

{{-- Hero --}}
<section class="hero" id="top" style="padding:0">
  <div class="hero-bg" style="background-image:url('{{ asset('images/hero.jpg') }}')"></div>
  <div class="hero-inner">
    <span class="eyebrow" data-i18n="hero.eyebrow">A central definitiva de Albion Online</span>
    <h1>
      <span data-i18n="hero.title.line1">Domine a economia.</span><br>
      <span data-i18n="hero.title.line2">Vença em</span> <span class="em">Albion</span>.
    </h1>
    <p class="sub" data-i18n="hero.subtitle">Calculadoras de craft e refino, preços em tempo real, builds de PvP testadas e mapas das black zones — tudo em um só lugar.</p>

    <div class="divider-orn">
      <span class="line"></span><span class="gem"></span><span class="line r"></span>
    </div>

    <div class="search">
      <form class="search-row" id="heroForm" method="GET" action="{{ route('itens.index') }}">
        <div class="field">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#C8942A" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
          <input id="heroSearch" name="busca" type="text"
            data-i18n-placeholder="hero.search.placeholder.items"
            placeholder="Espada Adamantita, Cajado de Fogo, Lingote de Titânio…" />
        </div>
        <button type="submit" class="btn btn-gold" data-i18n="hero.search.button">Buscar</button>
      </form>
    </div>

    <div class="hero-stats">
      <div class="s"><b data-i18n="hero.stat.items.value">8.1k</b><span data-i18n="hero.stat.items.label">Itens rastreados</span></div>
      <div class="s"><b data-i18n="hero.stat.cities.value">Caerleon</b><span data-i18n="hero.stat.cities.label">Transporte</span></div>
      <div class="s"><b data-i18n="hero.stat.craft.value">craft</b><span data-i18n="hero.stat.craft.label">Calculadora de Craft</span></div>
      <div class="s"><b data-i18n="hero.stat.free.value">Free</b><span data-i18n="hero.stat.free.label">Sem custo, sempre</span></div>
    </div>
  </div>
</section>

{{-- Quick Access --}}
<section class="wrap" id="ferramentas">
  <div class="sec-head" style="text-align:center">
    <span class="eyebrow" style="justify-content:center" data-i18n="tools.eyebrow">Acesso rápido</span>
    <h2 data-i18n="tools.title">Suas ferramentas essenciais</h2>
  </div>
  <div class="quick-grid">
    <a class="qcard" href="{{ route('itens.index') }}">
      <span class="corner tl"></span><span class="corner tr"></span><span class="corner bl"></span><span class="corner br"></span>
      <div class="ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/>
        </svg>
      </div>
      <h3 data-i18n="tools.items.name">Itens</h3>
      <p data-i18n="tools.items.desc">Navegue pelo catálogo de itens, consulte preços de mercado e descubra receitas de craft.</p>
      <span class="go" data-i18n="tools.items.cta">Ver catálogo →</span>
    </a>
    <a class="qcard" href="{{ route('transporte.index') }}">
      <span class="corner tl"></span><span class="corner tr"></span><span class="corner bl"></span><span class="corner br"></span>
      <div class="ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
          <path d="M2 4h3l2.5 12h11"/><path d="M7 8h14l-1.5 7H8.5"/><circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/>
        </svg>
      </div>
      <h3 data-i18n="tools.transport.name">Transporte</h3>
      <p data-i18n="tools.transport.desc">Compare preços entre cidades e encontre as melhores oportunidades de arbitragem.</p>
      <span class="go" data-i18n="tools.transport.cta">Calcular rota →</span>
    </a>
    <a class="qcard" href="{{ route('crafting.index') }}">
      <span class="corner tl"></span><span class="corner tr"></span><span class="corner bl"></span><span class="corner br"></span>
      <div class="ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
          <path d="M14 6l4 4"/><path d="M3 21l9-9"/><path d="M14.5 5.5l4 4 2.5-2.5a2.8 2.8 0 0 0-4-4z"/><path d="M5 13l-2 2 3 3 2-2"/>
        </svg>
      </div>
      <h3 data-i18n="tools.craft.name">Craft</h3>
      <p data-i18n="tools.craft.desc">Calcule o custo de ingredientes e encontre os itens mais lucrativos para craftar.</p>
      <span class="go" data-i18n="tools.craft.cta">Abrir calculadora →</span>
    </a>
  </div>
</section>

{{-- Transport Table --}}
<section class="wrap" id="precos">
  <style>
    .home-city{font-family:"JetBrains Mono",monospace;font-size:10px;letter-spacing:.04em;color:var(--parch-faint);text-transform:uppercase;display:block;margin-bottom:2px}
    .profit-pos{font-family:"JetBrains Mono",monospace;color:#6db389}
    .pct-badge{display:inline-flex;align-items:center;padding:2px 7px;border-radius:3px;font-family:"JetBrains Mono",monospace;font-size:12px;font-weight:700}
    .pct-badge.strong{background:rgba(176,143,214,.22);color:#c8a8e8}
    .pct-badge.medium{background:rgba(111,168,200,.18);color:#8ec4dc}
    .pct-badge.light{background:rgba(232,184,75,.14);color:var(--gold-bright)}
    .pct-badge.neutral{background:rgba(255,255,255,.06);color:var(--parch-faint)}
    .maint-box{text-align:center;padding:60px 20px;border:1px dashed var(--line-soft);border-radius:8px;color:var(--parch-faint)}
    .maint-box svg{opacity:.3;margin-bottom:14px}
    .maint-box p{font-size:15px;margin:0}
    .maint-box small{font-family:"JetBrains Mono",monospace;font-size:11px;letter-spacing:.08em;opacity:.6;margin-top:6px;display:block}
  </style>

  <div class="sec-head row">
    <div>
      <span class="eyebrow solo" data-i18n="transport.eyebrow">Arbitragem</span>
      <h2 data-i18n="market.title">Maiores variações do dia</h2>
      <p class="lead" data-i18n="market.lead">Itens com maior diferença entre compra direta e melhor preço de venda.</p>
    </div>
    <a href="{{ route('transporte.index') }}" class="btn" data-i18n="market.viewall">Ver mercado completo →</a>
  </div>

  <div class="tablewrap">
    <table>
      <thead>
        <tr>
          <th data-i18n="market.col.item">Item</th>
          <th class="r hide" data-i18n="transport.col.menor_valor">Compra Direta</th>
          <th class="r hide" data-i18n="transport.col.maior_valor">Maior Venda</th>
          <th class="r" data-i18n="transport.col.lucro_direto">Lucro</th>
          <th class="r" data-i18n="transport.col.pct_direto">%</th>
        </tr>
      </thead>
      <tbody>
        @forelse($transportes as $row)
          @php
            $ench    = (int) $row->encantamento;
            $enchSuf = $ench > 0 ? ' .'.$ench : '';
            $nome    = ($row->item_portugues ?? $row->item_ingles) . $enchSuf;
            $pld     = (float) $row->pct_lucro_direto;
            $badge   = $pld >= 100 ? 'strong' : ($pld >= 50 ? 'medium' : ($pld >= 20 ? 'light' : 'neutral'));
          @endphp
          <tr>
            <td>
              <a href="{{ route('itens.mercado', $row->item_id) }}" style="font-weight:600;color:var(--parch);text-decoration:none"
                 data-name-pt="{{ $row->item_portugues }}{{ $enchSuf }}"
                 data-name-en="{{ $row->item_ingles }}{{ $enchSuf }}"
                 data-name-es="{{ $row->item_espanhol }}{{ $enchSuf }}"
                 data-name-fr="{{ $row->item_frances }}{{ $enchSuf }}">
                {{ $nome }}
              </a>
            </td>
            <td class="r hide">
              <span class="home-city"
                    data-city-pt="{{ $row->cidade_compra_pt }}"
                    data-city-en="{{ $row->cidade_compra_en }}"
                    data-city-es="{{ $row->cidade_compra_es }}"
                    data-city-fr="{{ $row->cidade_compra_fr }}">{{ $row->cidade_compra_pt }}</span>
              <span style="font-family:'JetBrains Mono',monospace;color:var(--parch)">
                {{ number_format($row->menor_valor, 0, ',', '.') }}
                <span style="font-size:10px;color:var(--parch-faint)">prata</span>
              </span>
            </td>
            <td class="r hide">
              <span class="home-city"
                    data-city-pt="{{ $row->cidade_venda_pt }}"
                    data-city-en="{{ $row->cidade_venda_en }}"
                    data-city-es="{{ $row->cidade_venda_es }}"
                    data-city-fr="{{ $row->cidade_venda_fr }}">{{ $row->cidade_venda_pt }}</span>
              <span style="font-family:'JetBrains Mono',monospace;color:var(--parch)">
                {{ number_format($row->maior_valor, 0, ',', '.') }}
                <span style="font-size:10px;color:var(--parch-faint)">prata</span>
              </span>
            </td>
            <td class="r">
              <span class="profit-pos">+{{ number_format($row->lucro_direto, 0, ',', '.') }}</span>
            </td>
            <td class="r">
              <span class="pct-badge {{ $badge }}">+{{ number_format($pld, 1, ',', '.') }}%</span>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" style="text-align:center;padding:40px;color:var(--parch-faint);font-style:italic">Sem dados disponíveis.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</section>

{{-- Donation --}}
<section class="wrap" style="padding-top:20px;padding-bottom:60px">
  <div class="donate" style="align-items:center;gap:48px">
    <div class="dico">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 21s-7.5-4.6-10-9.5C.5 8 2.5 4.5 6 4.5c2.2 0 3.6 1.3 4.5 2.8.9-1.5 2.3-2.8 4.5-2.8 3.5 0 5.5 3.5 4 7C19.5 16.4 12 21 12 21z"/>
      </svg>
    </div>
    <div class="dtext" style="flex:1">
      <h2 data-i18n="donate.title">O AlbionHub é mantido por jogadores</h2>
      <p data-i18n="donate.desc" style="margin-bottom:16px">Sem anúncios invasivos e sem paywall. Se as ferramentas te economizam prata todo dia, considere apoiar os servidores e o desenvolvimento contínuo.</p>
      <p style="font-size:14px;color:var(--parch-dim)">
        Faça um <b style="color:var(--gold-bright)">Pix</b> para a chave abaixo ou escaneie o QR code:
      </p>
      <div style="display:inline-flex;align-items:center;gap:10px;margin-top:10px;padding:10px 18px;background:rgba(0,0,0,.35);border:1px solid var(--line-soft);border-radius:4px">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--gold-bright)" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M7 7h.01M12 7h.01M17 7h.01M7 12h.01M12 12h.01M17 12h.01M7 17h.01M12 17h.01M17 17h.01"/></svg>
        <span style="font-family:'JetBrains Mono',monospace;font-size:14px;color:var(--parch);letter-spacing:.04em;user-select:all">joao.beleno@gmail.com</span>
      </div>
    </div>
    <div style="flex:0 0 auto;display:flex;flex-direction:column;align-items:center;gap:10px;z-index:999999">
      <div id="pixQrThumb" style="padding:10px;background:#fff;border-radius:8px;line-height:0;cursor:zoom-in" title="Clique para ampliar">
        <img src="{{ asset('images/pix-qrcode.png') }}" alt="QR Code Pix" width="160" height="160" style="display:block">
      </div>
      <span style="font-family:'JetBrains Mono',monospace;font-size:10px;letter-spacing:.1em;color:var(--parch-faint);text-transform:uppercase">Pix · clique para ampliar</span>
    </div>
  </div>
</section>

{{-- QR Code lightbox --}}
<div id="pixModal" style="display:none;position:fixed;inset:0;z-index:1;background:rgba(0,0,0,.85);align-items:center;justify-content:center">
  <div style="background:#1a1710;border:1px solid var(--line-soft);border-radius:12px;padding:32px;display:flex;flex-direction:column;align-items:center;gap:20px;box-shadow:0 24px 80px rgba(0,0,0,.8);max-width:90vw">

    {{-- Fechar --}}
    <button id="pixModalClose"
            style="align-self:flex-end;background:none;border:none;color:var(--parch-faint);cursor:pointer;font-size:20px;line-height:1;padding:0" title="Fechar">✕</button>

    {{-- QR Code --}}
    <div style="padding:14px;background:#fff;border-radius:8px;line-height:0">
      <img src="{{ asset('images/pix-qrcode.png') }}" alt="QR Code Pix" width="280" height="280" style="display:block">
    </div>

    {{-- Chave Pix + botão copiar --}}
    <div style="display:flex;align-items:center;width:100%;max-width:320px">
      <div style="flex:1;padding:10px 14px;background:rgba(0,0,0,.4);border:1px solid var(--line-soft);border-right:none;border-radius:4px 0 0 4px">
        <span id="pixKey" style="font-family:'JetBrains Mono',monospace;font-size:13px;color:var(--parch);letter-spacing:.03em">joao.beleno@gmail.com</span>
      </div>
      <button id="pixCopyBtn"
              style="padding:10px 16px;background:var(--gold-bright);border:none;border-radius:0 4px 4px 0;cursor:pointer;font-family:'Cinzel',serif;font-size:11px;font-weight:700;letter-spacing:.08em;color:#241b06;white-space:nowrap;transition:.18s">
        Copiar
      </button>
    </div>

    <p style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--parch-faint);letter-spacing:.06em;text-transform:uppercase;margin:0">Chave Pix · e-mail</p>
  </div>
</div>

{{-- Builds --}}
<section class="builds-bg" id="builds">
  <div class="wrap">
    <div class="sec-head">
      <span class="eyebrow solo" data-i18n="builds.eyebrow">Arsenal</span>
      <h2 data-i18n="builds.title">Builds em destaque</h2>
    </div>
    <div class="maint-box">
      <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
      <p>Em manutenção</p>
      <small>EM BREVE</small>
    </div>
  </div>
</section>

{{-- Blog --}}
<section class="wrap" id="blog">
  <div class="sec-head">
    <span class="eyebrow solo" data-i18n="blog.eyebrow">Pergaminhos</span>
    <h2 data-i18n="blog.title">Do blog</h2>
  </div>
  <div class="maint-box">
    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
    <p>Em manutenção</p>
    <small>EM BREVE</small>
  </div>
</section>

@endsection

@push('scripts')
<script>
(function () {
  /* ── i18n ──────────────────────────────────────────── */
  var LANG_COL = {'pt-BR':'pt','pt':'pt','en-US':'en','en':'en','es-ES':'es','es':'es','fr-FR':'fr','fr':'fr','nl-NL':'en','nl':'en'};
  document.addEventListener('i18n:ready', function(e) {
    var col = LANG_COL[e.detail.locale] || LANG_COL[e.detail.locale.split('-')[0]] || 'pt';
    var K = col.charAt(0).toUpperCase() + col.slice(1);
    var hs = document.getElementById('heroSearch');
    if (hs) hs.placeholder = I18n.t('hero.search.placeholder.items');
    document.querySelectorAll('[data-name-pt]').forEach(function(el){ el.textContent = el.dataset['name'+K] || el.dataset.nameEn || el.textContent; });
    document.querySelectorAll('[data-city-pt]').forEach(function(el){ el.textContent = el.dataset['city'+K] || el.dataset.cityEn || el.textContent; });
  });

  /* ── PIX modal ─────────────────────────────────────── */
  var modal    = document.getElementById('pixModal');
  var thumb    = document.getElementById('pixQrThumb');
  var closeBtn = document.getElementById('pixModalClose');

  if (thumb)    thumb.addEventListener('click',    function()  { modal.style.display = 'flex'; });
  if (closeBtn) closeBtn.addEventListener('click', function()  { modal.style.display = 'none'; });
  if (modal)    modal.addEventListener('click',    function(e) { if (e.target === modal) modal.style.display = 'none'; });
  document.addEventListener('keydown', function(e) { if (e.key === 'Escape' && modal) modal.style.display = 'none'; });

  /* ── copiar chave PIX ──────────────────────────────── */
  var copyBtn = document.getElementById('pixCopyBtn');
  var pixKey  = document.getElementById('pixKey');
  if (copyBtn && pixKey) {
    copyBtn.addEventListener('click', function() {
      var text = pixKey.textContent.trim();
      function done() {
        copyBtn.textContent = 'Copiado!';
        copyBtn.style.background = '#6db389';
        setTimeout(function(){ copyBtn.textContent = 'Copiar'; copyBtn.style.background = 'var(--gold-bright)'; }, 2000);
      }
      function fallback() {
        var ta = document.createElement('textarea');
        ta.value = text; ta.style.cssText = 'position:fixed;top:0;left:0;opacity:0';
        document.body.appendChild(ta); ta.focus(); ta.select();
        try { document.execCommand('copy'); done(); } catch(err) {}
        document.body.removeChild(ta);
      }
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(done).catch(fallback);
      } else { fallback(); }
    });
  }
}());
</script>
@endpush

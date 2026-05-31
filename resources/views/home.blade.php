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
      <div class="search-tabs" id="searchTabs">
        <button class="search-tab active" data-tab="items" data-i18n="hero.tab.items">Itens</button>
        <button class="search-tab" data-tab="builds" data-i18n="hero.tab.builds">Builds</button>
        <button class="search-tab" data-tab="guides" data-i18n="hero.tab.guides">Guias</button>
      </div>
      <div class="search-row">
        <div class="field">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#C8942A" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
          <input id="heroSearch" type="text"
            data-i18n-placeholder="hero.search.placeholder.items"
            placeholder="Espada Adamantita, Cajado de Fogo, Lingote de Titânio…" />
        </div>
        <button class="btn btn-gold" id="heroSearchBtn" data-i18n="hero.search.button">Buscar</button>
      </div>
    </div>

    <div class="hero-stats">
      <div class="s"><b data-i18n="hero.stat.items.value">12.4k</b><span data-i18n="hero.stat.items.label">Itens rastreados</span></div>
      <div class="s"><b data-i18n="hero.stat.builds.value">840+</b><span data-i18n="hero.stat.builds.label">Builds da meta</span></div>
      <div class="s"><b data-i18n="hero.stat.update.value">5 min</b><span data-i18n="hero.stat.update.label">Atualização de preços</span></div>
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
    <a class="qcard" href="#">
      <span class="corner tl"></span><span class="corner tr"></span><span class="corner bl"></span><span class="corner br"></span>
      <div class="ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
          <path d="M14 6l4 4"/><path d="M3 21l9-9"/><path d="M14.5 5.5l4 4 2.5-2.5a2.8 2.8 0 0 0-4-4z"/><path d="M5 13l-2 2 3 3 2-2"/>
        </svg>
      </div>
      <h3 data-i18n="tools.craft.name">Craft</h3>
      <p data-i18n="tools.craft.desc">Calcule lucro, taxas de retorno e o melhor foco de fabricação por cidade.</p>
      <span class="go" data-i18n="tools.craft.cta">Abrir calculadora →</span>
    </a>
    <a class="qcard" href="#">
      <span class="corner tl"></span><span class="corner tr"></span><span class="corner bl"></span><span class="corner br"></span>
      <div class="ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 3c2 3-1 4 0 6 1.5 2.5 3 2 3 5a5 5 0 1 1-9-1.5C7 9 11 8 12 3z"/>
        </svg>
      </div>
      <h3 data-i18n="tools.refine.name">Refino</h3>
      <p data-i18n="tools.refine.desc">Otimize minério, couro e fibra com bônus de cidade e retorno do foco.</p>
      <span class="go" data-i18n="tools.refine.cta">Abrir calculadora →</span>
    </a>
    <a class="qcard" href="#">
      <span class="corner tl"></span><span class="corner tr"></span><span class="corner bl"></span><span class="corner br"></span>
      <div class="ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
          <path d="M2 4h3l2.5 12h11"/><path d="M7 8h14l-1.5 7H8.5"/><circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/>
        </svg>
      </div>
      <h3 data-i18n="tools.transport.name">Transporte</h3>
      <p data-i18n="tools.transport.desc">Compare frete entre cidades, peso da carga e risco da rota até o destino.</p>
      <span class="go" data-i18n="tools.transport.cta">Calcular rota →</span>
    </a>
    <a class="qcard" href="#">
      <span class="corner tl"></span><span class="corner tr"></span><span class="corner bl"></span><span class="corner br"></span>
      <div class="ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
          <path d="M14.5 3.5l6 6L8 22l-4 1 1-4z"/><path d="M9.5 8.5l-6-6L2 6l6 6"/>
        </svg>
      </div>
      <h3 data-i18n="tools.pvp.name">Builds PvP</h3>
      <p data-i18n="tools.pvp.desc">Explore loadouts da meta para solo, small group e ZvZ com custo estimado.</p>
      <span class="go" data-i18n="tools.pvp.cta">Ver builds →</span>
    </a>
  </div>
</section>

{{-- Price Table --}}
<section class="wrap" id="precos">
  <div class="sec-head row">
    <div>
      <span class="eyebrow solo" data-i18n="market.eyebrow">Mercado</span>
      <h2 data-i18n="market.title">Maiores variações do dia</h2>
      <p class="lead" data-i18n="market.lead">Itens com maior oscilação de preço nas últimas 24 horas pelo Royal Market.</p>
    </div>
    <button class="btn" data-i18n="market.viewall">Ver mercado completo →</button>
  </div>

  <div class="tablewrap">
    <div class="table-top">
      <div class="live"><span class="pulse"></span><span data-i18n="market.live">Dados ao vivo · atualizado há 4 min</span></div>
      <div class="tfilter">
        <div class="tsearch">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#8a7f60" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
          <input id="tableSearch" type="text"
            data-i18n-placeholder="market.filter.placeholder"
            placeholder="Filtrar item…" />
        </div>
        <button class="chip active" data-f="all" data-i18n="market.filter.all">Todos</button>
        <button class="chip" data-f="craft" data-i18n="market.filter.craft">Craft</button>
        <button class="chip" data-f="refino" data-i18n="market.filter.refine">Refino</button>
      </div>
    </div>
    <table>
      <thead>
        <tr>
          <th data-i18n="market.col.item">Item</th>
          <th class="hide" data-i18n="market.col.city">Cidade</th>
          <th class="r" data-i18n="market.col.price">Preço</th>
          <th class="r" data-i18n="market.col.variation">Variação 24h</th>
          <th class="r hide" data-i18n="market.col.type">Tipo</th>
        </tr>
      </thead>
      <tbody id="priceBody"></tbody>
    </table>
  </div>
</section>

{{-- Builds --}}
<section class="builds-bg" id="builds">
  <div class="wrap">
    <div class="sec-head row">
      <div>
        <span class="eyebrow solo" data-i18n="builds.eyebrow">Arsenal</span>
        <h2 data-i18n="builds.title">Builds em destaque</h2>
        <p class="lead" data-i18n="builds.lead">Loadouts da meta atual, com conteúdo recomendado e custo estimado em prata.</p>
      </div>
      <div class="role-filter" id="roleFilter">
        <button class="chip active" data-r="all" data-i18n="builds.filter.all">Todas</button>
        <button class="chip" data-r="dps" data-i18n="builds.filter.dps">DPS</button>
        <button class="chip" data-r="tank" data-i18n="builds.filter.tank">Tank</button>
        <button class="chip" data-r="healer" data-i18n="builds.filter.healer">Healer</button>
        <button class="chip" data-r="solo" data-i18n="builds.filter.solo">Solo</button>
      </div>
    </div>
    <div class="builds-grid" id="buildsGrid"></div>
  </div>
</section>

{{-- Blog --}}
<section class="wrap" id="blog">
  <div class="sec-head row">
    <div>
      <span class="eyebrow solo" data-i18n="blog.eyebrow">Pergaminhos</span>
      <h2 data-i18n="blog.title">Do blog</h2>
      <p class="lead" data-i18n="blog.lead">Patch notes, análises de economia e guias escritos pela comunidade.</p>
    </div>
    <button class="btn" data-i18n="blog.viewall">Todos os artigos →</button>
  </div>
  <div class="blog-grid" id="blogGrid"></div>
</section>

{{-- Donation --}}
<section class="wrap" style="padding-top:20px">
  <div class="donate">
    <div class="dico">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 21s-7.5-4.6-10-9.5C.5 8 2.5 4.5 6 4.5c2.2 0 3.6 1.3 4.5 2.8.9-1.5 2.3-2.8 4.5-2.8 3.5 0 5.5 3.5 4 7C19.5 16.4 12 21 12 21z"/>
      </svg>
    </div>
    <div class="dtext">
      <h2 data-i18n="donate.title">O AlbionHub é mantido por jogadores</h2>
      <p data-i18n="donate.desc">Sem anúncios invasivos e sem paywall. Se as ferramentas te economizam prata todo dia, considere apoiar os servidores e o desenvolvimento contínuo.</p>
    </div>
    <div class="dact">
      <button class="btn btn-gold" data-i18n="donate.support">Apoiar o site</button>
      <button class="btn" data-i18n="donate.learnmore">Saiba mais</button>
    </div>
  </div>
</section>

@endsection

@push('scripts')
<script>
  const IMG = {
    battle:     '{{ asset("images/battle.jpg") }}',
    hero:       '{{ asset("images/hero.jpg") }}',
    transporte: '{{ asset("images/transporte.jpg") }}',
  };

  /* ── static data (keys reference locales) ──────────────────── */
  const prices = [
    {t:'8.1', n:'Espada Adamantita',    city:'Caerleon',      price:'1.284.500', v:18.4,  type:'craft'},
    {t:'6',   n:'Barra de Aço',         city:'Bridgewatch',   price:'4.120',     v:12.1,  type:'refino'},
    {t:'7',   n:'Manto de Especialista',city:'Martlock',      price:'248.900',   v:-9.7,  type:'craft'},
    {t:'5',   n:'Couro Curtido',        city:'Fort Sterling', price:'1.860',     v:7.3,   type:'refino'},
    {t:'8',   n:'Cajado de Fogo Grande',city:'Thetford',      price:'932.000',   v:-6.2,  type:'craft'},
    {t:'7',   n:'Tábua de Carvalho',    city:'Lymhurst',      price:'2.540',     v:5.9,   type:'refino'},
    {t:'6.2', n:'Elmo Pesado de Placas',city:'Caerleon',      price:'86.400',    v:4.4,   type:'craft'},
    {t:'8',   n:'Lingote de Titânio',   city:'Bridgewatch',   price:'9.870',     v:-3.8,  type:'refino'},
  ];

  const builds = [
    {role:'dps',    nameKey:'build.curtain_caller.name',   contentKey:'build.curtain_caller.content',   descKey:'build.curtain_caller.desc',   cost:'2.4M', img:IMG.battle},
    {role:'tank',   nameKey:'build.iron_wall.name',        contentKey:'build.iron_wall.content',        descKey:'build.iron_wall.desc',        cost:'3.1M', img:null},
    {role:'healer', nameKey:'build.forest_blessing.name',  contentKey:'build.forest_blessing.content',  descKey:'build.forest_blessing.desc',  cost:'1.8M', img:null},
    {role:'dps',    nameKey:'build.ice_storm.name',        contentKey:'build.ice_storm.content',        descKey:'build.ice_storm.desc',        cost:'2.7M', img:IMG.hero},
    {role:'solo',   nameKey:'build.shadow_hunter.name',    contentKey:'build.shadow_hunter.content',    descKey:'build.shadow_hunter.desc',    cost:'1.5M', img:null},
    {role:'dps',    nameKey:'build.ash_inferno.name',      contentKey:'build.ash_inferno.content',      descKey:'build.ash_inferno.desc',      cost:'3.6M', img:IMG.transporte},
  ];

  const posts = [
    {tagKey:'post.patch_horizon.tag',    titleKey:'post.patch_horizon.title',    dateKey:'post.patch_horizon.date',    img:IMG.battle,     ph:null},
    {tagKey:'post.top5_builds.tag',      titleKey:'post.top5_builds.title',      dateKey:'post.top5_builds.date',      img:null,           ph:'Cover art'},
    {tagKey:'post.transport_routes.tag', titleKey:'post.transport_routes.title', dateKey:'post.transport_routes.date', img:IMG.transporte, ph:null},
    {tagKey:'post.avalon_roads.tag',     titleKey:'post.avalon_roads.title',     dateKey:'post.avalon_roads.date',     img:null,           ph:'Cover art'},
  ];

  /* ── table filter state ─────────────────────────────────────── */
  let tFilter = 'all';

  /* ── render functions (use I18n.t for all text) ─────────────── */
  function t(key) { return I18n.t(key); }

  function renderTable(rows) {
    const body = document.getElementById('priceBody');
    if (!rows.length) {
      body.innerHTML = '<tr class="no-rows"><td colspan="5">' + t('market.empty') + '</td></tr>';
      return;
    }
    body.innerHTML = rows.map(r => {
      const up = r.v >= 0;
      return `<tr>
        <td><div class="item-cell"><span class="tier">T${r.t}</span><span class="nm">${r.n}</span></div></td>
        <td class="hide"><span class="city">${r.city}</span></td>
        <td class="r"><span class="price">${r.price} <span class="ag">${t('market.currency')}</span></span></td>
        <td class="r"><span class="var ${up ? 'up' : 'down'}"><span class="arr">${up ? '▲' : '▼'}</span>${up ? '+' : ''}${r.v.toFixed(1).replace('.', ',')}%</span></td>
        <td class="r hide"><span class="badge ${r.type}">${r.type}</span></td>
      </tr>`;
    }).join('');
  }

  function applyTable() {
    const q = (document.getElementById('tableSearch').value || '').trim().toLowerCase();
    renderTable(prices.filter(r =>
      (tFilter === 'all' || r.type === tFilter) &&
      (r.n.toLowerCase().includes(q) || r.city.toLowerCase().includes(q))
    ));
  }

  function renderBuilds(list) {
    document.getElementById('buildsGrid').innerHTML = list.map(b => `
      <div class="build">
        <div class="art ${b.img ? '' : 'placeholder'}">
          <span class="role-tag ${b.role}">${t('builds.filter.' + b.role)}</span>
          ${b.img ? `<img src="${b.img}" alt="${t(b.nameKey)}">` : ''}
        </div>
        <div class="body">
          <h3>${t(b.nameKey)}</h3>
          <div class="content-tag">${t(b.contentKey)}</div>
          <p class="desc">${t(b.descKey)}</p>
          <div class="foot">
            <div class="cost"><b>${b.cost}</b><span>${t('builds.cost.label')}</span></div>
            <span class="view">${t('builds.view.cta')}</span>
          </div>
        </div>
      </div>`).join('');
  }

  function renderBlog() {
    document.getElementById('blogGrid').innerHTML = posts.map(p => `
      <div class="post">
        <div class="pic">
          <span class="ptag">${t(p.tagKey)}</span>
          ${p.img ? `<img src="${p.img}" alt="">` : `<span class="ph">${p.ph}</span>`}
        </div>
        <div class="pbody">
          <h3>${t(p.titleKey)}</h3>
          <div class="date">${t(p.dateKey)}</div>
        </div>
      </div>`).join('');
  }

  function renderAll() {
    applyTable();
    renderBuilds(builds);
    renderBlog();
    /* sync active tab placeholder */
    const activeTab = document.querySelector('.search-tab.active');
    if (activeTab) {
      const key = 'hero.search.placeholder.' + activeTab.dataset.tab;
      document.getElementById('heroSearch').placeholder = t(key);
    }
  }

  /* ── react to locale changes ────────────────────────────────── */
  document.addEventListener('i18n:ready', renderAll);

  /* ── search tab clicks ──────────────────────────────────────── */
  document.querySelectorAll('.search-tab').forEach(tab => {
    tab.addEventListener('click', () => {
      document.querySelectorAll('.search-tab').forEach(x => x.classList.remove('active'));
      tab.classList.add('active');
      const key = 'hero.search.placeholder.' + tab.dataset.tab;
      tab.dataset.i18nPlaceholder && delete tab.dataset.i18nPlaceholder;
      const input = document.getElementById('heroSearch');
      input.dataset.i18nPlaceholder = key;
      input.placeholder = t(key);
    });
  });

  document.getElementById('heroSearchBtn').addEventListener('click', () => {
    const q = document.getElementById('heroSearch').value.trim();
    if (q) {
      document.getElementById('tableSearch').value = q;
      applyTable();
      document.getElementById('precos').scrollIntoView({ behavior: 'smooth' });
    }
  });
  document.getElementById('heroSearch').addEventListener('keydown', e => {
    if (e.key === 'Enter') document.getElementById('heroSearchBtn').click();
  });

  /* ── price table filters ────────────────────────────────────── */
  document.querySelectorAll('.tfilter .chip').forEach(c => c.addEventListener('click', () => {
    document.querySelectorAll('.tfilter .chip').forEach(x => x.classList.remove('active'));
    c.classList.add('active');
    tFilter = c.dataset.f;
    applyTable();
  }));
  document.getElementById('tableSearch').addEventListener('input', applyTable);

  /* ── role filter ────────────────────────────────────────────── */
  document.querySelectorAll('#roleFilter .chip').forEach(c => c.addEventListener('click', () => {
    document.querySelectorAll('#roleFilter .chip').forEach(x => x.classList.remove('active'));
    c.classList.add('active');
    const r = c.dataset.r;
    renderBuilds(r === 'all' ? builds : builds.filter(b => b.role === r));
  }));
</script>
@endpush

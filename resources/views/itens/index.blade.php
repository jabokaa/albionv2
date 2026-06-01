@extends('layouts.app')

@section('title', 'Catálogo de Itens — AlbionHub')

@push('styles')
<style>
  /* ── enchantment palette ─────────────────────────── */
  :root{
    --e0:#8a7f60;--e1:oklch(0.72 0.13 150);--e2:#6fa8c8;--e3:#b08fd6;--e4:#E8B84B;
  }

  /* ── page header ─────────────────────────────────── */
  .page-head{position:relative;border-bottom:1px solid var(--line);overflow:hidden;background:linear-gradient(180deg,#1d1910,var(--bg))}
  .page-head::before{content:"";position:absolute;right:-80px;top:-120px;width:420px;height:420px;border-radius:50%;background:radial-gradient(circle,rgba(232,184,75,.12),transparent 65%)}
  .page-head-inner{position:relative;z-index:2;padding:48px 28px 40px;max-width:1240px;margin:0 auto}
  .crumb{font-family:"JetBrains Mono",monospace;font-size:12px;letter-spacing:.08em;color:var(--parch-faint);margin-bottom:18px}
  .crumb a:hover{color:var(--gold-bright)}
  .page-head h1{font-size:clamp(32px,5vw,52px);font-weight:900;margin-top:12px}
  .page-head .lead{margin-top:12px;color:var(--parch-dim);max-width:640px;font-weight:300;font-size:18px}

  /* ── toolbar ─────────────────────────────────────── */
  .toolbar{position:sticky;top:74px;z-index:120;background:rgba(18,16,11,.92);backdrop-filter:blur(10px);border-bottom:1px solid var(--line-soft);padding:16px 0}
  .toolbar-inner{display:flex;align-items:center;gap:16px;flex-wrap:wrap}
  .searchbox{display:flex;align-items:center;gap:11px;background:rgba(0,0,0,.4);border:1px solid var(--line-soft);border-radius:3px;padding:0 15px;flex:1;min-width:240px}
  .searchbox input{flex:1;background:none;border:0;outline:none;color:var(--parch);font-family:"Spectral",serif;font-size:16px;padding:12px 0}
  .searchbox input::placeholder{color:var(--parch-faint)}
  .count{font-family:"JetBrains Mono",monospace;font-size:13px;color:var(--parch-faint);white-space:nowrap}
  .count b{color:var(--gold-bright)}

  /* ── filters ─────────────────────────────────────── */
  .filters{display:flex;align-items:center;gap:8px;flex-wrap:wrap;padding:18px 0 4px}
  .filter-label{font-family:"Cinzel",serif;font-size:11px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;color:var(--parch-faint);margin-right:4px;white-space:nowrap}
  .chip{padding:7px 14px;font-family:"Cinzel",serif;font-size:12px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--parch-faint);border:1px solid var(--line-soft);background:none;cursor:pointer;border-radius:20px;transition:.18s;white-space:nowrap;text-decoration:none;display:inline-flex;align-items:center;gap:7px}
  .chip.active{color:#241b06;background:var(--gold-bright);border-color:var(--gold-bright)}
  .chip:not(.active):hover{color:var(--gold-bright);border-color:var(--line)}
  .category-filter{display:flex;align-items:center;gap:10px;flex-wrap:wrap;width:100%}
  .select-shell{position:relative;min-width:260px;flex:1 1 320px;max-width:520px;width:100%}
  .select-shell::after{content:"";position:absolute;right:15px;top:50%;width:8px;height:8px;margin-top:-7px;border-right:2px solid var(--gold-bright);border-bottom:2px solid var(--gold-bright);transform:rotate(45deg);pointer-events:none;opacity:.9}
  .category-select{width:100%;height:42px;appearance:none;-webkit-appearance:none;-moz-appearance:none;padding:0 40px 0 14px;font-family:"Cinzel",serif;font-size:12px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--parch);border:1px solid var(--line-soft);background:rgba(0,0,0,.38);border-radius:20px;cursor:pointer;transition:.18s;outline:none;display:block}
  .category-select:hover,.category-select:focus{border-color:var(--gold);box-shadow:0 0 0 1px rgba(232,184,75,.12)}
  .category-select option{background:#1b170f;color:var(--parch)}
  .echip .gem{width:9px;height:9px;transform:rotate(45deg);border-radius:1px}
  .echip[data-e="0"] .gem{background:var(--e0)}.echip[data-e="1"] .gem{background:var(--e1)}
  .echip[data-e="2"] .gem{background:var(--e2)}.echip[data-e="3"] .gem{background:var(--e3)}
  .echip[data-e="4"] .gem{background:var(--e4)}

  /* ── item grid ───────────────────────────────────── */
  .grid{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;padding:30px 0 20px}
  .item-card{position:relative;background:linear-gradient(180deg,var(--panel),#19150a);border:1px solid var(--line-soft);border-radius:6px;overflow:hidden;transition:.25s ease;display:flex;flex-direction:column}
  .item-card:hover{transform:translateY(-5px);border-color:var(--line);box-shadow:var(--shadow)}
  .item-card[hidden]{display:none!important}
  .thumb{position:relative;aspect-ratio:1;display:flex;align-items:center;justify-content:center;
    background:radial-gradient(circle at 50% 38%,rgba(200,148,42,.10),transparent 62%),
    repeating-linear-gradient(135deg,#221d10,#221d10 12px,#1d190d 12px,#1d190d 24px);
    border-bottom:1px solid var(--line-soft)}
  .thumb img{width:78%;height:78%;object-fit:contain;filter:drop-shadow(0 8px 14px rgba(0,0,0,.55))}
  .thumb .fallback{display:none;flex-direction:column;align-items:center;gap:8px;color:var(--parch-faint);font-family:"JetBrains Mono",monospace;font-size:10px;letter-spacing:.12em;text-transform:uppercase}
  .thumb .fallback svg{width:42px;height:42px;opacity:.5}
  .thumb.err img{display:none}.thumb.err .fallback{display:flex}
  .tier-badge{position:absolute;top:9px;left:9px;font-family:"JetBrains Mono",monospace;font-weight:700;font-size:11px;color:var(--gold-bright);border:1px solid var(--line);background:rgba(13,12,8,.7);padding:3px 7px;border-radius:3px}
  .ench-badge{position:absolute;top:9px;right:9px;display:flex;align-items:center;gap:5px;font-family:"JetBrains Mono",monospace;font-weight:700;font-size:11px;color:var(--parch);border:1px solid var(--line);background:rgba(13,12,8,.7);padding:3px 8px;border-radius:3px}
  .ench-badge .gem{width:8px;height:8px;transform:rotate(45deg);border-radius:1px}
  .ench-badge[data-e="0"] .gem{background:var(--e0)}.ench-badge[data-e="1"] .gem{background:var(--e1)}
  .ench-badge[data-e="2"] .gem{background:var(--e2)}.ench-badge[data-e="3"] .gem{background:var(--e3)}
  .ench-badge[data-e="4"] .gem{background:var(--e4)}
  .item-body{padding:15px 16px 16px;flex:1;display:flex;flex-direction:column}
  .cat-line{font-family:"JetBrains Mono",monospace;font-size:11px;letter-spacing:.06em;text-transform:uppercase;color:var(--gold);margin-bottom:7px}
  .item-card h3{font-size:17px;font-weight:600;line-height:1.25;color:var(--parch)}
  .ench-line{margin-top:6px;font-size:13px;color:var(--parch-faint);font-family:"JetBrains Mono",monospace}
  .actions{display:flex;gap:8px;margin-top:16px}
  .actions .btn{flex:1;font-size:12px;padding:9px 12px;gap:7px}
  .no-rows{grid-column:1/-1;text-align:center;color:var(--parch-faint);padding:70px 20px;font-style:italic;border:1px dashed var(--line-soft);border-radius:8px}

  /* ── pagination ──────────────────────────────────── */
  .pagination-wrap{display:flex;justify-content:center;gap:8px;padding:16px 0 60px;flex-wrap:wrap}
  .pagination-wrap .page-link,.pagination-wrap .page-item>a,.pagination-wrap .page-item>span{
    font-family:"Cinzel",serif;font-size:13px;font-weight:600;letter-spacing:.06em;
    padding:9px 15px;border:1px solid var(--line-soft);background:transparent;color:var(--parch-dim);
    border-radius:3px;display:inline-flex;align-items:center;transition:.18s;cursor:pointer;text-decoration:none;
  }
  .pagination-wrap .page-item.active>span,.pagination-wrap .page-item.active>a{
    background:var(--gold-bright);border-color:var(--gold-bright);color:#241b06;
  }
  .pagination-wrap .page-item>a:hover{border-color:var(--gold);color:var(--gold-bright)}
  .pagination-wrap .page-item.disabled>span{opacity:.4;cursor:default}

  /* ── toast ───────────────────────────────────────── */
  .toast{position:fixed;left:50%;bottom:34px;transform:translateX(-50%) translateY(20px);
    background:linear-gradient(180deg,#241d0d,#16130b);border:1px solid var(--gold);color:var(--parch);
    padding:14px 22px;border-radius:5px;box-shadow:var(--shadow);display:flex;align-items:center;gap:12px;
    opacity:0;pointer-events:none;transition:.3s ease;z-index:400;font-size:15px}
  .toast.show{opacity:1;transform:translateX(-50%) translateY(0)}
  .toast .tdot{width:8px;height:8px;background:var(--gold-bright);transform:rotate(45deg);flex:0 0 auto}

  @media(max-width:1080px){.grid{grid-template-columns:repeat(3,1fr)}}
  @media(max-width:760px){.grid{grid-template-columns:repeat(2,1fr)}}
  @media(max-width:680px){.page-head-inner{padding:36px 18px 30px}.toolbar{top:74px}}
  @media(max-width:440px){.grid{grid-template-columns:1fr}}
</style>
@endpush

@section('content')

{{-- ── PAGE HEADER ──────────────────────────────────────── --}}
<div class="page-head">
  <div class="page-head-inner">
    <div class="crumb">
      <a href="{{ url('/') }}" data-i18n="items.crumb.home">Início</a>
      / <span data-i18n="items.crumb.economy">Economia</span>
      / <span style="color:var(--parch-dim)" data-i18n="items.crumb">Catálogo de Itens</span>
    </div>
    <span class="eyebrow solo" data-i18n="items.eyebrow">Compêndio</span>
    <h1 data-i18n="items.title">Catálogo de Itens</h1>
    <p class="lead" data-i18n="items.lead">Navegue por armas, armaduras e recursos com seus encantamentos. Mande craftar ou consulte o preço de mercado direto da listagem.</p>
  </div>
</div>

{{-- ── TOOLBAR ──────────────────────────────────────────── --}}
<div class="toolbar">
  <div class="wrap toolbar-inner">
    <form method="GET" action="{{ route('itens.index') }}" style="display:contents" id="searchForm">
      @if($categoriaId)
        <input type="hidden" name="categoria" value="{{ $categoriaId }}">
      @endif
      @if($encantamento !== null && $encantamento !== '')
        <input type="hidden" name="encantamento" value="{{ $encantamento }}">
      @endif
      <div class="searchbox">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#C8942A" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
        <input id="searchInput" name="busca" type="text" value="{{ $busca }}"
               data-i18n-placeholder="items.search.placeholder"
               placeholder="Buscar item pelo nome…" autocomplete="off" />
      </div>
    </form>
    <div class="count" id="count">
      <b>{{ $itens->total() }}</b>&nbsp;<span data-i18n="items.count.label">itens</span>
    </div>
  </div>
</div>

<div class="wrap">

  {{-- ── CATEGORY FILTER ──────────────────────────────────── --}}
  <div class="filters" id="catFilter">
    <form method="GET" action="{{ route('itens.index') }}" class="category-filter">
      @if($busca)
        <input type="hidden" name="busca" value="{{ $busca }}">
      @endif
      @if($encantamento !== null && $encantamento !== '')
        <input type="hidden" name="encantamento" value="{{ $encantamento }}">
      @endif
      <span class="filter-label" data-i18n="items.filter.category">Categoria</span>
      <div class="select-shell">
        <select class="category-select" name="categoria" onchange="this.form.submit()">
          <option value="" {{ !$categoriaId ? 'selected' : '' }} data-i18n="items.filter.all">Todos</option>
          @foreach($categorias as $cat)
            <option value="{{ $cat->id }}" {{ (string) $categoriaId === (string) $cat->id ? 'selected' : '' }}
                    data-name-pt="{{ $cat->portugues }}"
                    data-name-en="{{ $cat->ingles }}"
                    data-name-es="{{ $cat->espanhol }}"
                    data-name-fr="{{ $cat->frances }}">
              {{ $cat->portugues ?? $cat->ingles }}
            </option>
          @endforeach
        </select>
      </div>
    </form>
  </div>

  {{-- ── ENCHANTMENT FILTER (server-side) ─────────────────── --}}
  <div class="filters" id="enchFilter">
    <span class="filter-label" data-i18n="items.filter.enchantment">Encantamento</span>
    @php $baseParams = array_filter(['busca' => $busca, 'categoria' => $categoriaId]); @endphp
    <a href="{{ route('itens.index', $baseParams) }}"
       class="chip {{ $encantamento === null || $encantamento === '' ? 'active' : '' }}"
       data-i18n="items.filter.all">Todos</a>
    @foreach([0,1,2,3,4] as $e)
      <a href="{{ route('itens.index', $baseParams + ['encantamento' => $e]) }}"
         class="chip echip {{ (string) $encantamento === (string) $e ? 'active' : '' }}"
         data-e="{{ $e }}">
        <span class="gem"></span>.{{ $e }}
      </a>
    @endforeach
  </div>

  {{-- ── ITEM GRID ─────────────────────────────────────────── --}}
  <div class="grid" id="grid">
    @forelse($itens as $item)
      @php
        $tier  = preg_match('/^T(\d+)_/', $item->id_externo, $m) ? 'T'.$m[1] : '';
        $ench  = (int) $item->encantamento;
        $imgUrl = 'https://render.albiononline.com/v1/item/'
                . $item->id_externo
                . ($ench > 0 ? '@'.$ench : '')
                . '.png?size=217&quality=1';
        $catNome = optional($item->categoria)->portugues ?? optional($item->categoria)->ingles ?? '';
      @endphp
      <div class="item-card"
           data-ench="{{ $ench }}"
           data-name="{{ strtolower($item->ingles ?? '') }}"
           data-name-pt="{{ $item->portugues ?? $item->ingles }}"
           data-name-en="{{ $item->ingles }}"
           data-name-es="{{ $item->espanhol ?? $item->ingles }}"
           data-name-fr="{{ $item->frances ?? $item->ingles }}">

        <div class="thumb">
          @if($tier)
            <span class="tier-badge">{{ $tier }}</span>
          @endif
          <span class="ench-badge" data-e="{{ $ench }}">
            <span class="gem"></span>.{{ $ench }}
          </span>
          <img src="{{ $imgUrl }}" alt="{{ $item->ingles }}" loading="lazy"
               onerror="this.closest('.thumb').classList.add('err')">
          <span class="fallback">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <rect x="3" y="3" width="18" height="18" rx="2"/>
              <path d="M3 16l5-5 4 4 4-4 5 5"/>
              <circle cx="9" cy="9" r="1.5"/>
            </svg>
            icon
          </span>
        </div>

        <div class="item-body">
          <div class="cat-line">{{ $catNome }}</div>
          <h3>{{ $item->portugues ?? $item->ingles }}</h3>
          <div class="ench-line">
            <span data-i18n="items.filter.enchantment">Encantamento</span> {{ $ench }}
            @if($ench === 0)<span data-i18n="items.ench.base">(base)</span>@endif
          </div>
          <div class="actions">
            @if($item->receita)
            <a href="{{ route('itens.craft', $item->id) }}"
               class="btn btn-gold btn-sm"
               data-i18n="items.btn.craft">
              Craftar
            </a>
            @endif
            <a href="{{ route('itens.mercado', $item->id) }}"
               class="btn btn-sm"
               data-i18n="items.btn.market">
              Mercado
            </a>
          </div>
        </div>
      </div>
    @empty
      <div class="no-rows" data-i18n="items.empty">Nenhum item encontrado com esses filtros.</div>
    @endforelse
  </div>

  {{-- ── PAGINATION ────────────────────────────────────────── --}}
  @if($itens->hasPages())
    <div class="pagination-wrap">
      {{ $itens->links() }}
    </div>
  @endif

</div>

{{-- ── TOAST ─────────────────────────────────────────────── --}}
<div class="toast" id="toast">
  <span class="tdot"></span>
  <span id="toastMsg"></span>
</div>

@endsection

@push('scripts')
<script>
(function () {
  /* ── locale → column map ─────────────────────────── */
  const LANG_COL = {
    'pt-BR': 'pt', 'pt': 'pt',
    'en-US': 'en', 'en': 'en',
    'es-ES': 'es', 'es': 'es',
    'fr-FR': 'fr', 'fr': 'fr',
    'nl-NL': 'en', 'nl': 'en',
  };

  /* ── live search (client-side, current page only) ── */
  const searchInput = document.getElementById('searchInput');

  /* ── i18n: update item names and category chips ──── */
  function applyLocale(locale) {
    const col = LANG_COL[locale] || LANG_COL[locale.split('-')[0]] || 'pt';
    const optionKey = 'name' + col.charAt(0).toUpperCase() + col.slice(1);

    /* item names + category lines */
    document.querySelectorAll('.item-card').forEach(card => {
      const name = card.dataset['name' + col.charAt(0).toUpperCase() + col.slice(1)]
                || card.dataset.nameEn
                || '';
      const h3 = card.querySelector('h3');
      if (h3) h3.textContent = name;

      /* search data-name attribute used for live filtering */
      card.dataset.name = name.toLowerCase();

      /* button labels */
      card.querySelectorAll('button[data-i18n-label]').forEach(btn => {
        btn.textContent = I18n.t(btn.dataset.i18nLabel);
      });
    });

    /* category select labels */
    document.querySelectorAll('#catFilter option[data-name-pt], #catFilter option[data-name-en], #catFilter option[data-name-es], #catFilter option[data-name-fr]').forEach(option => {
      const name = option.dataset[optionKey] || option.dataset.nameEn || option.textContent;
      option.textContent = name;
    });
  }

  document.addEventListener('i18n:ready', e => applyLocale(e.detail.locale));

  let searchTimer;
  if (searchInput) {
    searchInput.addEventListener('input', () => {
      clearTimeout(searchTimer);
      searchTimer = setTimeout(() => {
        const q = searchInput.value.trim().toLowerCase();
        document.querySelectorAll('.item-card').forEach(card => {
          card.hidden = q.length >= 3 && !(card.dataset.name || '').includes(q);
        });
      }, 250);
    });
  }

  /* ── toast ───────────────────────────────────────── */
  const toast    = document.getElementById('toast');
  const toastMsg = document.getElementById('toastMsg');
  let toastTimer;
  function showToast(html) {
    toastMsg.innerHTML = html;
    toast.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toast.classList.remove('show'), 2800);
  }

  document.getElementById('grid').addEventListener('click', e => {
    const btn = e.target.closest('button[data-act]');
    if (!btn) return;
    const name = btn.dataset.name;
    if (btn.dataset.act === 'craft') {
      showToast(I18n.t('items.toast.craft', { name: `<b>${name}</b>` }));
    } else {
      showToast(I18n.t('items.toast.market', { name: `<b>${name}</b>` }));
    }
  });
})();
</script>
@endpush

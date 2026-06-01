@extends('layouts.app')

@section('title', 'Oportunidades de Craft — AlbionHub')

@push('styles')
<style>
  /* ── quality palette ─────────────────────────────── */
  :root{
    --q1:#8a7f60;--q2:oklch(0.72 0.13 150);--q3:#6fa8c8;--q4:#b08fd6;--q5:#E8B84B;
    --profit-light:rgba(232,184,75,.07);
    --profit-medium:rgba(111,168,200,.10);
    --profit-strong:rgba(176,143,214,.13);
  }

  /* ── page header ─────────────────────────────────── */
  .page-head{position:relative;border-bottom:1px solid var(--line);overflow:hidden;background:linear-gradient(180deg,#1d1910,var(--bg))}
  .page-head::before{content:"";position:absolute;right:-80px;top:-120px;width:420px;height:420px;border-radius:50%;background:radial-gradient(circle,rgba(232,184,75,.12),transparent 65%)}
  .page-head-inner{position:relative;z-index:2;padding:48px 28px 40px;max-width:1240px;margin:0 auto}
  .crumb{font-family:"JetBrains Mono",monospace;font-size:12px;letter-spacing:.08em;color:var(--parch-faint);margin-bottom:18px}
  .crumb a:hover{color:var(--gold-bright)}
  .page-head h1{font-size:clamp(28px,4vw,44px);font-weight:900;margin-top:12px}
  .page-head .lead{margin-top:12px;color:var(--parch-dim);max-width:640px;font-weight:300;font-size:17px}

  /* ── filters panel ───────────────────────────────── */
  .filters-panel{background:linear-gradient(180deg,#1a1710,#14120a);border-bottom:1px solid var(--line-soft);padding:20px 0}
  .filters-toggle{display:none;align-items:center;gap:10px;font-family:"Cinzel",serif;font-size:12px;font-weight:600;letter-spacing:.1em;text-transform:uppercase;color:var(--parch-faint);background:none;border:1px solid var(--line-soft);padding:9px 18px;border-radius:3px;cursor:pointer;margin-bottom:4px;transition:.18s}
  .filters-toggle:hover{color:var(--gold-bright);border-color:var(--gold)}
  .filters-toggle svg{transition:.2s}
  .filters-toggle.open svg{transform:rotate(180deg)}
  .filters-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:12px 16px}
  .filter-group{display:flex;flex-direction:column;gap:5px}
  .filter-group label{font-family:"JetBrains Mono",monospace;font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:var(--parch-faint)}
  .filter-input{width:100%;height:36px;background:rgba(0,0,0,.38);border:1px solid var(--line-soft);border-radius:3px;color:var(--parch);font-family:"Spectral",serif;font-size:14px;padding:0 10px;outline:none;transition:.18s}
  .filter-input:focus{border-color:var(--gold);box-shadow:0 0 0 1px rgba(232,184,75,.12)}
  .filter-input::placeholder{color:var(--parch-faint)}
  .filter-select{width:100%;height:36px;appearance:none;-webkit-appearance:none;background:rgba(0,0,0,.38) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%23C8942A'/%3E%3C/svg%3E") no-repeat right 10px center;border:1px solid var(--line-soft);border-radius:3px;color:var(--parch);font-family:"Cinzel",serif;font-size:11px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;padding:0 28px 0 10px;outline:none;cursor:pointer;transition:.18s}
  .filter-select:focus{border-color:var(--gold)}
  .filter-select option{background:#1b170f;color:var(--parch);text-transform:none;font-family:sans-serif;font-size:13px;font-weight:400;letter-spacing:0}
  .filters-actions{display:flex;flex-direction:row;gap:10px;align-items:center}
  .filters-actions .btn{height:36px;padding:0 18px;font-size:12px;white-space:nowrap}
  .toggle-irreal{display:inline-flex;align-items:center;gap:8px;height:36px;padding:0 14px;border:1px solid var(--line-soft);border-radius:3px;cursor:pointer;font-family:"JetBrains Mono",monospace;font-size:11px;letter-spacing:.06em;text-transform:uppercase;color:var(--parch-faint);transition:.18s;white-space:nowrap;user-select:none}
  .toggle-irreal:hover{border-color:var(--gold);color:var(--gold-bright)}
  .toggle-irreal.active{background:rgba(232,184,75,.12);border-color:var(--gold);color:var(--gold-bright)}
  .toggle-dot{width:8px;height:8px;border-radius:50%;background:var(--line);flex:0 0 auto;transition:.18s}
  .toggle-irreal.active .toggle-dot{background:var(--gold-bright)}

  /* ── toolbar ─────────────────────────────────────── */
  .results-bar{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;padding:16px 0 10px}
  .results-info{font-family:"JetBrains Mono",monospace;font-size:13px;color:var(--parch-faint)}
  .results-info b{color:var(--gold-bright)}
  .per-page-wrap{display:flex;align-items:center;gap:8px}
  .per-page-label{font-family:"JetBrains Mono",monospace;font-size:12px;color:var(--parch-faint);white-space:nowrap}
  .per-page-select{height:30px;appearance:none;-webkit-appearance:none;background:rgba(0,0,0,.38) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%23C8942A'/%3E%3C/svg%3E") no-repeat right 8px center;border:1px solid var(--line-soft);border-radius:3px;color:var(--parch);font-family:"JetBrains Mono",monospace;font-size:12px;padding:0 24px 0 10px;outline:none;cursor:pointer}

  /* ── legend ──────────────────────────────────────── */
  .legend{display:flex;gap:16px;flex-wrap:wrap;padding:0 0 12px}
  .legend-item{display:flex;align-items:center;gap:6px;font-family:"JetBrains Mono",monospace;font-size:11px;color:var(--parch-faint)}
  .legend-dot{width:10px;height:10px;border-radius:2px;flex:0 0 auto}
  .legend-dot.light{background:rgba(232,184,75,.5)}
  .legend-dot.medium{background:rgba(111,168,200,.6)}
  .legend-dot.strong{background:rgba(176,143,214,.7)}

  /* ── table container ─────────────────────────────── */
  .tablewrap-craft{overflow-x:auto;border:1px solid var(--line-soft);border-radius:6px;background:linear-gradient(180deg,#1a1710,#14120a)}
  .tablewrap-craft table{min-width:1060px;width:100%;border-collapse:collapse}
  .tablewrap-craft thead th{
    position:sticky;top:0;z-index:10;
    background:#1a1710;border-bottom:1px solid var(--line);
    font-family:"Cinzel",serif;font-size:11px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;
    color:var(--parch-faint);padding:11px 13px;white-space:nowrap;
  }
  .tablewrap-craft thead th a{color:inherit;text-decoration:none;display:flex;align-items:center;gap:5px;transition:.15s}
  .tablewrap-craft thead th a:hover{color:var(--gold-bright)}
  .tablewrap-craft thead th.sorted a{color:var(--gold-bright)}
  .sort-arrow{font-size:10px;opacity:.7}
  .tablewrap-craft tbody tr{border-bottom:1px solid var(--line-soft);transition:.15s}
  .tablewrap-craft tbody tr:last-child{border-bottom:0}
  .tablewrap-craft tbody tr:hover{background:rgba(200,148,42,.04)}
  .tablewrap-craft tbody tr[data-href]{cursor:pointer}
  .tablewrap-craft tbody td{padding:10px 13px;font-size:13px;color:var(--parch-dim);vertical-align:middle;white-space:nowrap}

  /* ── profit row highlights ───────────────────────── */
  .tablewrap-craft tbody tr.profit-strong{background:var(--profit-strong)}
  .tablewrap-craft tbody tr.profit-medium{background:var(--profit-medium)}
  .tablewrap-craft tbody tr.profit-light{background:var(--profit-light)}
  .tablewrap-craft tbody tr.profit-strong:hover,
  .tablewrap-craft tbody tr.profit-medium:hover,
  .tablewrap-craft tbody tr.profit-light:hover{filter:brightness(1.15)}

  /* ── cell types ──────────────────────────────────── */
  .cell-item{font-weight:600;color:var(--parch)}
  .cell-item a{color:inherit;text-decoration:none}
  .cell-item a:hover{color:var(--gold-bright);text-decoration:underline}
  .city-val{display:flex;flex-direction:column;align-items:flex-end;gap:2px}
  .city-val .city-name{font-family:"JetBrains Mono",monospace;font-size:10px;letter-spacing:.04em;color:var(--parch-faint);text-transform:uppercase}
  .silver{font-family:"JetBrains Mono",monospace;color:var(--parch)}
  .silver .unit{font-size:10px;letter-spacing:.1em;color:var(--parch-faint);margin-left:2px;text-transform:uppercase}
  .silver.zero{color:var(--parch-faint);font-style:italic}
  .profit-pos{font-family:"JetBrains Mono",monospace;color:#6db389}
  .profit-neg{font-family:"JetBrains Mono",monospace;color:#c87070}
  .profit-zero{font-family:"JetBrains Mono",monospace;color:var(--parch-faint)}
  .pct-badge{display:inline-flex;align-items:center;padding:2px 7px;border-radius:3px;font-family:"JetBrains Mono",monospace;font-size:12px;font-weight:700}
  .pct-badge.strong{background:rgba(176,143,214,.22);color:#c8a8e8}
  .pct-badge.medium{background:rgba(111,168,200,.18);color:#8ec4dc}
  .pct-badge.light{background:rgba(232,184,75,.14);color:var(--gold-bright)}
  .pct-badge.neutral{background:rgba(255,255,255,.06);color:var(--parch-faint)}
  .pct-badge.neg{background:rgba(200,112,112,.12);color:#c87070}
  .r{text-align:right}
  .c{text-align:center}

  /* ── empty state ─────────────────────────────────── */
  .empty-state{text-align:center;padding:70px 20px;color:var(--parch-faint);font-style:italic;border-top:1px solid var(--line-soft)}

  /* ── pagination ──────────────────────────────────── */
  .pagination-wrap{display:flex;justify-content:center;align-items:center;gap:6px;padding:24px 0 60px;flex-wrap:wrap}
  .pg-link{font-family:"Cinzel",serif;font-size:12px;font-weight:600;letter-spacing:.06em;padding:8px 13px;border:1px solid var(--line-soft);background:transparent;color:var(--parch-dim);border-radius:3px;text-decoration:none;transition:.18s;cursor:pointer;display:inline-flex;align-items:center}
  .pg-link:hover{border-color:var(--gold);color:var(--gold-bright)}
  .pg-link.active{background:var(--gold-bright);border-color:var(--gold-bright);color:#241b06}
  .pg-link.disabled{opacity:.35;pointer-events:none}
  .pg-dots{color:var(--parch-faint);padding:8px 4px;font-size:13px}

  @media(max-width:900px){
    .filters-toggle{display:flex}
    .filters-body{display:none}.filters-body.open{display:block}
    .filters-grid{grid-template-columns:repeat(2,1fr)}
  }
  @media(max-width:560px){
    .filters-grid{grid-template-columns:1fr}
    .page-head-inner{padding:32px 16px 28px}
  }
</style>
@endpush

@section('content')

{{-- ── PAGE HEADER ──────────────────────────────────────── --}}
<div class="page-head">
  <div class="page-head-inner">
    <div class="crumb">
      <a href="{{ url('/') }}" data-i18n="items.crumb.home">Início</a>
      / <span data-i18n="items.crumb.economy">Economia</span>
      / <span style="color:var(--parch-dim)" data-i18n="crafting.crumb">Craft</span>
    </div>
    <span class="eyebrow solo" data-i18n="crafting.eyebrow">Calculadora de Craft</span>
    <h1 data-i18n="crafting.title">Oportunidades de Craft</h1>
    <p class="lead" data-i18n="crafting.lead">Compare o custo dos ingredientes com o maior preço de venda e identifique os itens mais rentáveis para craftar.</p>
  </div>
</div>

{{-- ── FILTERS ───────────────────────────────────────────── --}}
<div class="filters-panel">
  <div class="wrap">
    <button class="filters-toggle" id="filtersToggle" type="button">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M6 12h12M9 18h6"/></svg>
      <span data-i18n="crafting.filter.title">Filtros</span>
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-left:auto"><path d="M6 9l6 6 6-6"/></svg>
    </button>

    <form method="GET" action="{{ route('crafting.index') }}" id="filterForm">
      @if(request('sort') && request('sort') !== 'lucro_ordem')
        <input type="hidden" name="sort" value="{{ request('sort') }}">
      @endif
      @if(request('dir') && request('dir') !== 'desc')
        <input type="hidden" name="dir" value="{{ request('dir') }}">
      @endif
      @if(request('per_page') && request('per_page') != 50)
        <input type="hidden" name="per_page" value="{{ request('per_page') }}">
      @endif

      <div class="filters-body" id="filtersBody">
        <div class="filters-grid" style="padding-top:16px">

          {{-- Busca --}}
          <div class="filter-group" style="grid-column:span 2">
            <label data-i18n="crafting.filter.busca">Nome do item</label>
            <input type="text" name="busca" class="filter-input"
                   value="{{ $busca }}"
                   data-i18n-placeholder="crafting.filter.busca.placeholder"
                   placeholder="Buscar item…">
          </div>

          {{-- Categoria --}}
          <div class="filter-group">
            <label data-i18n="crafting.filter.category">Categoria</label>
            <select name="categoria" class="filter-select">
              <option value="" data-i18n="items.filter.all">Todos</option>
              @foreach($categorias as $cat)
                <option value="{{ $cat->id }}"
                        {{ (string)$categoriaId === (string)$cat->id ? 'selected' : '' }}
                        data-name-pt="{{ $cat->portugues }}"
                        data-name-en="{{ $cat->ingles }}"
                        data-name-es="{{ $cat->espanhol }}"
                        data-name-fr="{{ $cat->frances }}">
                  {{ $cat->portugues ?? $cat->ingles }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- Cidade de Compra --}}
          <div class="filter-group">
            <label data-i18n="crafting.filter.city_custo">Cidade de Compra</label>
            <select name="cidade_custo" class="filter-select">
              <option value="" data-i18n="items.filter.all">Todas</option>
              @foreach($cidades as $cidade)
                <option value="{{ $cidade->id }}"
                        {{ (string)$cidadeCustoId === (string)$cidade->id ? 'selected' : '' }}
                        data-name-pt="{{ $cidade->portugues }}"
                        data-name-en="{{ $cidade->ingles }}"
                        data-name-es="{{ $cidade->espanhol }}"
                        data-name-fr="{{ $cidade->frances }}">
                  {{ $cidade->portugues ?? $cidade->ingles ?? $cidade->nome }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- Cidade de Venda --}}
          <div class="filter-group">
            <label data-i18n="crafting.filter.city_venda">Cidade de Venda</label>
            <select name="cidade_venda" class="filter-select">
              <option value="" data-i18n="items.filter.all">Todas</option>
              @foreach($cidades as $cidade)
                <option value="{{ $cidade->id }}"
                        {{ (string)$cidadeVendaId === (string)$cidade->id ? 'selected' : '' }}
                        data-name-pt="{{ $cidade->portugues }}"
                        data-name-en="{{ $cidade->ingles }}"
                        data-name-es="{{ $cidade->espanhol }}"
                        data-name-fr="{{ $cidade->frances }}">
                  {{ $cidade->portugues ?? $cidade->ingles ?? $cidade->nome }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- Lucro mínimo Ordem --}}
          <div class="filter-group">
            <label data-i18n="crafting.filter.lucro_min_ordem">Lucro mínimo (Ordem)</label>
            <input type="number" name="lucro_min_ordem" class="filter-input"
                   value="{{ $lucroMinOrdem }}" min="0" placeholder="0">
          </div>

          {{-- Lucro mínimo Direto --}}
          <div class="filter-group">
            <label data-i18n="crafting.filter.lucro_min_direto">Lucro mínimo (Direto)</label>
            <input type="number" name="lucro_min_direto" class="filter-input"
                   value="{{ $lucroMinDireto }}" min="0" placeholder="0">
          </div>

          {{-- % mínimo --}}
          <div class="filter-group">
            <label data-i18n="crafting.filter.pct_min">% Mínimo de lucro</label>
            <input type="number" name="pct_min_lucro" class="filter-input"
                   value="{{ $pctMinLucro }}" min="0" step="0.01" placeholder="0">
          </div>

          {{-- Actions + toggle --}}
          <div class="filter-group filters-actions" style="grid-column:span 4">
            <label class="toggle-irreal {{ $removerIrreais ? 'active' : '' }}" id="toggleIrreal">
              <input type="checkbox" name="remover_irreais" value="1"
                     {{ $removerIrreais ? 'checked' : '' }}
                     id="checkIrreal"
                     style="display:none"
                     onchange="this.closest('form').submit()">
              <span class="toggle-dot"></span>
              <span data-i18n="crafting.filter.remover_irreais">Remover valores irreais</span>
            </label>
            <div style="margin-left:auto;display:flex;gap:10px">
              <button type="submit" class="btn btn-gold" data-i18n="crafting.filter.apply">Aplicar</button>
              <a href="{{ route('crafting.index') }}" class="btn btn-ghost" data-i18n="crafting.filter.clear">Limpar</a>
            </div>
          </div>

        </div>
      </div>
    </form>
  </div>
</div>

{{-- ── RESULTS ───────────────────────────────────────────── --}}
<div class="wrap" style="padding-top:8px;padding-bottom:8px">

  <div class="results-bar">
    <div class="results-info">
      <b>{{ number_format($total, 0, ',', '.') }}</b>&nbsp;<span data-i18n="crafting.total_label">oportunidades encontradas</span>
      &nbsp;·&nbsp;
      <span data-i18n="crafting.page_info" data-page="{{ $page }}" data-pages="{{ $totalPages }}">
        Página {{ $page }} de {{ $totalPages }}
      </span>
    </div>

    <form method="GET" action="{{ route('crafting.index') }}" class="per-page-wrap" id="perPageForm">
      @foreach(request()->except(['per_page','page']) as $k => $v)
        @if($v !== null && $v !== '')
          <input type="hidden" name="{{ $k }}" value="{{ $v }}">
        @endif
      @endforeach
      <span class="per-page-label" data-i18n="crafting.per_page">Por página:</span>
      <select class="per-page-select" name="per_page" onchange="this.form.submit()">
        @foreach([25, 50, 100, 250] as $pp)
          <option value="{{ $pp }}" {{ $perPage == $pp ? 'selected' : '' }}>{{ $pp }}</option>
        @endforeach
      </select>
    </form>
  </div>

  {{-- legend --}}
  <div class="legend">
    <div class="legend-item">
      <span class="legend-dot light"></span>
      <span data-i18n="crafting.profit.light">&gt;20% lucro</span>
    </div>
    <div class="legend-item">
      <span class="legend-dot medium"></span>
      <span data-i18n="crafting.profit.medium">&gt;50% lucro</span>
    </div>
    <div class="legend-item">
      <span class="legend-dot strong"></span>
      <span data-i18n="crafting.profit.strong">&gt;100% lucro</span>
    </div>
  </div>

  {{-- ── TABLE ────────────────────────────────────────────── --}}
  @php
    function craftSortUrl(string $key, string $currentKey, string $currentDir, $request): string {
        $dir = ($key === $currentKey) ? ($currentDir === 'asc' ? 'desc' : 'asc') : 'desc';
        return $request->fullUrlWithQuery(['sort' => $key, 'dir' => $dir, 'page' => 1]);
    }

    function craftSortArrow(string $key, string $currentKey, string $currentDir): string {
        if ($key !== $currentKey) return '';
        return '<span class="sort-arrow">' . ($currentDir === 'asc' ? '↑' : '↓') . '</span>';
    }
  @endphp

  <div class="tablewrap-craft">
    <table>
      <thead>
        <tr>
          @php
            $cols = [
              ['key' => 'item_nome',        'i18n' => 'crafting.col.item',          'label' => 'Item'],
              ['key' => 'custo_ordem',       'i18n' => 'crafting.col.custo_ordem',   'label' => 'Custo (Ordem)',   'r' => true],
              ['key' => 'custo_direto',      'i18n' => 'crafting.col.custo_direto',  'label' => 'Custo (Direto)', 'r' => true],
              ['key' => 'maior_venda',       'i18n' => 'crafting.col.maior_venda',   'label' => 'Maior Venda',    'r' => true],
              ['key' => 'lucro_ordem',       'i18n' => 'crafting.col.lucro_ordem',   'label' => 'Lucro (Ordem)',  'r' => true],
              ['key' => 'pct_lucro_ordem',   'i18n' => 'crafting.col.pct_ordem',     'label' => '% Ordem',        'r' => true],
              ['key' => 'lucro_direto',      'i18n' => 'crafting.col.lucro_direto',  'label' => 'Lucro (Direto)', 'r' => true],
              ['key' => 'pct_lucro_direto',  'i18n' => 'crafting.col.pct_direto',    'label' => '% Direto',       'r' => true],
              ['key' => 'vendidos',          'i18n' => 'crafting.col.vendidos',       'label' => 'Vendidos',       'r' => true],
            ];
          @endphp
          @foreach($cols as $col)
            <th class="{{ $sortKey === $col['key'] ? 'sorted' : '' }} {{ !empty($col['r']) ? 'r' : '' }}">
              <a href="{{ craftSortUrl($col['key'], $sortKey, $sortDir, request()) }}">
                <span data-i18n="{{ $col['i18n'] }}">{{ $col['label'] }}</span>
                {!! craftSortArrow($col['key'], $sortKey, $sortDir) !!}
              </a>
            </th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @forelse($results as $row)
          @php
            $maxPct   = max((float)$row->pct_lucro_ordem, (float)$row->pct_lucro_direto);
            $rowClass = '';
            if ($maxPct >= 100)    $rowClass = 'profit-strong';
            elseif ($maxPct >= 50) $rowClass = 'profit-medium';
            elseif ($maxPct >= 20) $rowClass = 'profit-light';

            $ench    = (int) $row->encantamento;
            $enchSuf = $ench > 0 ? ' .'.$ench : '';
            $itemNome = ($row->item_portugues ?? $row->item_ingles) . $enchSuf;
          @endphp
          <tr class="{{ $rowClass }}" data-href="{{ route('itens.craft', $row->item_id) }}">

            {{-- Item --}}
            <td class="cell-item">
              <a href="{{ route('itens.craft', $row->item_id) }}"
                 data-name-pt="{{ $row->item_portugues }}{{ $enchSuf }}"
                 data-name-en="{{ $row->item_ingles }}{{ $enchSuf }}"
                 data-name-es="{{ $row->item_espanhol }}{{ $enchSuf }}"
                 data-name-fr="{{ $row->item_frances }}{{ $enchSuf }}">
                {{ $itemNome }}
              </a>
            </td>

            {{-- Custo Ordem --}}
            <td class="r">
              <div class="city-val">
                <span class="city-name"
                      data-city-pt="{{ $row->cidade_ordem_pt }}"
                      data-city-en="{{ $row->cidade_ordem_en }}"
                      data-city-es="{{ $row->cidade_ordem_es }}"
                      data-city-fr="{{ $row->cidade_ordem_fr }}">
                  {{ $row->cidade_ordem_pt }}
                </span>
                @if($row->custo_ordem > 0)
                  <span class="silver">{{ number_format($row->custo_ordem, 0, ',', '.') }}</span>
                @else
                  <span class="silver zero">—</span>
                @endif
              </div>
            </td>

            {{-- Custo Direto --}}
            <td class="r">
              <div class="city-val">
                <span class="city-name"
                      data-city-pt="{{ $row->cidade_direto_pt }}"
                      data-city-en="{{ $row->cidade_direto_en }}"
                      data-city-es="{{ $row->cidade_direto_es }}"
                      data-city-fr="{{ $row->cidade_direto_fr }}">
                  {{ $row->cidade_direto_pt }}
                </span>
                @if($row->custo_direto > 0)
                  <span class="silver">{{ number_format($row->custo_direto, 0, ',', '.') }}</span>
                @else
                  <span class="silver zero">—</span>
                @endif
              </div>
            </td>

            {{-- Maior Venda --}}
            <td class="r">
              <div class="city-val">
                <span class="city-name"
                      data-city-pt="{{ $row->cidade_venda_pt }}"
                      data-city-en="{{ $row->cidade_venda_en }}"
                      data-city-es="{{ $row->cidade_venda_es }}"
                      data-city-fr="{{ $row->cidade_venda_fr }}">
                  {{ $row->cidade_venda_pt }}
                </span>
                @if($row->maior_valor > 0)
                  <span class="silver">{{ number_format($row->maior_valor, 0, ',', '.') }}</span>
                @else
                  <span class="silver zero">—</span>
                @endif
              </div>
            </td>

            {{-- Lucro Ordem --}}
            @php $lo = (int)$row->lucro_ordem; @endphp
            <td class="r">
              @if($lo > 0)
                <span class="profit-pos">+{{ number_format($lo, 0, ',', '.') }}</span>
              @elseif($lo < 0)
                <span class="profit-neg">{{ number_format($lo, 0, ',', '.') }}</span>
              @else
                <span class="profit-zero">—</span>
              @endif
            </td>

            {{-- % Lucro Ordem --}}
            @php
              $plo = (float)$row->pct_lucro_ordem;
              $ploBadge = $plo >= 100 ? 'strong' : ($plo >= 50 ? 'medium' : ($plo >= 20 ? 'light' : ($plo > 0 ? 'neutral' : 'neg')));
            @endphp
            <td class="r">
              <span class="pct-badge {{ $ploBadge }}">
                {{ $plo > 0 ? '+' : '' }}{{ number_format($plo, 2, ',', '.') }}%
              </span>
            </td>

            {{-- Lucro Direto --}}
            @php $ld = (int)$row->lucro_direto; @endphp
            <td class="r">
              @if($ld > 0)
                <span class="profit-pos">+{{ number_format($ld, 0, ',', '.') }}</span>
              @elseif($ld < 0)
                <span class="profit-neg">{{ number_format($ld, 0, ',', '.') }}</span>
              @else
                <span class="profit-zero">—</span>
              @endif
            </td>

            {{-- % Lucro Direto --}}
            @php
              $pld = (float)$row->pct_lucro_direto;
              $pldBadge = $pld >= 100 ? 'strong' : ($pld >= 50 ? 'medium' : ($pld >= 20 ? 'light' : ($pld > 0 ? 'neutral' : 'neg')));
            @endphp
            <td class="r">
              <span class="pct-badge {{ $pldBadge }}">
                {{ $pld > 0 ? '+' : '' }}{{ number_format($pld, 2, ',', '.') }}%
              </span>
            </td>

            {{-- Vendidos --}}
            <td class="r">
              @if($row->vendidos > 0)
                <span class="silver">{{ number_format($row->vendidos, 0, ',', '.') }}</span>
              @else
                <span class="silver zero">—</span>
              @endif
            </td>

          </tr>
        @empty
          <tr>
            <td colspan="9" class="empty-state" data-i18n="crafting.empty">
              Nenhuma oportunidade de craft encontrada com esses filtros.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- ── PAGINATION ────────────────────────────────────────── --}}
  @if($totalPages > 1)
    @php
      $pageParams = request()->except(['page']);
      $pageRange  = range(max(1, $page - 2), min($totalPages, $page + 2));
    @endphp
    <div class="pagination-wrap">
      @if($page > 1)
        <a class="pg-link" href="{{ request()->fullUrlWithQuery(array_merge($pageParams, ['page' => 1])) }}">«</a>
        <a class="pg-link" href="{{ request()->fullUrlWithQuery(array_merge($pageParams, ['page' => $page - 1])) }}">‹</a>
      @else
        <span class="pg-link disabled">«</span>
        <span class="pg-link disabled">‹</span>
      @endif

      @if($pageRange[0] > 1)
        <span class="pg-dots">…</span>
      @endif

      @foreach($pageRange as $p)
        @if($p === $page)
          <span class="pg-link active">{{ $p }}</span>
        @else
          <a class="pg-link" href="{{ request()->fullUrlWithQuery(array_merge($pageParams, ['page' => $p])) }}">{{ $p }}</a>
        @endif
      @endforeach

      @if(end($pageRange) < $totalPages)
        <span class="pg-dots">…</span>
      @endif

      @if($page < $totalPages)
        <a class="pg-link" href="{{ request()->fullUrlWithQuery(array_merge($pageParams, ['page' => $page + 1])) }}">›</a>
        <a class="pg-link" href="{{ request()->fullUrlWithQuery(array_merge($pageParams, ['page' => $totalPages])) }}">»</a>
      @else
        <span class="pg-link disabled">›</span>
        <span class="pg-link disabled">»</span>
      @endif
    </div>
  @endif

</div>

@endsection

@push('scripts')
<script>
(function () {
  const LANG_COL = {
    'pt-BR':'pt','pt':'pt','en-US':'en','en':'en',
    'es-ES':'es','es':'es','fr-FR':'fr','fr':'fr','nl-NL':'en','nl':'en',
  };

  const toggle = document.getElementById('filtersToggle');
  const body   = document.getElementById('filtersBody');
  if (toggle && body) {
    toggle.addEventListener('click', () => {
      const open = body.classList.toggle('open');
      toggle.classList.toggle('open', open);
    });
  }

  const checkIrreal = document.getElementById('checkIrreal');
  const labelIrreal = document.getElementById('toggleIrreal');
  if (checkIrreal && labelIrreal) {
    checkIrreal.addEventListener('change', () => {
      labelIrreal.classList.toggle('active', checkIrreal.checked);
    });
  }

  function applyLocale(locale) {
    const col = LANG_COL[locale] || LANG_COL[locale.split('-')[0]] || 'pt';
    const K   = col.charAt(0).toUpperCase() + col.slice(1);

    document.querySelectorAll('[data-name-pt]').forEach(el => {
      el.textContent = el.dataset['name' + K] || el.dataset.nameEn || el.textContent;
    });
    document.querySelectorAll('[data-city-pt]').forEach(el => {
      el.textContent = el.dataset['city' + K] || el.dataset.cityEn || el.textContent;
    });

    ['[name="categoria"] option', '[name="cidade_custo"] option', '[name="cidade_venda"] option']
    .forEach(sel => {
      document.querySelectorAll(sel).forEach(opt => {
        if (!opt.value) return;
        const name = opt.dataset['name' + K] || opt.dataset.nameEn;
        if (name) opt.textContent = name;
      });
    });
  }

  document.addEventListener('i18n:ready', e => applyLocale(e.detail.locale));

  document.querySelectorAll('.tablewrap-craft tbody tr[data-href]').forEach(tr => {
    tr.addEventListener('click', e => {
      if (e.target.closest('a')) return;
      window.location.href = tr.dataset.href;
    });
  });
})();
</script>
@endpush

@extends('layouts.app')

@section('title', 'Preços por Cidade — AlbionMillionaire')

@push('styles')
<style>
  /* ── quality palette ─────────────────────────────── */
  :root{
    --q1:#8a7f60;--q2:oklch(0.72 0.13 150);--q3:#6fa8c8;--q4:#b08fd6;--q5:#E8B84B;
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
  .filters-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px 16px}
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

  /* ── toolbar ─────────────────────────────────────── */
  .results-bar{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;padding:16px 0 10px}
  .results-info{font-family:"JetBrains Mono",monospace;font-size:13px;color:var(--parch-faint)}
  .results-info b{color:var(--gold-bright)}
  .per-page-wrap{display:flex;align-items:center;gap:8px}
  .per-page-label{font-family:"JetBrains Mono",monospace;font-size:12px;color:var(--parch-faint);white-space:nowrap}
  .per-page-select{height:30px;appearance:none;-webkit-appearance:none;background:rgba(0,0,0,.38) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%23C8942A'/%3E%3C/svg%3E") no-repeat right 8px center;border:1px solid var(--line-soft);border-radius:3px;color:var(--parch);font-family:"JetBrains Mono",monospace;font-size:12px;padding:0 24px 0 10px;outline:none;cursor:pointer}

  /* ── table container ─────────────────────────────── */
  .tablewrap-transport{overflow-x:auto;border:1px solid var(--line-soft);border-radius:6px;background:linear-gradient(180deg,#1a1710,#14120a)}
  .tablewrap-transport table{min-width:860px;width:100%;border-collapse:collapse}
  .tablewrap-transport thead th{
    position:sticky;top:0;z-index:10;
    background:#1a1710;border-bottom:1px solid var(--line);
    font-family:"Cinzel",serif;font-size:11px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;
    color:var(--parch-faint);padding:11px 13px;white-space:nowrap;
  }
  .tablewrap-transport thead th a{color:inherit;text-decoration:none;display:flex;align-items:center;gap:5px;transition:.15s}
  .tablewrap-transport thead th a:hover{color:var(--gold-bright)}
  .tablewrap-transport thead th.sorted a{color:var(--gold-bright)}
  .sort-arrow{font-size:10px;opacity:.7}
  .tablewrap-transport tbody tr{border-bottom:1px solid var(--line-soft);transition:.15s}
  .tablewrap-transport tbody tr:last-child{border-bottom:0}
  .tablewrap-transport tbody tr:hover{background:rgba(200,148,42,.04)}
  .tablewrap-transport tbody td{padding:10px 13px;font-size:13px;color:var(--parch-dim);vertical-align:middle;white-space:nowrap}
  .tablewrap-transport tbody td.cell-item{white-space:normal}

  /* ── cell types ──────────────────────────────────── */
  .cell-item{font-weight:600;color:var(--parch)}
  .cell-item a{color:inherit;text-decoration:none}
  .cell-item a:hover{color:var(--gold-bright);text-decoration:underline}
  .quality-cell{display:flex;align-items:center;gap:8px}
  .quality-gem{width:8px;height:8px;transform:rotate(45deg);border-radius:1px;flex:0 0 auto}
  .qgem-1{background:var(--q1)}.qgem-2{background:var(--q2)}.qgem-3{background:var(--q3)}
  .qgem-4{background:var(--q4)}.qgem-5{background:var(--q5)}
  .silver{font-family:"JetBrains Mono",monospace;color:var(--parch)}
  .silver.zero{color:var(--parch-faint);font-style:italic}
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

  /* ── mobile card view ────────────────────────────── */
  .mobile-sort{display:none}
  .preco-cards{display:none}
  @media(max-width:768px){
    .tablewrap-transport{display:none}
    .mobile-sort{display:flex;gap:8px;overflow-x:auto;padding:4px 0 12px;scrollbar-width:none;-ms-overflow-style:none}
    .mobile-sort::-webkit-scrollbar{display:none}
    .msort-btn{flex:0 0 auto;display:inline-flex;align-items:center;gap:5px;padding:7px 13px;border:1px solid var(--line-soft);border-radius:20px;font-family:"JetBrains Mono",monospace;font-size:11px;letter-spacing:.04em;color:var(--parch-faint);background:rgba(0,0,0,.25);text-decoration:none;white-space:nowrap;transition:.18s;touch-action:manipulation;cursor:pointer;-webkit-tap-highlight-color:rgba(232,184,75,.15)}
    .msort-btn:hover{border-color:var(--gold);color:var(--gold-bright)}
    .msort-btn.active{background:rgba(232,184,75,.12);border-color:var(--gold);color:var(--gold-bright);font-weight:700}
    .msort-arrow{font-size:11px}
    .preco-cards{display:flex;flex-direction:column;gap:10px}
    .pcard{border:1px solid var(--line-soft);border-radius:6px;background:linear-gradient(180deg,#1a1710,#14120a);overflow:hidden}
    .pcard-head{display:flex;align-items:flex-start;justify-content:space-between;padding:12px 14px 10px;border-bottom:1px solid var(--line-soft);gap:10px}
    .pcard-title{font-weight:600;color:var(--parch);font-size:14px;flex:1;min-width:0;line-height:1.3}
    .pcard-title a{color:inherit;text-decoration:none}
    .pcard-title a:hover{color:var(--gold-bright)}
    .pcard-quality{display:flex;align-items:center;gap:6px;font-size:12px;color:var(--parch-dim);white-space:nowrap;padding-top:2px}
    .pcard-city{font-family:"JetBrains Mono",monospace;font-size:11px;letter-spacing:.04em;color:var(--parch-faint);text-transform:uppercase;padding:8px 14px;border-bottom:1px solid var(--line-soft)}
    .pcard-grid{display:grid;grid-template-columns:1fr 1fr}
    .pcard-cell{display:flex;flex-direction:column;gap:3px;padding:10px 14px;border-bottom:1px solid var(--line-soft)}
    .pcard-cell:nth-child(odd){border-right:1px solid var(--line-soft)}
    .pcard-cell:last-child,.pcard-cell:nth-last-child(2):nth-child(odd){border-bottom:0}
    .pcard-label{font-family:"JetBrains Mono",monospace;font-size:10px;letter-spacing:.06em;text-transform:uppercase;color:var(--parch-faint)}
    .pcard-val{font-family:"JetBrains Mono",monospace;font-size:14px;color:var(--parch)}
    .pcard-val.zero{color:var(--parch-faint);font-style:italic;font-size:13px}
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
      / <span style="color:var(--parch-dim)" data-i18n="precos.crumb">Preços por Cidade</span>
    </div>
    <span class="eyebrow solo" data-i18n="precos.eyebrow">Mercado</span>
    <h1 data-i18n="precos.title">Preços por Cidade</h1>
    <p class="lead" data-i18n="precos.lead">Consulte a ordem de compra, o valor de venda e a quantidade vendida de cada item em cada cidade.</p>
  </div>
</div>

{{-- ── FILTERS ───────────────────────────────────────────── --}}
<div class="filters-panel">
  <div class="wrap">
    <button class="filters-toggle" id="filtersToggle" type="button">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M6 12h12M9 18h6"/></svg>
      <span data-i18n="precos.filter.title">Filtros</span>
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-left:auto"><path d="M6 9l6 6 6-6"/></svg>
    </button>

    <form method="GET" action="{{ route('precos.index') }}" id="filterForm">
      {{-- preserve sort/per_page when re-filtering --}}
      @if(request('sort') && request('sort') !== 'item_nome')
        <input type="hidden" name="sort" value="{{ request('sort') }}">
      @endif
      @if(request('dir') && request('dir') !== 'asc')
        <input type="hidden" name="dir" value="{{ request('dir') }}">
      @endif
      @if(request('per_page') && request('per_page') != 50)
        <input type="hidden" name="per_page" value="{{ request('per_page') }}">
      @endif

      <div class="filters-body" id="filtersBody">
        <div class="filters-grid" style="padding-top:16px">

          {{-- Busca --}}
          <div class="filter-group" style="grid-column:span 2">
            <label data-i18n="precos.filter.busca">Nome do item</label>
            <input type="text" name="busca" class="filter-input"
                   value="{{ $busca }}"
                   data-i18n-placeholder="precos.filter.busca.placeholder"
                   placeholder="Buscar item…">
          </div>

          {{-- Categoria --}}
          <div class="filter-group">
            <label data-i18n="precos.filter.category">Categoria</label>
            <x-category-filter :tree="$categoriasTree" :selected-id="$categoriaId" />
          </div>

          {{-- Qualidade --}}
          <div class="filter-group">
            <label data-i18n="precos.filter.quality">Qualidade</label>
            <select name="qualidade" class="filter-select">
              <option value="" data-i18n="items.filter.all">Todas</option>
              @foreach($qualidades as $qual)
                <option value="{{ $qual->id }}"
                        {{ (string)$qualidadeId === (string)$qual->id ? 'selected' : '' }}
                        data-name-pt="{{ $qual->portugues }}"
                        data-name-en="{{ $qual->ingles }}"
                        data-name-es="{{ $qual->espanhol }}"
                        data-name-fr="{{ $qual->frances }}">
                  {{ $qual->portugues ?? $qual->ingles }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- Grau (Nível) --}}
          <div class="filter-group">
            <label data-i18n="precos.filter.nivel">Grau</label>
            <select name="nivel" class="filter-select">
              <option value="" data-i18n="items.filter.all">Todos</option>
              @foreach(range(1, 8) as $t)
                <option value="{{ $t }}" {{ (string)$nivel === (string)$t ? 'selected' : '' }}>T{{ $t }}</option>
              @endforeach
            </select>
          </div>

          {{-- Encantamento --}}
          <div class="filter-group">
            <label data-i18n="precos.filter.encantamento">Encantamento</label>
            <select name="encantamento" class="filter-select">
              <option value="" data-i18n="items.filter.all">Todos</option>
              @foreach([0,1,2,3,4] as $e)
                <option value="{{ $e }}" {{ (string)$encantamento === (string)$e ? 'selected' : '' }}>
                  .{{ $e }}{{ $e === 0 ? ' (base)' : '' }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- Cidade --}}
          <div class="filter-group">
            <label data-i18n="precos.filter.city">Cidade</label>
            <select name="cidade" class="filter-select">
              <option value="" data-i18n="items.filter.all">Todas</option>
              @foreach($cidades as $cidade)
                <option value="{{ $cidade->id }}"
                        {{ (string)$cidadeId === (string)$cidade->id ? 'selected' : '' }}
                        data-name-pt="{{ $cidade->portugues }}"
                        data-name-en="{{ $cidade->ingles }}"
                        data-name-es="{{ $cidade->espanhol }}"
                        data-name-fr="{{ $cidade->frances }}">
                  {{ $cidade->portugues ?? $cidade->ingles ?? $cidade->nome }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- Valor mínimo --}}
          <div class="filter-group">
            <label data-i18n="precos.filter.valor_min">Valor mínimo</label>
            <input type="number" name="valor_min" class="filter-input"
                   value="{{ $valorMin }}" min="0" placeholder="0">
          </div>

          {{-- Qtd mínima vendida --}}
          <div class="filter-group">
            <label data-i18n="precos.filter.qtd_min">Qtd. mínima vendida</label>
            <input type="number" name="qtd_min_vendidos" class="filter-input"
                   value="{{ $qtdMinVendidos }}" min="0" placeholder="0">
          </div>

          {{-- Actions --}}
          <div class="filter-group filters-actions" style="grid-column:span 2">
            <div style="margin-left:auto;display:flex;gap:10px">
              <button type="submit" class="btn btn-gold" data-i18n="precos.filter.apply">Aplicar</button>
              <a href="{{ route('precos.index') }}" class="btn btn-ghost" data-i18n="precos.filter.clear">Limpar</a>
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
      <b>{{ number_format($total, 0, ',', '.') }}</b>&nbsp;<span data-i18n="precos.total_label">preços encontrados</span>
      &nbsp;·&nbsp;
      <span data-i18n="precos.page_info" data-i18n-vars='{"page":{{ $page }},"total":{{ $totalPages }}}'>
        Página {{ $page }} de {{ $totalPages }}
      </span>
    </div>

    <form method="GET" action="{{ route('precos.index') }}" class="per-page-wrap" id="perPageForm">
      {{-- preserve all current filters --}}
      @foreach(request()->except(['per_page','page']) as $k => $v)
        @if($v !== null && $v !== '')
          <input type="hidden" name="{{ $k }}" value="{{ $v }}">
        @endif
      @endforeach
      <span class="per-page-label" data-i18n="precos.per_page">Por página:</span>
      <select class="per-page-select" name="per_page" onchange="this.form.submit()">
        @foreach([25, 50, 100, 250] as $pp)
          <option value="{{ $pp }}" {{ $perPage == $pp ? 'selected' : '' }}>{{ $pp }}</option>
        @endforeach
      </select>
    </form>
  </div>

  {{-- ── TABLE ────────────────────────────────────────────── --}}
  @php
    function precosSortUrl(string $key, string $currentKey, string $currentDir, $request): string {
        $dir = ($key === $currentKey) ? ($currentDir === 'asc' ? 'desc' : 'asc') : 'asc';
        return $request->fullUrlWithQuery(['sort' => $key, 'dir' => $dir, 'page' => 1]);
    }

    function precosSortArrow(string $key, string $currentKey, string $currentDir): string {
        if ($key !== $currentKey) return '';
        return '<span class="sort-arrow">' . ($currentDir === 'asc' ? '↑' : '↓') . '</span>';
    }
  @endphp

  <div class="tablewrap-transport">
    <table>
      <thead>
        <tr>
          @php
            $cols = [
              ['key' => 'item_nome',                 'i18n' => 'precos.col.item',   'label' => 'Item'],
              ['key' => 'cidade_nome',                'i18n' => 'precos.col.city',   'label' => 'Cidade'],
              ['key' => 'ordem_de_compra',             'i18n' => 'precos.col.ordem',  'label' => 'Ordem de Compra', 'r' => true],
              ['key' => 'valor',                       'i18n' => 'precos.col.valor',  'label' => 'Valor',           'r' => true],
              ['key' => 'preco_medio',                 'i18n' => 'precos.col.medio',  'label' => 'Valor Médio',     'r' => true],
              ['key' => 'quantidade_itens_vendidos',   'i18n' => 'precos.col.qtd',    'label' => 'Quantidade',      'r' => true],
            ];
          @endphp
          @foreach($cols as $col)
            @php $isSort = $sortKey === $col['key']; @endphp
            <th class="{{ $isSort ? 'sorted' : '' }} {{ !empty($col['r']) ? 'r' : '' }}">
              <a href="{{ precosSortUrl($col['key'], $sortKey, $sortDir, request()) }}">
                <span data-i18n="{{ $col['i18n'] }}">{{ $col['label'] }}</span>
                {!! precosSortArrow($col['key'], $sortKey, $sortDir) !!}
              </a>
            </th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @forelse($results as $row)
          @php
            $ench     = (int) $row->encantamento;
            $nivel    = $row->nivel ?? null;
            $enchSuf  = $nivel !== null
                ? ' '.$nivel.($ench > 0 ? '.'.$ench : '')
                : ($ench > 0 ? ' .'.$ench : '');
            $itemNome = ($row->item_portugues ?? $row->item_ingles) . $enchSuf;
          @endphp
          <tr>
            {{-- Item + Qualidade --}}
            <td class="cell-item">
              <a href="{{ route('itens.mercado', $row->item_id) }}"
                 data-name-pt="{{ $row->item_portugues }}{{ $enchSuf }}"
                 data-name-en="{{ $row->item_ingles }}{{ $enchSuf }}"
                 data-name-es="{{ $row->item_espanhol }}{{ $enchSuf }}"
                 data-name-fr="{{ $row->item_frances }}{{ $enchSuf }}">
                {{ $itemNome }}
              </a>
              <div class="quality-cell" style="margin-top:3px;opacity:.75">
                <span class="quality-gem qgem-{{ $row->qualidade_id }}"></span>
                <span style="font-size:11px;font-family:'JetBrains Mono',monospace;letter-spacing:.04em"
                      data-qual-pt="{{ $row->qualidade_portugues }}"
                      data-qual-en="{{ $row->qualidade_ingles }}"
                      data-qual-es="{{ $row->qualidade_espanhol }}"
                      data-qual-fr="{{ $row->qualidade_frances }}">
                  {{ $row->qualidade_portugues ?? $row->qualidade_ingles }}
                </span>
              </div>
            </td>

            {{-- Cidade --}}
            <td>
              <span data-city-pt="{{ $row->cidade_pt }}"
                    data-city-en="{{ $row->cidade_en }}"
                    data-city-es="{{ $row->cidade_es }}"
                    data-city-fr="{{ $row->cidade_fr }}">
                {{ $row->cidade_pt }}
              </span>
            </td>

            {{-- Ordem de Compra --}}
            <td class="r">
              @if($row->ordem_de_compra > 0)
                <span class="silver">{{ number_format($row->ordem_de_compra, 0, ',', '.') }}</span>
              @else
                <span class="silver zero">—</span>
              @endif
            </td>

            {{-- Valor --}}
            <td class="r">
              @if($row->valor > 0)
                <span class="silver">{{ number_format($row->valor, 0, ',', '.') }}</span>
              @else
                <span class="silver zero">—</span>
              @endif
            </td>

            {{-- Valor Médio --}}
            <td class="r">
              @if($row->preco_medio > 0)
                <span class="silver">{{ number_format($row->preco_medio, 0, ',', '.') }}</span>
              @else
                <span class="silver zero">—</span>
              @endif
            </td>

            {{-- Quantidade --}}
            <td class="r">
              @if($row->quantidade_itens_vendidos > 0)
                <span class="silver">{{ number_format($row->quantidade_itens_vendidos, 0, ',', '.') }}</span>
              @else
                <span class="silver zero">—</span>
              @endif
            </td>

          </tr>
        @empty
          <tr>
            <td colspan="6" class="empty-state" data-i18n="precos.empty">
              Nenhum preço encontrado com esses filtros.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- ── MOBILE SORT BAR ──────────────────────────────────── --}}
  <div class="mobile-sort">
    @foreach($cols as $col)
      <a href="{{ precosSortUrl($col['key'], $sortKey, $sortDir, request()) }}"
         class="msort-btn {{ $sortKey === $col['key'] ? 'active' : '' }}">
        <span data-i18n="{{ $col['i18n'] }}">{{ $col['label'] }}</span>
        @if($sortKey === $col['key'])
          <span class="msort-arrow">{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
        @endif
      </a>
    @endforeach
  </div>

  {{-- ── MOBILE CARDS ──────────────────────────────────────── --}}
  <div class="preco-cards">
    @forelse($results as $row)
      @php
        $ench2    = (int) $row->encantamento;
        $nivel2   = $row->nivel ?? null;
        $enchSuf2 = $nivel2 !== null
            ? ' '.$nivel2.($ench2 > 0 ? '.'.$ench2 : '')
            : ($ench2 > 0 ? ' .'.$ench2 : '');
        $itemNome2 = ($row->item_portugues ?? $row->item_ingles) . $enchSuf2;
      @endphp
      <div class="pcard">

        {{-- Cabeçalho: item + qualidade --}}
        <div class="pcard-head">
          <div class="pcard-title">
            <a href="{{ route('itens.mercado', $row->item_id) }}"
               data-name-pt="{{ $row->item_portugues }}{{ $enchSuf2 }}"
               data-name-en="{{ $row->item_ingles }}{{ $enchSuf2 }}"
               data-name-es="{{ $row->item_espanhol }}{{ $enchSuf2 }}"
               data-name-fr="{{ $row->item_frances }}{{ $enchSuf2 }}">
              {{ $itemNome2 }}
            </a>
          </div>
          <div class="pcard-quality">
            <span class="quality-gem qgem-{{ $row->qualidade_id }}"></span>
            <span data-qual-pt="{{ $row->qualidade_portugues }}"
                  data-qual-en="{{ $row->qualidade_ingles }}"
                  data-qual-es="{{ $row->qualidade_espanhol }}"
                  data-qual-fr="{{ $row->qualidade_frances }}">
              {{ $row->qualidade_portugues ?? $row->qualidade_ingles }}
            </span>
          </div>
        </div>

        <div class="pcard-city">
          <span data-city-pt="{{ $row->cidade_pt }}"
                data-city-en="{{ $row->cidade_en }}"
                data-city-es="{{ $row->cidade_es }}"
                data-city-fr="{{ $row->cidade_fr }}">
            {{ $row->cidade_pt }}
          </span>
        </div>

        {{-- Grid de preços --}}
        <div class="pcard-grid">

          <div class="pcard-cell">
            <span class="pcard-label" data-i18n="precos.col.ordem">Ordem de Compra</span>
            @if($row->ordem_de_compra > 0)
              <span class="pcard-val">{{ number_format($row->ordem_de_compra, 0, ',', '.') }}</span>
            @else
              <span class="pcard-val zero">—</span>
            @endif
          </div>

          <div class="pcard-cell">
            <span class="pcard-label" data-i18n="precos.col.valor">Valor</span>
            @if($row->valor > 0)
              <span class="pcard-val">{{ number_format($row->valor, 0, ',', '.') }}</span>
            @else
              <span class="pcard-val zero">—</span>
            @endif
          </div>

          <div class="pcard-cell">
            <span class="pcard-label" data-i18n="precos.col.medio">Valor Médio</span>
            @if($row->preco_medio > 0)
              <span class="pcard-val">{{ number_format($row->preco_medio, 0, ',', '.') }}</span>
            @else
              <span class="pcard-val zero">—</span>
            @endif
          </div>

          <div class="pcard-cell">
            <span class="pcard-label" data-i18n="precos.col.qtd">Quantidade</span>
            @if($row->quantidade_itens_vendidos > 0)
              <span class="pcard-val">{{ number_format($row->quantidade_itens_vendidos, 0, ',', '.') }}</span>
            @else
              <span class="pcard-val zero">—</span>
            @endif
          </div>

        </div>

      </div>
    @empty
      <div class="empty-state" data-i18n="precos.empty">
        Nenhum preço encontrado com esses filtros.
      </div>
    @endforelse
  </div>

  {{-- ── PAGINATION ────────────────────────────────────────── --}}
  @if($totalPages > 1)
    @php
      $pageParams = request()->except(['page']);
      $pageRange  = range(max(1, $page - 2), min($totalPages, $page + 2));
    @endphp
    <div class="pagination-wrap">
      {{-- First --}}
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

      {{-- Last --}}
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

  /* ── mobile filter toggle ─────────────────────────── */
  const toggle = document.getElementById('filtersToggle');
  const body   = document.getElementById('filtersBody');
  if (toggle && body) {
    toggle.addEventListener('click', () => {
      const open = body.classList.toggle('open');
      toggle.classList.toggle('open', open);
    });
  }

  /* ── i18n locale application ─────────────────────── */
  function applyLocale(locale) {
    const col = LANG_COL[locale] || LANG_COL[locale.split('-')[0]] || 'pt';
    const K   = col.charAt(0).toUpperCase() + col.slice(1);

    document.querySelectorAll('[data-name-pt]').forEach(el => {
      el.textContent = el.dataset['name' + K] || el.dataset.nameEn || el.textContent;
    });
    document.querySelectorAll('[data-city-pt]').forEach(el => {
      el.textContent = el.dataset['city' + K] || el.dataset.cityEn || el.textContent;
    });
    document.querySelectorAll('[data-qual-pt]').forEach(el => {
      el.textContent = el.dataset['qual' + K] || el.dataset.qualEn || el.textContent;
    });

    /* quality & city select options */
    ['[name="qualidade"] option', '[name="cidade"] option']
    .forEach(sel => {
      document.querySelectorAll(sel).forEach(opt => {
        if (!opt.value) return;
        const name = opt.dataset['name' + K] || opt.dataset.nameEn;
        if (name) opt.textContent = name;
      });
    });
  }

  document.addEventListener('i18n:ready', e => applyLocale(e.detail.locale));
})();
</script>
@endpush

@extends('admin.layout')

@section('title', 'Itens — Categorias')
@section('page-title')
<span data-i18n="admin.items.page_title">Itens / Categorias</span>
@endsection

@section('content')
<div class="breadcrumb">
  <span>Admin</span><span class="sep">/</span><span data-i18n="admin.items.page_title">Itens / Categorias</span>
</div>

{{-- Lote --}}
<div class="card" style="margin-bottom:18px">
  <div class="card-head">
    <h2 data-i18n="admin.items.batch.title">Atualização em Lote</h2>
    <span id="lote-count" style="font-family:'JetBrains Mono',monospace;font-size:12px;color:var(--parch-faint)"
          data-i18n="admin.items.batch.count">0 selecionados</span>
  </div>
  <div class="card-body" style="padding:16px">
    <form method="POST" action="{{ route('admin.itens.lote') }}" id="form-lote">
      @csrf
      <div id="lote-ids"></div>
      <div style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap">
        <div class="form-group" style="flex:1;min-width:220px">
          <label for="lote_categoria_id" data-i18n="admin.items.batch.cat_label">Nova Categoria</label>
          <select id="lote_categoria_id" name="categoria_id">
            <option value="" data-i18n="admin.items.cat.none">— Sem categoria —</option>
            @foreach($categorias as $cat)
              <option value="{{ $cat->id }}">
                {{ $cat->portugues ?: $cat->nome }}
                @if($cat->pai) / {{ $cat->pai->portugues ?: $cat->pai->nome }} @endif
              </option>
            @endforeach
          </select>
        </div>
        <button type="submit" class="btn btn-gold" id="btn-lote" disabled
                data-i18n="admin.items.batch.btn">
          Aplicar nos Selecionados
        </button>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-head">
    <h2>
      <span data-i18n="admin.items.page_title">Itens / Categorias</span>
      ({{ $itens->total() }})
    </h2>
    <form method="GET" action="{{ route('admin.itens.index') }}" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
      <div class="search-bar">
        <div class="field">
          <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" style="width:16px;height:16px;opacity:.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/></svg>
          <input type="text" name="busca" value="{{ $busca }}"
                 data-i18n-placeholder="admin.items.search_placeholder"
                 placeholder="Nome ou ID externo...">
        </div>
      </div>
      <select name="categoria_id" style="max-width:220px;background:rgba(0,0,0,.35);border:1px solid var(--line-soft);border-radius:3px;color:var(--parch);font-family:'Spectral',serif;font-size:14px;padding:9px 12px;outline:none">
        <option value="" data-i18n="admin.items.filter.all_cats">Todas as categorias</option>
        @foreach($categorias as $cat)
          <option value="{{ $cat->id }}" {{ $categoriaId == $cat->id ? 'selected' : '' }}>
            {{ $cat->portugues ?: $cat->nome }}
          </option>
        @endforeach
      </select>
      <button type="submit" class="btn btn-sm" data-i18n="admin.items.filter.btn">Filtrar</button>
      @if($busca || $categoriaId)
        <a href="{{ route('admin.itens.index') }}" class="btn btn-sm" data-i18n="admin.items.filter.clear">Limpar</a>
      @endif
    </form>
  </div>

  <div class="tablewrap">
    <table>
      <thead>
        <tr>
          <th style="width:36px">
            <input type="checkbox" id="check-all" style="accent-color:var(--gold-bright);cursor:pointer" />
          </th>
          <th data-i18n="admin.items.col.ext_id">ID Externo</th>
          <th data-i18n="admin.items.col.name_pt">Nome (PT)</th>
          <th data-i18n="admin.items.col.name_en">Nome (EN)</th>
          <th data-i18n="admin.items.col.current_cat">Categoria Atual</th>
          <th data-i18n="admin.items.col.change_cat">Alterar Categoria</th>
        </tr>
      </thead>
      <tbody>
        @forelse($itens as $item)
          <tr>
            <td>
              <input type="checkbox" class="item-check" value="{{ $item->id }}"
                     style="accent-color:var(--gold-bright);cursor:pointer" />
            </td>
            <td>
              <span class="mono" style="font-size:12px;color:var(--parch-faint)">{{ $item->id_externo }}</span>
            </td>
            <td>{{ $item->portugues ?? '—' }}</td>
            <td>{{ $item->ingles ?? '—' }}</td>
            <td>
              @if($item->categoria)
                <span class="badge badge-gold">{{ $item->categoria->portugues ?: $item->categoria->nome }}</span>
              @else
                <span style="color:var(--parch-faint);font-size:12px" data-i18n="admin.items.no_cat">sem categoria</span>
              @endif
            </td>
            <td>
              <form method="POST" action="{{ route('admin.itens.update', $item) }}"
                    style="display:flex;gap:6px;align-items:center">
                @csrf @method('PATCH')
                <select name="categoria_id"
                        style="background:rgba(0,0,0,.35);border:1px solid var(--line-soft);border-radius:3px;color:var(--parch);font-family:'Spectral',serif;font-size:13px;padding:6px 10px;outline:none;min-width:160px">
                  <option value="" data-i18n="admin.items.cat.none">— Sem categoria —</option>
                  @foreach($categorias as $cat)
                    <option value="{{ $cat->id }}" {{ $item->categoria_id == $cat->id ? 'selected' : '' }}>
                      {{ $cat->portugues ?: $cat->nome }}
                      @if($cat->pai) / {{ $cat->pai->portugues ?: $cat->pai->nome }} @endif
                    </option>
                  @endforeach
                </select>
                <button type="submit" class="btn btn-sm" data-i18n="admin.items.btn.save">Salvar</button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" style="text-align:center;color:var(--parch-faint);padding:40px"
                data-i18n="admin.items.empty">Nenhum item encontrado.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($itens->hasPages())
    <div style="padding:16px 22px;border-top:1px solid var(--line-soft)">
      <div class="pagination">
        @if($itens->onFirstPage())
          <span class="disabled" data-i18n="admin.prev">‹ Anterior</span>
        @else
          <a href="{{ $itens->previousPageUrl() }}" data-i18n="admin.prev">‹ Anterior</a>
        @endif

        @foreach($itens->getUrlRange(max(1, $itens->currentPage()-2), min($itens->lastPage(), $itens->currentPage()+2)) as $page => $url)
          @if($page == $itens->currentPage())
            <span class="active">{{ $page }}</span>
          @else
            <a href="{{ $url }}">{{ $page }}</a>
          @endif
        @endforeach

        @if($itens->hasMorePages())
          <a href="{{ $itens->nextPageUrl() }}" data-i18n="admin.next">Próxima ›</a>
        @else
          <span class="disabled" data-i18n="admin.next">Próxima ›</span>
        @endif
      </div>
    </div>
  @endif
</div>

@push('scripts')
<script>
  const checkAll  = document.getElementById('check-all');
  const checks    = () => [...document.querySelectorAll('.item-check')];
  const loteIds   = document.getElementById('lote-ids');
  const loteCount = document.getElementById('lote-count');
  const btnLote   = document.getElementById('btn-lote');

  function updateLote() {
    const sel = checks().filter(c => c.checked).map(c => c.value);
    const n = sel.length;
    loteCount.textContent = n + ' ' + (window.I18n ? window.I18n.t('admin.items.batch.count').replace(/^\d+\s*/, '') : 'selecionados');
    btnLote.disabled = n === 0;
    loteIds.innerHTML = sel.map(id => `<input type="hidden" name="ids[]" value="${id}">`).join('');
  }

  checkAll.addEventListener('change', () => {
    checks().forEach(c => c.checked = checkAll.checked);
    updateLote();
  });

  document.addEventListener('change', e => {
    if (e.target.classList.contains('item-check')) updateLote();
  });

  document.addEventListener('i18n:ready', updateLote);
</script>
@endpush
@endsection

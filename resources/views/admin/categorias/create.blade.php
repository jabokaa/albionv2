@extends('admin.layout')

@section('title', 'Nova Categoria')
@section('page-title')
<span data-i18n="admin.cat.create.title">Criar Categoria</span>
@endsection

@section('content')
<div class="breadcrumb">
  <a href="{{ route('admin.categorias.index') }}" data-i18n="admin.cat.page_title">Categorias</a>
  <span class="sep">/</span>
  <span data-i18n="admin.cat.create.crumb">Nova</span>
</div>

<form method="POST" action="{{ route('admin.categorias.store') }}" id="formCriarCategoria">
@csrf

{{-- Dados da categoria --}}
<div class="card" style="max-width:780px;margin-bottom:24px">
  <div class="card-head">
    <h2 data-i18n="admin.cat.create.title">Criar Categoria</h2>
  </div>
  <div class="card-body">
    <div class="form-grid">
      <div class="form-group full">
        <label for="nome" data-i18n="admin.cat.label.slug">Nome / Slug *</label>
        <input type="text" id="nome" name="nome" value="{{ old('nome') }}" placeholder="ex: weapons_sword" required />
        @error('nome')<span class="field-error">{{ $message }}</span>@enderror
        <span style="font-size:12px;color:var(--parch-faint);margin-top:2px" data-i18n="admin.cat.label.slug_hint">Identificador interno único (letras minúsculas, sem espaços)</span>
      </div>

      <div class="form-group full">
        <label for="categoria_pai_id" data-i18n="admin.cat.label.parent">Categoria Pai</label>
        <select id="categoria_pai_id" name="categoria_pai_id">
          <option value="" data-i18n="admin.cat.label.parent.none">— Nenhuma (categoria raiz) —</option>
          @foreach($pais as $pai)
            @php
              $sufPt = $pai->pai ? ' (' . ($pai->pai->portugues ?: $pai->pai->nome) . ')' : '';
              $sufEn = $pai->pai ? ' (' . ($pai->pai->ingles    ?: $pai->pai->nome) . ')' : '';
              $sufEs = $pai->pai ? ' (' . ($pai->pai->espanhol  ?: $pai->pai->nome) . ')' : '';
              $sufFr = $pai->pai ? ' (' . ($pai->pai->frances   ?: $pai->pai->nome) . ')' : '';
            @endphp
            <option value="{{ $pai->id }}"
              class="loc-opt"
              data-pt="{{ ($pai->portugues ?: $pai->nome) . $sufPt }}"
              data-en="{{ ($pai->ingles    ?: $pai->nome) . $sufEn }}"
              data-es="{{ ($pai->espanhol  ?: $pai->nome) . $sufEs }}"
              data-fr="{{ ($pai->frances   ?: $pai->nome) . $sufFr }}"
              {{ (old('categoria_pai_id', request('pai')) == $pai->id) ? 'selected' : '' }}>
              {{ $pai->portugues ?: $pai->nome }}{{ $sufPt }}
            </option>
          @endforeach
        </select>
        @error('categoria_pai_id')<span class="field-error">{{ $message }}</span>@enderror
      </div>

      <div class="form-group">
        <label for="portugues" data-i18n="admin.cat.label.pt">Português</label>
        <input type="text" id="portugues" name="portugues" value="{{ old('portugues') }}" />
        @error('portugues')<span class="field-error">{{ $message }}</span>@enderror
      </div>

      <div class="form-group">
        <label for="ingles" data-i18n="admin.cat.label.en">Inglês</label>
        <input type="text" id="ingles" name="ingles" value="{{ old('ingles') }}" />
        @error('ingles')<span class="field-error">{{ $message }}</span>@enderror
      </div>

      <div class="form-group">
        <label for="espanhol" data-i18n="admin.cat.label.es">Espanhol</label>
        <input type="text" id="espanhol" name="espanhol" value="{{ old('espanhol') }}" />
        @error('espanhol')<span class="field-error">{{ $message }}</span>@enderror
      </div>

      <div class="form-group">
        <label for="frances" data-i18n="admin.cat.label.fr">Francês</label>
        <input type="text" id="frances" name="frances" value="{{ old('frances') }}" />
        @error('frances')<span class="field-error">{{ $message }}</span>@enderror
      </div>
    </div>
  </div>
</div>

{{-- Picker de itens --}}
<div class="card" style="margin-bottom:24px">
  <div class="card-head">
    <h2>
      <span data-i18n="admin.cat.items.title">Vincular Itens</span>
      <span id="selectedCount" class="badge badge-gold" style="margin-left:10px;display:none">0 <span data-i18n="admin.cat.items.selected">selecionados</span></span>
    </h2>
    <div class="search-bar">
      <div class="field">
        <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" style="width:16px;height:16px;opacity:.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/></svg>
        <input type="text" id="itemBusca" data-i18n-placeholder="admin.cat.items.search_placeholder" placeholder="Buscar item por nome ou ID...">
      </div>
      <button type="button" id="btnDesmarcarTodos" class="btn btn-sm" style="display:none" data-i18n="admin.cat.items.deselect_all">Desmarcar todos</button>
    </div>
  </div>

  <div class="tablewrap">
    <table id="itemTable">
      <thead>
        <tr>
          <th style="width:36px"></th>
          <th style="width:48px"></th>
          <th data-i18n="admin.cat.items.col.id">ID Externo</th>
          <th data-i18n="admin.cat.items.col.name">Nome</th>
          <th data-i18n="admin.cat.items.col.current_cat">Categoria Atual</th>
        </tr>
      </thead>
      <tbody id="itemTableBody">
        <tr id="itemTableEmpty">
          <td colspan="5" style="text-align:center;color:var(--parch-faint);padding:40px" data-i18n="admin.cat.items.search_hint">
            Digite para buscar itens...
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <div id="itemTableLimit" style="display:none;padding:12px 22px;border-top:1px solid var(--line-soft);font-size:12px;color:var(--parch-faint)" data-i18n="admin.cat.items.limit_hint">
    Mostrando os primeiros 100 resultados. Refine a busca para ver mais.
  </div>
</div>

{{-- Botões de submit --}}
<div style="display:flex;gap:10px">
  <button type="submit" class="btn btn-gold" data-i18n="admin.cat.btn.create">Criar Categoria</button>
  <a href="{{ route('admin.categorias.index') }}" class="btn" data-i18n="admin.cat.btn.cancel">Cancelar</a>
</div>

</form>

@push('styles')
<style>
  #itemTable tbody tr { cursor: pointer; }
  #itemTable tbody tr:hover { background: rgba(200,148,42,.06); }
  #itemTable tbody tr.selected-row { background: rgba(200,148,42,.1); }
  #itemTable tbody tr.selected-row td { color: var(--parch); }
  .item-img { width:32px;height:32px;object-fit:contain;border-radius:3px;background:rgba(0,0,0,.3) }
  .item-img-placeholder { width:32px;height:32px;border-radius:3px;background:rgba(0,0,0,.3);display:inline-block }
  .item-check { accent-color: var(--gold); width:16px;height:16px;cursor:pointer }
</style>
@endpush

@push('scripts')
<script>
(function () {
  const BUSCA_URL  = '{{ route('admin.itens.busca') }}';
  const selected   = new Map(); // id → item object
  let   debounce   = null;

  /* ── helpers ── */
  function itemName(item) {
    const col = window.localeToCol ? localeToCol(window._locale || 'pt-BR') : 'pt';
    const map  = { pt: 'portugues', en: 'ingles', es: 'espanhol', fr: 'frances' };
    const field = map[col] || 'ingles';
    return item[field] || item.ingles || item.portugues || item.id_externo;
  }

  function catName(cat) {
    if (!cat) return '—';
    const col  = window.localeToCol ? localeToCol(window._locale || 'pt-BR') : 'pt';
    const map  = { pt: 'portugues', en: 'ingles', es: 'espanhol', fr: 'frances' };
    return cat[map[col] || 'ingles'] || cat.ingles || cat.portugues || cat.nome || '—';
  }

  function updateUI() {
    const count   = selected.size;
    const badge   = document.getElementById('selectedCount');
    const btnDesel = document.getElementById('btnDesmarcarTodos');
    const countEl = badge.firstChild;

    countEl.textContent = count + ' ';
    badge.style.display   = count ? 'inline-flex' : 'none';
    btnDesel.style.display = count ? '' : 'none';

    /* sync hidden inputs */
    document.querySelectorAll('.hidden-item-id').forEach(el => el.remove());
    const form = document.getElementById('formCriarCategoria');
    selected.forEach((_, id) => {
      const inp = document.createElement('input');
      inp.type  = 'hidden';
      inp.name  = 'item_ids[]';
      inp.value = id;
      inp.className = 'hidden-item-id';
      form.appendChild(inp);
    });

    /* update checkboxes in visible rows */
    document.querySelectorAll('#itemTableBody tr[data-id]').forEach(row => {
      const id  = Number(row.dataset.id);
      const cb  = row.querySelector('.item-check');
      const sel = selected.has(id);
      if (cb) cb.checked = sel;
      row.classList.toggle('selected-row', sel);
    });
  }

  function toggleItem(item) {
    if (selected.has(item.id)) {
      selected.delete(item.id);
    } else {
      selected.set(item.id, item);
    }
    updateUI();
  }

  function renderRows(itens) {
    const tbody = document.getElementById('itemTableBody');
    const empty = document.getElementById('itemTableEmpty');
    const limit = document.getElementById('itemTableLimit');

    tbody.innerHTML = '';

    if (!itens.length) {
      empty.querySelector('td').setAttribute('data-i18n', 'admin.cat.items.empty');
      empty.querySelector('td').textContent = I18n.t('admin.cat.items.empty');
      tbody.appendChild(empty);
      limit.style.display = 'none';
      return;
    }

    limit.style.display = itens.length >= 100 ? '' : 'none';

    itens.forEach(item => {
      const isSel = selected.has(item.id);
      const tr = document.createElement('tr');
      tr.dataset.id = item.id;
      if (isSel) tr.classList.add('selected-row');

      tr.innerHTML = `
        <td style="text-align:center">
          <input type="checkbox" class="item-check" ${isSel ? 'checked' : ''}>
        </td>
        <td>
          ${item.imagem_url
            ? `<img src="${item.imagem_url}" class="item-img" alt="">`
            : `<span class="item-img-placeholder"></span>`}
        </td>
        <td><span class="mono" style="font-size:11px;color:var(--parch-faint)">${item.id_externo}</span></td>
        <td style="color:var(--parch)">${itemName(item)}</td>
        <td style="font-size:13px;color:var(--parch-faint)">${catName(item.categoria)}</td>
      `;

      tr.addEventListener('click', e => {
        if (e.target.tagName === 'INPUT') return;
        toggleItem(item);
      });
      tr.querySelector('.item-check').addEventListener('change', () => toggleItem(item));

      tbody.appendChild(tr);
    });
  }

  async function buscar(q) {
    try {
      const res   = await fetch(BUSCA_URL + '?q=' + encodeURIComponent(q));
      const itens = await res.json();
      renderRows(itens);
    } catch (_) {}
  }

  /* ── events ── */
  document.getElementById('itemBusca').addEventListener('input', function () {
    clearTimeout(debounce);
    debounce = setTimeout(() => buscar(this.value), 320);
  });

  document.getElementById('btnDesmarcarTodos').addEventListener('click', () => {
    selected.clear();
    updateUI();
  });

  /* re-render names when locale changes */
  document.addEventListener('i18n:ready', () => {
    document.querySelectorAll('#itemTableBody tr[data-id]').forEach(row => {
      const id = Number(row.dataset.id);
      if (selected.has(id)) {
        const item = selected.get(id);
        row.cells[3].textContent = itemName(item);
        row.cells[4].textContent = catName(item.categoria);
      }
    });
  });
})();
</script>
@endpush

@endsection

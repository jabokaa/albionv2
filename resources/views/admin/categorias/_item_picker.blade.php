{{--
  Variáveis:
    $preloadId  (int|null) – ID da categoria para pré-carregar itens já vinculados (edit)
    $formId     (string)   – ID do <form> pai para injetar os hidden inputs
--}}
@php $preloadId = $preloadId ?? null; @endphp

<div class="card" id="itemPickerCard"
     data-busca-url="{{ route('admin.itens.busca') }}"
     data-preload-id="{{ $preloadId ?? '' }}"
     data-form-id="{{ $formId ?? 'formCriarCategoria' }}"
     style="margin-bottom:24px">
  <div class="card-head" style="flex-wrap:wrap;gap:12px">
    <h2>
      <span data-i18n="admin.cat.items.title">Vincular Itens</span>
      <span id="pickerSelectedCount" class="badge badge-gold" style="margin-left:10px;display:none">
        0 <span data-i18n="admin.cat.items.selected">selecionados</span>
      </span>
    </h2>
    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
      <div class="search-bar">
        <div class="field">
          <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" style="width:16px;height:16px;opacity:.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
          </svg>
          <input type="text" id="pickerBusca"
                 data-i18n-placeholder="admin.cat.items.search_placeholder"
                 placeholder="Buscar item por nome ou ID...">
        </div>
      </div>
      <button type="button" id="btnSelectAll"    class="btn btn-sm" style="display:none" data-i18n="admin.cat.items.select_all_filtered">Selecionar filtrados</button>
      <button type="button" id="btnDeselectAll"  class="btn btn-sm" style="display:none" data-i18n="admin.cat.items.deselect_all">Desmarcar todos</button>
    </div>
  </div>

  <div class="tablewrap">
    <table>
      <thead>
        <tr>
          <th style="width:36px"></th>
          <th style="width:48px"></th>
          <th data-i18n="admin.cat.items.col.id">ID Externo</th>
          <th data-i18n="admin.cat.items.col.name">Nome</th>
          <th data-i18n="admin.cat.items.col.current_cat">Categoria Atual</th>
        </tr>
      </thead>
      <tbody id="pickerTableBody">
        <tr id="pickerEmptyRow">
          <td colspan="5" style="text-align:center;color:var(--parch-faint);padding:40px"
              data-i18n="{{ $preloadId ? 'admin.cat.items.loading' : 'admin.cat.items.search_hint' }}">
            {{ $preloadId ? 'Carregando...' : 'Digite para buscar itens...' }}
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <div id="pickerLimitHint" style="display:none;padding:12px 22px;border-top:1px solid var(--line-soft);font-size:12px;color:var(--parch-faint)"
       data-i18n="admin.cat.items.limit_hint">
    Mostrando os primeiros 100 resultados. Refine a busca para ver mais.
  </div>
</div>

@push('styles')
<style>
  #pickerTableBody tr[data-id] { cursor: pointer; }
  #pickerTableBody tr[data-id]:hover { background: rgba(200,148,42,.06); }
  #pickerTableBody tr[data-id].sel-row { background: rgba(200,148,42,.1); }
  #pickerTableBody tr[data-id].sel-row td { color: var(--parch); }
  .p-img  { width:32px;height:32px;object-fit:contain;border-radius:3px;background:rgba(0,0,0,.3) }
  .p-img-ph { width:32px;height:32px;border-radius:3px;background:rgba(0,0,0,.3);display:inline-block }
  .p-cb   { accent-color:var(--gold);width:16px;height:16px;cursor:pointer }
</style>
@endpush

@push('scripts')
<script>
(function () {
  const _card      = document.getElementById('itemPickerCard');
  const BUSCA_URL  = _card.dataset.buscaUrl;
  const PRELOAD_ID = _card.dataset.preloadId ? Number(_card.dataset.preloadId) : null;
  const FORM_ID    = _card.dataset.formId;

  const selected      = new Map();   // id → item
  let   currentResults = [];         // last AJAX result
  let   debounce;

  /* ── language helpers ── */
  function col() {
    const map = { pt:'portugues', en:'ingles', es:'espanhol', fr:'frances' };
    const c   = window.localeToCol ? localeToCol(window._locale || 'pt-BR') : 'pt';
    return map[c] || 'ingles';
  }
  function itemName(item) { return item[col()] || item.ingles || item.portugues || item.id_externo || '—'; }
  function catName(cat)   {
    if (!cat) return '—';
    return cat[col()] || cat.ingles || cat.portugues || cat.nome || '—';
  }

  /* ── hidden inputs sync ── */
  function syncHiddenInputs() {
    const form = document.getElementById(FORM_ID);
    if (!form) return;
    form.querySelectorAll('.picker-item-id').forEach(el => el.remove());
    selected.forEach((_, id) => {
      const inp = document.createElement('input');
      inp.type  = 'hidden';
      inp.name  = 'item_ids[]';
      inp.value = id;
      inp.className = 'picker-item-id';
      form.appendChild(inp);
    });
  }

  /* ── UI refresh ── */
  function updateUI() {
    const count    = selected.size;
    const badge    = document.getElementById('pickerSelectedCount');
    const btnSel   = document.getElementById('btnSelectAll');
    const btnDesel = document.getElementById('btnDeselectAll');

    badge.firstChild.textContent = count + ' ';
    badge.style.display   = count ? 'inline-flex' : 'none';
    btnDesel.style.display = count ? '' : 'none';

    /* btnSelectAll: show when there are unselected rows in current results */
    const unselected = currentResults.filter(i => !selected.has(i.id));
    btnSel.style.display = (currentResults.length && unselected.length) ? '' : 'none';

    /* sync checkboxes in visible rows */
    document.querySelectorAll('#pickerTableBody tr[data-id]').forEach(row => {
      const id  = Number(row.dataset.id);
      const cb  = row.querySelector('.p-cb');
      const sel = selected.has(id);
      if (cb) cb.checked = sel;
      row.classList.toggle('sel-row', sel);
    });

    syncHiddenInputs();
  }

  /* ── toggle ── */
  function toggleItem(item) {
    selected.has(item.id) ? selected.delete(item.id) : selected.set(item.id, item);
    updateUI();
  }

  /* ── render ── */
  function renderRows(itens, keepSelected) {
    currentResults = itens;
    const tbody = document.getElementById('pickerTableBody');
    const empty = document.getElementById('pickerEmptyRow');
    const limit = document.getElementById('pickerLimitHint');

    tbody.innerHTML = '';

    if (!itens.length) {
      const td = empty.querySelector('td');
      td.setAttribute('data-i18n', 'admin.cat.items.empty');
      td.textContent = I18n.t('admin.cat.items.empty');
      tbody.appendChild(empty);
      limit.style.display = 'none';
      updateUI();
      return;
    }

    limit.style.display = itens.length >= 100 ? '' : 'none';

    itens.forEach(item => {
      const isSel = selected.has(item.id);
      const tr = document.createElement('tr');
      tr.dataset.id = item.id;
      if (isSel) tr.classList.add('sel-row');

      tr.innerHTML = `
        <td style="text-align:center">
          <input type="checkbox" class="p-cb"${isSel ? ' checked' : ''}>
        </td>
        <td>
          ${item.imagem_url
            ? `<img src="${item.imagem_url}" class="p-img" alt="">`
            : `<span class="p-img-ph"></span>`}
        </td>
        <td><span class="mono" style="font-size:11px;color:var(--parch-faint)">${item.id_externo}</span></td>
        <td style="color:var(--parch)">${itemName(item)}</td>
        <td style="font-size:13px;color:var(--parch-faint)">${catName(item.categoria)}</td>
      `;

      tr.addEventListener('click', e => { if (e.target.tagName !== 'INPUT') toggleItem(item); });
      tr.querySelector('.p-cb').addEventListener('change', () => toggleItem(item));

      tbody.appendChild(tr);
    });

    updateUI();
  }

  /* ── fetch ── */
  async function buscar(params) {
    try {
      const url = BUSCA_URL + '?' + new URLSearchParams(params).toString();
      const res = await fetch(url);
      return await res.json();
    } catch(_) { return []; }
  }

  /* ── search input ── */
  document.getElementById('pickerBusca').addEventListener('input', function () {
    clearTimeout(debounce);
    const q = this.value;
    debounce = setTimeout(async () => {
      const itens = await buscar({ q });
      renderRows(itens);
    }, 320);
  });

  /* ── select all filtered ── */
  document.getElementById('btnSelectAll').addEventListener('click', () => {
    currentResults.forEach(item => selected.set(item.id, item));
    updateUI();
    /* update checkboxes */
    document.querySelectorAll('#pickerTableBody tr[data-id]').forEach(row => {
      const cb = row.querySelector('.p-cb');
      if (cb) cb.checked = true;
      row.classList.add('sel-row');
    });
  });

  /* ── deselect all ── */
  document.getElementById('btnDeselectAll').addEventListener('click', () => {
    selected.clear();
    updateUI();
  });

  /* ── locale change: re-render names ── */
  document.addEventListener('i18n:ready', () => {
    document.querySelectorAll('#pickerTableBody tr[data-id]').forEach(row => {
      const id   = Number(row.dataset.id);
      const item = selected.get(id) || currentResults.find(i => i.id === id);
      if (!item) return;
      row.cells[3].textContent = itemName(item);
      row.cells[4].textContent = catName(item.categoria);
    });
  });

  /* ── preload (edit page) ── */
  let preloadReady = Promise.resolve();

  if (PRELOAD_ID) {
    preloadReady = buscar({ categoria_id: PRELOAD_ID }).then(itens => {
      itens.forEach(item => selected.set(item.id, item));
      renderRows(itens);
    });
  }

  /* ── intercept submit: wait for preload, then sync ── */
  const form = document.getElementById(FORM_ID);
  if (form) {
    form.addEventListener('submit', function (e) {
      if (form.dataset.pickerReady) return; // already processed
      e.preventDefault();
      preloadReady.then(() => {
        syncHiddenInputs();
        form.dataset.pickerReady = '1';
        form.submit();
      });
    });
  }
})();
</script>
@endpush

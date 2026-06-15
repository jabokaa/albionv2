@extends('admin.layout')

@section('title', 'Árvore de Categorias')
@section('page-title')
<span>Árvore de Categorias</span>
@endsection

@push('styles')
<style>
  .tree-wrap { display: flex; flex-direction: column; gap: 8px; }

  /* Raiz (avô) */
  .tree-root {
    background: linear-gradient(180deg, var(--panel), #1b1709);
    border: 1px solid var(--line-soft);
    border-radius: 6px;
    overflow: hidden;
  }
  .tree-root-head {
    display: flex; align-items: center; gap: 10px;
    padding: 12px 16px; cursor: pointer;
    background: rgba(200,148,42,.06);
    border-bottom: 1px solid transparent;
    user-select: none; transition: .15s;
  }
  .tree-root-head:hover { background: rgba(200,148,42,.1); }
  .tree-root.open .tree-root-head { border-bottom-color: var(--line-soft); }
  .tree-root-icon {
    width: 18px; height: 18px; flex: 0 0 auto;
    color: var(--gold); transition: transform .2s;
  }
  .tree-root.open .tree-root-icon { transform: rotate(90deg); }
  .tree-root-label {
    flex: 1; font-family: "Cinzel", serif; font-size: 13px;
    font-weight: 700; letter-spacing: .04em; color: var(--parch);
  }
  a.tree-root-label:hover { color: var(--gold-bright); text-decoration: underline; text-underline-offset: 3px; }
  .tree-root-slug {
    font-family: "JetBrains Mono", monospace; font-size: 10px;
    color: var(--parch-faint); background: rgba(0,0,0,.3);
    padding: 2px 8px; border-radius: 20px; border: 1px solid var(--line-soft);
  }
  .tree-root-count { font-family: "JetBrains Mono", monospace; font-size: 10px; color: var(--parch-faint); }
  .tree-root-body { display: none; padding: 12px; }
  .tree-root.open .tree-root-body { display: flex; flex-direction: column; gap: 8px; }

  /* Pai */
  .tree-pai {
    border: 1px solid rgba(200,148,42,.12);
    border-radius: 4px; overflow: hidden;
  }
  .tree-pai-head {
    display: flex; align-items: center; gap: 8px;
    padding: 9px 13px; cursor: pointer;
    background: rgba(0,0,0,.2);
    border-bottom: 1px solid transparent;
    user-select: none; transition: .15s;
  }
  .tree-pai-head:hover { background: rgba(200,148,42,.07); }
  .tree-pai.open .tree-pai-head { border-bottom-color: rgba(200,148,42,.1); }
  .tree-pai-icon {
    width: 14px; height: 14px; flex: 0 0 auto;
    color: var(--gold); opacity: .7; transition: transform .2s;
  }
  .tree-pai.open .tree-pai-icon { transform: rotate(90deg); }
  .tree-pai-label {
    flex: 1; font-family: "Cinzel", serif; font-size: 12px;
    font-weight: 600; letter-spacing: .03em; color: var(--parch-dim);
  }
  a.tree-pai-label:hover { color: var(--gold-bright); text-decoration: underline; text-underline-offset: 3px; }
  .tree-pai-slug { font-family: "JetBrains Mono", monospace; font-size: 10px; color: var(--parch-faint); }
  .tree-pai-count {
    font-family: "JetBrains Mono", monospace; font-size: 10px;
    color: var(--parch-faint); padding: 1px 7px; border-radius: 20px;
    border: 1px solid var(--line-soft);
  }
  .tree-pai-body { display: none; }
  .tree-pai.open .tree-pai-body { display: block; }

  /* Filhos */
  .tree-filhos { display: flex; flex-direction: column; gap: 1px; background: rgba(200,148,42,.07); padding: 1px; }
  .tree-filho {
    background: var(--panel); padding: 8px 13px;
    display: flex; align-items: center; gap: 7px;
    transition: background .12s;
  }
  .tree-filho:hover { background: rgba(200,148,42,.08); }
  .tree-filho-dot { width: 4px; height: 4px; border-radius: 50%; background: var(--gold); opacity: .5; flex: 0 0 auto; }
  .tree-filho-label { font-size: 13px; color: var(--parch-dim); flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  a.tree-filho-label:hover { color: var(--gold-bright); }
  .tree-filho-slug { font-family: "JetBrains Mono", monospace; font-size: 10px; color: var(--parch-faint); white-space: nowrap; }

  /* Pai sem filhos */
  .tree-pai-empty { padding: 8px 13px; font-family: "JetBrains Mono", monospace; font-size: 11px; color: var(--parch-faint); font-style: italic; }

  /* Toolbar */
  .tree-toolbar { display: flex; gap: 8px; align-items: center; margin-bottom: 16px; flex-wrap: wrap; }

  /* ── Drag & drop ─────────────────────────────── */
  .drag-handle {
    cursor: grab; flex: 0 0 auto;
    display: flex; align-items: center; padding: 0 4px;
    color: var(--parch-faint); opacity: .5;
    transition: opacity .15s;
  }
  .drag-handle:hover { opacity: 1; color: var(--gold); }
  .drag-handle:active { cursor: grabbing; }
  .drag-handle svg { pointer-events: none; }

  .is-dragging { opacity: .35; }

  /* drop zone highlight */
  .drop-zone { transition: outline .1s, background .1s; }
  .drop-zone.drag-over {
    outline: 2px dashed var(--gold);
    outline-offset: -2px;
    background: rgba(200,148,42,.1) !important;
    border-radius: 4px;
  }
  /* drop placeholder line inside a filhos list */
  .drop-placeholder {
    height: 3px; background: var(--gold);
    border-radius: 2px; margin: 1px 8px;
    pointer-events: none;
  }
</style>
@endpush

@section('content')
<div class="breadcrumb">
  <a href="{{ route('admin.categorias.index') }}">Categorias</a>
  <span class="sep">/</span>
  <span>Árvore</span>
</div>

<div class="tree-toolbar">
  <button class="btn btn-sm" id="btnExpandAll">Expandir tudo</button>
  <button class="btn btn-sm" id="btnCollapseAll">Recolher tudo</button>
  <span style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--parch-faint)">
    {{ $raizes->count() }} raízes ·
    {{ $raizes->sum(fn($r) => $r->filhos->count()) }} pais ·
    {{ $raizes->sum(fn($r) => $r->filhos->sum(fn($p) => $p->filhos->count())) }} filhos
  </span>
</div>

<div class="tree-wrap">
  @foreach($raizes as $raiz)
    <div class="tree-root" id="root-{{ $raiz->id }}">

      {{-- Cabeçalho da raiz --}}
      <div class="tree-root-head" onclick="toggleNode(this.closest('.tree-root'))">
        <svg class="tree-root-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
        <a href="{{ route('admin.categorias.edit', $raiz) }}"
           class="tree-root-label loc-name"
           onclick="event.stopPropagation()"
           data-pt="{{ $raiz->portugues }}"
           data-en="{{ $raiz->ingles }}"
           data-es="{{ $raiz->espanhol }}"
           data-fr="{{ $raiz->frances }}">
          {{ $raiz->portugues ?: $raiz->ingles ?: $raiz->nome }}
        </a>
        <span class="tree-root-slug">{{ $raiz->nome }}</span>
        <span class="tree-root-count">{{ $raiz->filhos->count() }} pais</span>
      </div>

      {{-- Zona de drop para pais → esta raiz vira o novo pai --}}
      <div class="tree-root-body drop-zone"
           data-accept="pai"
           data-new-pai="{{ $raiz->id }}">
        @forelse($raiz->filhos as $pai)
          <div class="tree-pai" data-id="{{ $pai->id }}" data-level="pai">

            {{-- Cabeçalho do pai --}}
            <div class="tree-pai-head" onclick="toggleNode(this.closest('.tree-pai'))">
              {{-- Handle de arrastar --}}
              <span class="drag-handle"
                    draggable="true"
                    data-drag-id="{{ $pai->id }}"
                    data-drag-level="pai"
                    onclick="event.stopPropagation()">
                <svg width="12" height="16" viewBox="0 0 12 16" fill="currentColor">
                  <circle cx="4" cy="3"  r="1.5"/><circle cx="8" cy="3"  r="1.5"/>
                  <circle cx="4" cy="8"  r="1.5"/><circle cx="8" cy="8"  r="1.5"/>
                  <circle cx="4" cy="13" r="1.5"/><circle cx="8" cy="13" r="1.5"/>
                </svg>
              </span>
              <svg class="tree-pai-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
              </svg>
              <a href="{{ route('admin.categorias.edit', $pai) }}"
                 class="tree-pai-label loc-name"
                 onclick="event.stopPropagation()"
                 data-pt="{{ $pai->portugues }}"
                 data-en="{{ $pai->ingles }}"
                 data-es="{{ $pai->espanhol }}"
                 data-fr="{{ $pai->frances }}">
                {{ $pai->portugues ?: $pai->ingles ?: $pai->nome }}
              </a>
              <span class="tree-pai-slug">{{ $pai->nome }}</span>
              @if($pai->filhos->count())
                <span class="tree-pai-count">{{ $pai->filhos->count() }}</span>
              @endif
            </div>

            {{-- Zona de drop para filhos → este pai vira o novo pai --}}
            <div class="tree-pai-body drop-zone"
                 data-accept="filho"
                 data-new-pai="{{ $pai->id }}">
              @if($pai->filhos->count())
                <div class="tree-filhos">
                  @foreach($pai->filhos as $filho)
                    <div class="tree-filho" data-id="{{ $filho->id }}" data-level="filho">
                      {{-- Handle de arrastar --}}
                      <span class="drag-handle"
                            draggable="true"
                            data-drag-id="{{ $filho->id }}"
                            data-drag-level="filho">
                        <svg width="10" height="14" viewBox="0 0 10 14" fill="currentColor">
                          <circle cx="3" cy="2.5"  r="1.3"/><circle cx="7" cy="2.5"  r="1.3"/>
                          <circle cx="3" cy="7"    r="1.3"/><circle cx="7" cy="7"    r="1.3"/>
                          <circle cx="3" cy="11.5" r="1.3"/><circle cx="7" cy="11.5" r="1.3"/>
                        </svg>
                      </span>
                      <span class="tree-filho-dot"></span>
                      <a href="{{ route('admin.categorias.edit', $filho) }}"
                         class="tree-filho-label loc-name"
                         data-pt="{{ $filho->portugues }}"
                         data-en="{{ $filho->ingles }}"
                         data-es="{{ $filho->espanhol }}"
                         data-fr="{{ $filho->frances }}"
                         title="{{ $filho->nome }}">
                        {{ $filho->portugues ?: $filho->ingles ?: $filho->nome }}
                      </a>
                      <span class="tree-filho-slug">{{ $filho->nome }}</span>
                    </div>
                  @endforeach
                </div>
              @else
                <div class="tree-pai-empty">sem subcategorias</div>
              @endif
            </div>

          </div>
        @empty
          <div style="padding:10px 4px;font-size:13px;color:var(--parch-faint);font-style:italic">
            sem subcategorias
          </div>
        @endforelse
      </div>

    </div>
  @endforeach
</div>
@endsection

@push('scripts')
<script>
const CSRF = '{{ csrf_token() }}';

/* ── Toggle ──────────────────────────────────────── */
function toggleNode(el) { el.classList.toggle('open'); }

document.getElementById('btnExpandAll').addEventListener('click', () => {
  document.querySelectorAll('.tree-root, .tree-pai').forEach(el => el.classList.add('open'));
});
document.getElementById('btnCollapseAll').addEventListener('click', () => {
  document.querySelectorAll('.tree-root, .tree-pai').forEach(el => el.classList.remove('open'));
});

/* ── Drag & Drop ─────────────────────────────────── */
let dragging = null; // { id, level, el }

// Attach events to all drag handles
document.querySelectorAll('.drag-handle[draggable]').forEach(handle => {
  handle.addEventListener('dragstart', e => {
    dragging = {
      id:    handle.dataset.dragId,
      level: handle.dataset.dragLevel,
      el:    handle.closest('[data-level]'),
    };
    // Use the whole row as drag image
    if (dragging.el) {
      e.dataTransfer.setDragImage(dragging.el, 20, 14);
    }
    e.dataTransfer.effectAllowed = 'move';
    // Delay class so the original is visible in the ghost
    requestAnimationFrame(() => dragging.el?.classList.add('is-dragging'));
  });

  handle.addEventListener('dragend', () => {
    dragging?.el?.classList.remove('is-dragging');
    dragging = null;
    clearDropOver();
  });
});

// Drop zones
document.querySelectorAll('.drop-zone').forEach(zone => {
  zone.addEventListener('dragover', e => {
    if (!dragging || zone.dataset.accept !== dragging.level) return;
    // Prevent dropping onto own current parent
    const currentParent = dragging.el?.closest('[data-id]')?.parentElement?.closest('[data-id]')
                       ?? dragging.el?.closest('.tree-root-body')?.closest('.tree-root');
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
    zone.classList.add('drag-over');
  });

  zone.addEventListener('dragleave', e => {
    // Only remove if leaving the zone entirely (not entering a child)
    if (!zone.contains(e.relatedTarget)) {
      zone.classList.remove('drag-over');
    }
  });

  zone.addEventListener('drop', async e => {
    e.preventDefault();
    zone.classList.remove('drag-over');
    if (!dragging || zone.dataset.accept !== dragging.level) return;

    const id      = dragging.id;
    const newPaiId = zone.dataset.newPai;
    dragging?.el?.classList.remove('is-dragging');
    dragging = null;

    await mover(id, newPaiId);
  });
});

function clearDropOver() {
  document.querySelectorAll('.drag-over').forEach(el => el.classList.remove('drag-over'));
}

async function mover(id, paiId) {
  try {
    const res = await fetch(`/admin/categorias/${id}/mover`, {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': CSRF,
      },
      body: JSON.stringify({ pai_id: paiId }),
    });

    if (res.ok) {
      location.reload();
    } else {
      const data = await res.json().catch(() => ({}));
      alert(data.error || 'Erro ao mover categoria.');
    }
  } catch {
    alert('Erro de conexão.');
  }
}
</script>
@endpush

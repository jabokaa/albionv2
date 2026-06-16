@extends('admin.layout')

@section('title', 'Árvore de Categorias')
@section('page-title')
<span>Árvore de Categorias</span>
@endsection

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
/* ── Tree shell ─────────────────────────────────────── */
.tree-root,
.tree-children {
  list-style: none;
  padding: 0;
  margin: 0;
}
.tree-children {
  padding-left: 32px;
  display: none;
}
.tree-children.open { display: block; }

/* ── Connector lines ────────────────────────────────── */
.tree-node { position: relative; }
.tree-children .tree-node::before {
  content: '';
  position: absolute;
  left: -16px; top: 0; bottom: 0;
  width: 1px;
  background: var(--line-soft);
  pointer-events: none;
}
.tree-children .tree-node::after {
  content: '';
  position: absolute;
  left: -16px; top: 21px;
  width: 16px; height: 1px;
  background: var(--line-soft);
  pointer-events: none;
}
.tree-children .tree-node:last-child::before {
  height: 22px;
}

/* ── Node row ───────────────────────────────────────── */
.node-row {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 5px 8px 5px 4px;
  border-radius: 4px;
  cursor: default;
  transition: background .15s;
  user-select: none;
  min-height: 38px;
}
.node-row:hover { background: rgba(200,148,42,.07); }
.node-row.drag-over {
  background: rgba(200,148,42,.15);
  outline: 1px dashed var(--gold);
  outline-offset: -1px;
}
.node-row.dragging { opacity: .35; }

/* ── Toggle button ──────────────────────────────────── */
.node-toggle {
  width: 20px; height: 20px;
  display: flex; align-items: center; justify-content: center;
  flex: 0 0 20px;
  cursor: pointer;
  color: var(--gold);
  font-size: 9px;
  background: none;
  border: 1px solid var(--line-soft);
  border-radius: 3px;
  transition: transform .2s, background .15s;
  line-height: 1;
}
.node-toggle:hover { background: rgba(200,148,42,.1); border-color: var(--gold); }
.node-toggle.expanded { transform: rotate(90deg); }
.node-toggle-placeholder { width: 20px; flex: 0 0 20px; }

/* ── Drag handle ────────────────────────────────────── */
.node-drag {
  flex: 0 0 auto;
  cursor: grab;
  color: var(--parch-faint);
  opacity: .55;
  font-size: 16px;
  line-height: 1;
  padding: 2px 4px;
  border-radius: 3px;
  transition: opacity .15s, background .15s, color .15s;
}
.node-drag:hover { opacity: 1; color: var(--gold); background: rgba(200,148,42,.1); }
.node-drag:active { cursor: grabbing; }

/* ── Icon ───────────────────────────────────────────── */
.node-icon { flex: 0 0 auto; font-size: 14px; line-height: 1; }

/* ── Name ───────────────────────────────────────────── */
.node-name {
  flex: 1;
  display: flex;
  align-items: baseline;
  gap: 10px;
  min-width: 0;
}
.node-name .display-name {
  font-family: "Spectral", serif;
  font-size: 14px;
  color: var(--parch);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.node-name .slug {
  font-family: "JetBrains Mono", monospace;
  font-size: 11px;
  color: var(--parch-faint);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 220px;
}

/* ── Actions ────────────────────────────────────────── */
.node-actions {
  display: flex;
  align-items: center;
  gap: 3px;
  opacity: 0;
  transition: opacity .15s;
  flex: 0 0 auto;
}
.node-row:hover .node-actions { opacity: 1; }

.node-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 30px; height: 30px;
  border-radius: 4px;
  border: 1px solid transparent;
  background: transparent;
  cursor: pointer;
  font-size: 15px;
  font-family: inherit;
  text-decoration: none;
  transition: border-color .15s, background .15s;
  color: var(--parch-dim);
  line-height: 1;
  padding: 0;
}
.node-btn:hover {
  border-color: var(--line);
  background: rgba(200,148,42,.08);
}
.node-btn.add {
  font-size: 18px;
  font-family: "Cinzel", serif;
  font-weight: 700;
  color: var(--gold-bright);
}
.node-btn.add:hover {
  border-color: rgba(200,148,42,.5);
  background: rgba(200,148,42,.1);
}
.node-btn.del:hover {
  border-color: rgba(200,80,60,.4);
  background: rgba(200,80,60,.1);
}
.node-btn.del-disabled {
  opacity: .25;
  cursor: not-allowed;
  pointer-events: none;
}
.node-btn.btn-ordem {
  font-size: 16px;
  color: var(--parch-dim);
}
.node-btn.btn-ordem:hover {
  color: var(--gold-bright);
  border-color: rgba(200,148,42,.4);
}
.node-btn.btn-ordem:disabled {
  opacity: .2;
  cursor: not-allowed;
  pointer-events: none;
}
/* Esconde ↑ no primeiro e ↓ no último avô */
.tree-root > .tree-node:first-child .btn-ordem[data-dir="up"],
.tree-root > .tree-node:last-child .btn-ordem[data-dir="down"] {
  opacity: .15;
  pointer-events: none;
}

/* ── Drop-to-root zone ──────────────────────────────── */
.drop-to-root {
  margin-bottom: 12px;
  border: 1px dashed var(--line-soft);
  border-radius: 5px;
  padding: 10px 16px;
  font-size: 12px;
  font-family: "JetBrains Mono", monospace;
  color: var(--parch-faint);
  text-align: center;
  transition: border-color .15s, background .15s, color .15s;
}
.drop-to-root.drag-over {
  border-color: var(--gold);
  background: rgba(200,148,42,.07);
  color: var(--gold-bright);
}
.drop-to-root.hidden { display: none; }

/* ── Toast notification ─────────────────────────────── */
.tree-toast {
  position: fixed;
  top: 20px;
  right: 24px;
  z-index: 9999;
  padding: 12px 18px;
  border-radius: 5px;
  font-size: 14px;
  font-family: "Spectral", serif;
  box-shadow: 0 8px 24px rgba(0,0,0,.5);
  animation: toastIn .25s ease;
  max-width: 360px;
}
.tree-toast.success {
  background: rgba(40,80,40,.95);
  border: 1px solid rgba(100,180,100,.4);
  color: oklch(0.82 0.12 150);
}
.tree-toast.error {
  background: rgba(80,20,15,.95);
  border: 1px solid rgba(200,80,60,.4);
  color: oklch(0.78 0.16 28);
}
@keyframes toastIn {
  from { opacity:0; transform:translateY(-8px); }
  to   { opacity:1; transform:translateY(0); }
}

/* ── Level badges ───────────────────────────────────── */
.level-legend {
  display: flex;
  gap: 18px;
  align-items: center;
  flex-wrap: wrap;
}
.level-dot {
  display: inline-block;
  width: 8px; height: 8px;
  border-radius: 50%;
  margin-right: 6px;
}
</style>
@endpush

@section('content')
<div class="breadcrumb">
  <a href="{{ route('admin.categorias.index') }}">Categorias</a>
  <span class="sep">/</span>
  <span>Árvore</span>
</div>

<div class="card">
  <div class="card-head">
    <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap">
      <h2>Árvore de Categorias</h2>
      <div class="level-legend" style="font-size:12px;font-family:'JetBrains Mono',monospace;color:var(--parch-faint)">
        <span><span class="level-dot" style="background:var(--gold-bright)"></span>avô</span>
        <span><span class="level-dot" style="background:var(--gold)"></span>pai</span>
        <span><span class="level-dot" style="background:var(--parch-faint)"></span>filho</span>
      </div>
    </div>
    <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
      <button id="btnExpandAll" class="btn btn-sm" type="button">Expandir Tudo</button>
      <button id="btnCollapseAll" class="btn btn-sm" type="button">Colapsar Tudo</button>
      <a href="{{ route('admin.categorias.create') }}" class="btn btn-gold btn-sm">+ Nova Categoria</a>
    </div>
  </div>

  <div class="card-body" style="padding:16px 24px 24px">
    <div class="drop-to-root hidden" id="dropToRoot">
      ↑ Soltar aqui para mover para categoria raiz
    </div>

    @if($raizes->isEmpty())
      <p style="color:var(--parch-faint);font-size:13px;text-align:center;padding:40px 0">
        Nenhuma categoria encontrada.
        <a href="{{ route('admin.categorias.create') }}" style="color:var(--gold-bright)">Criar a primeira →</a>
      </p>
    @else
      <ul class="tree-root" id="catTree">
        @foreach($raizes as $raiz)
          @include('admin.categorias._tree_node', ['categoria' => $raiz, 'nivel' => 0])
        @endforeach
      </ul>
    @endif
  </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
  'use strict';

  /* ── Expand / Collapse toggles ───────────────────────── */
  document.querySelectorAll('.node-toggle').forEach(btn => {
    btn.addEventListener('click', e => {
      e.stopPropagation();
      const li = btn.closest('.tree-node');
      const children = li.querySelector('.tree-children');
      if (!children) return;
      const isOpen = children.classList.toggle('open');
      btn.classList.toggle('expanded', isOpen);
    });
  });

  document.getElementById('btnExpandAll')?.addEventListener('click', () => {
    document.querySelectorAll('.tree-children').forEach(ul => ul.classList.add('open'));
    document.querySelectorAll('.node-toggle').forEach(btn => btn.classList.add('expanded'));
  });

  document.getElementById('btnCollapseAll')?.addEventListener('click', () => {
    document.querySelectorAll('.tree-children').forEach(ul => ul.classList.remove('open'));
    document.querySelectorAll('.node-toggle').forEach(btn => btn.classList.remove('expanded'));
  });

  /* ── Drag and Drop ───────────────────────────────────── */
  let draggedId = null;
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
  const dropRoot  = document.getElementById('dropToRoot');

  function clearDragHighlights() {
    document.querySelectorAll('.drag-over').forEach(el => el.classList.remove('drag-over'));
  }

  function toast(msg, type = 'error') {
    const el = document.createElement('div');
    el.className = `tree-toast ${type}`;
    el.textContent = msg;
    document.body.appendChild(el);
    setTimeout(() => el.remove(), 4000);
  }

  async function moveCategory(catId, newParentId) {
    const res = await fetch(`/admin/categorias/${catId}/mover`, {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
      },
      body: JSON.stringify({ categoria_pai_id: newParentId ?? null }),
    });

    const data = await res.json();
    if (!res.ok || !data.success) {
      toast(data.error || data.message || 'Erro ao mover categoria.');
      return false;
    }
    return true;
  }

  /* ── DRAG SOURCE: apenas o handle ⠿ ─────────────────── */
  document.querySelectorAll('.node-drag').forEach(handle => {
    const node = handle.closest('.tree-node');
    const row  = node.querySelector('.node-row');

    handle.addEventListener('dragstart', e => {
      draggedId = node.dataset.id;
      e.dataTransfer.effectAllowed = 'move';
      e.dataTransfer.setData('text/plain', draggedId);
      e.dataTransfer.setDragImage(row, 28, row.offsetHeight / 2);
      dropRoot.classList.remove('hidden');
      setTimeout(() => row.classList.add('dragging'), 0);
    });

    handle.addEventListener('dragend', () => {
      row.classList.remove('dragging');
      draggedId = null;
      dropRoot.classList.add('hidden');
      clearDragHighlights();
    });
  });

  /* ── DROP TARGETS: cada node-row ─────────────────────── */
  document.querySelectorAll('.tree-node').forEach(node => {
    const row = node.querySelector('.node-row');
    if (!row) return;

    row.addEventListener('dragover', e => {
      if (!draggedId) return;
      e.preventDefault();
      e.dataTransfer.dropEffect = 'move';
      if (draggedId !== node.dataset.id) {
        clearDragHighlights();
        row.classList.add('drag-over');
      }
    });

    row.addEventListener('dragleave', e => {
      if (!row.contains(e.relatedTarget)) {
        row.classList.remove('drag-over');
      }
    });

    row.addEventListener('drop', async e => {
      e.preventDefault();
      clearDragHighlights();
      const targetId = node.dataset.id;
      if (!draggedId || draggedId === targetId) return;

      const ok = await moveCategory(draggedId, parseInt(targetId, 10));
      if (ok) location.reload();
    });
  });

  /* ── Reordenar avôs ─────────────────────────────────── */
  document.querySelectorAll('.btn-ordem').forEach(btn => {
    btn.addEventListener('click', async e => {
      e.stopPropagation();
      const id        = btn.dataset.id;
      const direction = btn.dataset.dir;

      btn.disabled = true;
      const res = await fetch(`/admin/categorias/${id}/reordenar`, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json',
        },
        body: JSON.stringify({ direction }),
      });

      const data = await res.json();
      if (!res.ok || !data.success) {
        toast(data.error || 'Erro ao reordenar.');
        btn.disabled = false;
        return;
      }

      // Move o <li> no DOM sem reload completo
      const li = btn.closest('.tree-node');
      const list = li.parentElement;
      if (direction === 'up' && li.previousElementSibling) {
        list.insertBefore(li, li.previousElementSibling);
      } else if (direction === 'down' && li.nextElementSibling) {
        list.insertBefore(li.nextElementSibling, li);
      }
      btn.disabled = false;
    });
  });

  /* ── DROP TO ROOT ────────────────────────────────────── */
  dropRoot.addEventListener('dragover', e => {
    if (!draggedId) return;
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
    clearDragHighlights();
    dropRoot.classList.add('drag-over');
  });

  dropRoot.addEventListener('dragleave', () => {
    dropRoot.classList.remove('drag-over');
  });

  dropRoot.addEventListener('drop', async e => {
    e.preventDefault();
    dropRoot.classList.remove('drag-over');
    if (!draggedId) return;

    const ok = await moveCategory(draggedId, null);
    if (ok) location.reload();
  });

})();
</script>
@endpush

@extends('admin.layout')

@section('title', 'Árvore de Categorias')
@section('page-title')<span>Árvore de Categorias</span>@endsection

@push('styles')
<style>
/* ── Layout base ───────────────────────────── */
.tree-wrap { display:flex; flex-direction:column; gap:6px; }

/* ── Nível 0 — Raiz ────────────────────────── */
.tree-root {
  border:1px solid var(--line-soft);
  border-radius:6px; overflow:hidden;
  background:linear-gradient(180deg,var(--panel),#1b1709);
}
.tree-root-head {
  display:flex; align-items:center; gap:8px;
  padding:11px 14px;
  background:rgba(200,148,42,.07);
  border-bottom:1px solid transparent;
  cursor:pointer; user-select:none; transition:.14s;
}
.tree-root-head:hover { background:rgba(200,148,42,.12); }
.tree-root.open > .tree-root-head { border-bottom-color:var(--line-soft); }

/* ── Nível 1 — Pai ─────────────────────────── */
.tree-root-body { display:none; flex-direction:column; gap:5px; padding:10px; }
.tree-root.open > .tree-root-body { display:flex; }

.tree-pai {
  border:1px solid rgba(200,148,42,.13);
  border-radius:4px; overflow:hidden;
}
.tree-pai-head {
  display:flex; align-items:center; gap:8px;
  padding:8px 12px;
  background:rgba(0,0,0,.22);
  border-bottom:1px solid transparent;
  cursor:pointer; user-select:none; transition:.14s;
}
.tree-pai-head:hover { background:rgba(200,148,42,.08); }
.tree-pai.open > .tree-pai-head { border-bottom-color:rgba(200,148,42,.1); }

/* ── Nível 2 — Filho ───────────────────────── */
.tree-pai-body { display:none; flex-direction:column; gap:1px; padding:1px; }
.tree-pai.open > .tree-pai-body { display:flex; }

.tree-filho {
  display:flex; align-items:center; gap:7px;
  padding:7px 13px;
  background:var(--panel); transition:.12s;
}
.tree-filho:hover { background:rgba(200,148,42,.07); }

/* ── Chevrons ──────────────────────────────── */
.chevron {
  width:14px; height:14px; flex:0 0 auto;
  color:var(--gold); opacity:.7; transition:transform .18s;
}
.tree-root.open  > .tree-root-head .chevron,
.tree-pai.open   > .tree-pai-head  .chevron { transform:rotate(90deg); }

/* ── Labels ────────────────────────────────── */
.lbl-root { flex:1; font-family:"Cinzel",serif; font-size:13px; font-weight:700; letter-spacing:.04em; color:var(--parch); }
.lbl-pai  { flex:1; font-family:"Cinzel",serif; font-size:12px; font-weight:600; letter-spacing:.03em; color:var(--parch-dim); }
.lbl-filho{ flex:1; font-size:13px; color:var(--parch-dim); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
a.lbl-root:hover, a.lbl-pai:hover { color:var(--gold-bright); text-decoration:underline; text-underline-offset:3px; }
a.lbl-filho:hover { color:var(--gold-bright); }

.slug-tag {
  font-family:"JetBrains Mono",monospace; font-size:10px; color:var(--parch-faint);
  background:rgba(0,0,0,.3); padding:2px 7px; border-radius:20px;
  border:1px solid var(--line-soft); white-space:nowrap;
}
.count-tag {
  font-family:"JetBrains Mono",monospace; font-size:10px; color:var(--parch-faint);
  padding:1px 6px; border-radius:20px; border:1px solid var(--line-soft);
}
.dot { width:4px; height:4px; border-radius:50%; background:var(--gold); opacity:.45; flex:0 0 auto; }

/* ── Toolbar ───────────────────────────────── */
.tree-toolbar { display:flex; gap:8px; align-items:center; margin-bottom:16px; flex-wrap:wrap; }

/* ════════════════════════════════════════════
   DRAG & DROP
   ════════════════════════════════════════════ */

/* Grip — handle visível de arrasto */
.grip {
  cursor:grab; flex:0 0 auto; padding:2px 5px;
  color:var(--parch-faint); opacity:.45;
  border-radius:3px; transition:opacity .15s, background .15s;
  line-height:1;
}
.grip:hover { opacity:1; color:var(--gold); background:rgba(200,148,42,.12); }
.grip:active { cursor:grabbing; }
.grip svg { pointer-events:none; display:block; }

/* Item sendo arrastado */
.is-dragging { opacity:.3; }

/* Drag mode: mostra todas as drop zones disponíveis */
body.dragging-active .drop-head[data-drop-depth="0"],
body.dragging-active .drop-head[data-drop-depth="1"] {
  outline:1px dashed rgba(200,148,42,.3);
  outline-offset:inset;
}
/* Zona proibida (filho não pode receber filhos) */
body.dragging-active .tree-filho .grip { opacity:.15; cursor:not-allowed; }

/* Drop hover — destaque na zona alvo */
.drop-head.drop-hover {
  background:rgba(200,148,42,.18) !important;
  outline:2px solid var(--gold) !important;
  outline-offset:-2px;
}
/* Drop hover proibido */
.drop-head.drop-hover-forbidden {
  background:rgba(200,80,60,.12) !important;
  outline:2px solid rgba(200,80,60,.5) !important;
  outline-offset:-2px;
}

/* Linha indicadora de posição */
.drop-line {
  height:3px; border-radius:2px;
  background:var(--gold); margin:0 8px;
  display:none;
}
body.dragging-active .drop-line { display:block; }
</style>
@endpush

@section('content')
<div class="breadcrumb">
  <a href="{{ route('admin.categorias.index') }}">Categorias</a>
  <span class="sep">/</span><span>Árvore</span>
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

<div class="tree-wrap" id="treeWrap">
@foreach($raizes as $raiz)
  <div class="tree-root" data-id="{{ $raiz->id }}" data-depth="0">

    {{-- Head da raiz — drop target (depth 0) --}}
    <div class="tree-root-head drop-head"
         data-drop-id="{{ $raiz->id }}"
         data-drop-depth="0"
         onclick="toggleNode(this.closest('.tree-root'))">
      <span class="grip"
            draggable="true"
            data-drag-id="{{ $raiz->id }}"
            data-drag-depth="0"
            onclick="event.stopPropagation()">
        <svg width="10" height="14" viewBox="0 0 10 14" fill="currentColor">
          <circle cx="3" cy="2.5" r="1.4"/><circle cx="7" cy="2.5" r="1.4"/>
          <circle cx="3" cy="7"   r="1.4"/><circle cx="7" cy="7"   r="1.4"/>
          <circle cx="3" cy="11.5" r="1.4"/><circle cx="7" cy="11.5" r="1.4"/>
        </svg>
      </span>
      <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
      </svg>
      <a href="{{ route('admin.categorias.edit', $raiz) }}"
         class="lbl-root loc-name"
         onclick="event.stopPropagation()"
         data-pt="{{ $raiz->portugues }}" data-en="{{ $raiz->ingles }}"
         data-es="{{ $raiz->espanhol }}"  data-fr="{{ $raiz->frances }}">
        {{ $raiz->portugues ?: $raiz->ingles ?: $raiz->nome }}
      </a>
      <span class="slug-tag">{{ $raiz->nome }}</span>
      <span class="count-tag">{{ $raiz->filhos->count() }} pais</span>
      <a href="{{ route('admin.categorias.create', ['pai' => $raiz->id]) }}"
         class="btn btn-sm" onclick="event.stopPropagation()" title="Adicionar pai">+ Pai</a>
    </div>

    <div class="tree-root-body">
    @forelse($raiz->filhos as $pai)
      <div class="tree-pai" data-id="{{ $pai->id }}" data-depth="1">

        {{-- Head do pai — drop target (depth 1) --}}
        <div class="tree-pai-head drop-head"
             data-drop-id="{{ $pai->id }}"
             data-drop-depth="1"
             onclick="toggleNode(this.closest('.tree-pai'))">
          <span class="grip"
                draggable="true"
                data-drag-id="{{ $pai->id }}"
                data-drag-depth="1"
                onclick="event.stopPropagation()">
            <svg width="10" height="14" viewBox="0 0 10 14" fill="currentColor">
              <circle cx="3" cy="2.5" r="1.4"/><circle cx="7" cy="2.5" r="1.4"/>
              <circle cx="3" cy="7"   r="1.4"/><circle cx="7" cy="7"   r="1.4"/>
              <circle cx="3" cy="11.5" r="1.4"/><circle cx="7" cy="11.5" r="1.4"/>
            </svg>
          </span>
          <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
          </svg>
          <a href="{{ route('admin.categorias.edit', $pai) }}"
             class="lbl-pai loc-name"
             onclick="event.stopPropagation()"
             data-pt="{{ $pai->portugues }}" data-en="{{ $pai->ingles }}"
             data-es="{{ $pai->espanhol }}"  data-fr="{{ $pai->frances }}">
            {{ $pai->portugues ?: $pai->ingles ?: $pai->nome }}
          </a>
          <span class="slug-tag" style="font-size:9px">{{ $pai->nome }}</span>
          @if($pai->filhos->count())
            <span class="count-tag">{{ $pai->filhos->count() }}</span>
          @endif
          <a href="{{ route('admin.categorias.create', ['pai' => $pai->id]) }}"
             class="btn btn-sm" onclick="event.stopPropagation()" title="Adicionar filho">+ Filho</a>
        </div>

        <div class="tree-pai-body">
          @foreach($pai->filhos as $filho)
            <div class="tree-filho" data-id="{{ $filho->id }}" data-depth="2">
              <span class="grip"
                    draggable="true"
                    data-drag-id="{{ $filho->id }}"
                    data-drag-depth="2"
                    onclick="event.stopPropagation()">
                <svg width="10" height="14" viewBox="0 0 10 14" fill="currentColor">
                  <circle cx="3" cy="2.5" r="1.4"/><circle cx="7" cy="2.5" r="1.4"/>
                  <circle cx="3" cy="7"   r="1.4"/><circle cx="7" cy="7"   r="1.4"/>
                  <circle cx="3" cy="11.5" r="1.4"/><circle cx="7" cy="11.5" r="1.4"/>
                </svg>
              </span>
              <span class="dot"></span>
              <a href="{{ route('admin.categorias.edit', $filho) }}"
                 class="lbl-filho loc-name"
                 data-pt="{{ $filho->portugues }}" data-en="{{ $filho->ingles }}"
                 data-es="{{ $filho->espanhol }}"  data-fr="{{ $filho->frances }}"
                 title="{{ $filho->nome }}">
                {{ $filho->portugues ?: $filho->ingles ?: $filho->nome }}
              </a>
              <span class="slug-tag" style="font-size:9px">{{ $filho->nome }}</span>
            </div>
          @endforeach
          @if($pai->filhos->isEmpty())
            <div style="padding:7px 13px;font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--parch-faint);font-style:italic">
              sem subcategorias
            </div>
          @endif
        </div>

      </div>
    @empty
      <div style="padding:8px 4px;font-size:13px;color:var(--parch-faint);font-style:italic">sem subcategorias</div>
    @endforelse
    </div>

  </div>
@endforeach
</div>
@endsection

@push('scripts')
<script>
const CSRF = '{{ csrf_token() }}';

/* ── Toggle ──────────────────────────────────── */
function toggleNode(el) { el.classList.toggle('open'); }
document.getElementById('btnExpandAll').addEventListener('click', () => {
  document.querySelectorAll('.tree-root,.tree-pai').forEach(el => el.classList.add('open'));
});
document.getElementById('btnCollapseAll').addEventListener('click', () => {
  document.querySelectorAll('.tree-root,.tree-pai').forEach(el => el.classList.remove('open'));
});

/* ── Drag & Drop ─────────────────────────────── */
let dragging = null;
let expandTimer = null;

/* — Grips: iniciar arrasto — */
document.querySelectorAll('.grip[draggable]').forEach(grip => {
  grip.addEventListener('dragstart', e => {
    dragging = { id: grip.dataset.dragId, depth: +grip.dataset.dragDepth };

    /* ghost image = o item inteiro */
    const item = grip.closest('[data-id]');
    if (item) e.dataTransfer.setDragImage(item, 20, 16);
    e.dataTransfer.effectAllowed = 'move';

    requestAnimationFrame(() => {
      item?.classList.add('is-dragging');
      document.body.classList.add('dragging-active');
    });
  });

  grip.addEventListener('dragend', () => {
    document.querySelectorAll('.is-dragging').forEach(el => el.classList.remove('is-dragging'));
    document.querySelectorAll('.drop-hover,.drop-hover-forbidden').forEach(el => {
      el.classList.remove('drop-hover','drop-hover-forbidden');
    });
    document.body.classList.remove('dragging-active');
    clearTimeout(expandTimer);
    dragging = null;
  });
});

/* — Drop heads: receber arrasto — */
document.querySelectorAll('.drop-head').forEach(head => {
  head.addEventListener('dragover', e => {
    if (!dragging) return;
    const targetId    = head.dataset.dropId;
    const targetDepth = +head.dataset.dropDepth;

    /* não pode cair sobre si mesmo */
    if (targetId === dragging.id) return;
    /* filhos (depth 2) não aceitam filhos */
    if (targetDepth >= 2) {
      e.preventDefault();
      e.dataTransfer.dropEffect = 'none';
      clearOldHover();
      head.classList.add('drop-hover-forbidden');
      return;
    }

    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
    clearOldHover();
    head.classList.add('drop-hover');

    /* auto-expandir após 600 ms de hover */
    const node = head.closest('.tree-root,.tree-pai');
    if (node && !node.classList.contains('open')) {
      clearTimeout(expandTimer);
      expandTimer = setTimeout(() => node.classList.add('open'), 600);
    }
  });

  head.addEventListener('dragleave', e => {
    if (!head.contains(e.relatedTarget)) {
      head.classList.remove('drop-hover','drop-hover-forbidden');
      clearTimeout(expandTimer);
    }
  });

  head.addEventListener('drop', async e => {
    e.preventDefault();
    head.classList.remove('drop-hover','drop-hover-forbidden');
    clearTimeout(expandTimer);
    if (!dragging) return;

    const targetDepth = +head.dataset.dropDepth;
    if (targetDepth >= 2) return; /* bloqueio client-side */

    const id       = dragging.id;
    const newPaiId = head.dataset.dropId;
    dragging = null;

    await moverCategoria(id, newPaiId);
  });
});

function clearOldHover() {
  document.querySelectorAll('.drop-hover,.drop-hover-forbidden').forEach(el => {
    el.classList.remove('drop-hover','drop-hover-forbidden');
  });
}

async function moverCategoria(id, paiId) {
  try {
    const res = await fetch(`/admin/categorias/${id}/mover`, {
      method: 'PATCH',
      headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF },
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

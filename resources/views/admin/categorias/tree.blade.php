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
  .tree-root-slug {
    font-family: "JetBrains Mono", monospace; font-size: 10px;
    color: var(--parch-faint); background: rgba(0,0,0,.3);
    padding: 2px 8px; border-radius: 20px; border: 1px solid var(--line-soft);
  }
  .tree-root-count {
    font-family: "JetBrains Mono", monospace; font-size: 10px;
    color: var(--parch-faint);
  }
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
  .tree-pai-slug {
    font-family: "JetBrains Mono", monospace; font-size: 10px;
    color: var(--parch-faint);
  }
  .tree-pai-count {
    font-family: "JetBrains Mono", monospace; font-size: 10px;
    color: var(--parch-faint); padding: 1px 7px; border-radius: 20px;
    border: 1px solid var(--line-soft);
  }
  .tree-pai-body { display: none; }
  .tree-pai.open .tree-pai-body { display: block; }

  /* Filhos */
  .tree-filhos {
    display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1px; background: rgba(200,148,42,.07); padding: 1px;
  }
  .tree-filho {
    background: var(--panel); padding: 8px 13px;
    display: flex; align-items: center; gap: 7px;
    transition: background .12s;
  }
  .tree-filho:hover { background: rgba(200,148,42,.08); }
  .tree-filho-dot {
    width: 4px; height: 4px; border-radius: 50%;
    background: var(--gold); opacity: .5; flex: 0 0 auto;
  }
  .tree-filho-label {
    font-size: 13px; color: var(--parch-dim); flex: 1;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
  }
  .tree-filho-slug {
    font-family: "JetBrains Mono", monospace; font-size: 10px;
    color: var(--parch-faint); white-space: nowrap;
  }

  /* Pai sem filhos */
  .tree-pai-empty {
    padding: 8px 13px;
    font-family: "JetBrains Mono", monospace; font-size: 11px;
    color: var(--parch-faint); font-style: italic;
  }

  /* Expand all */
  .tree-toolbar {
    display: flex; gap: 8px; align-items: center; margin-bottom: 16px; flex-wrap: wrap;
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
        <span class="tree-root-label loc-name"
              data-pt="{{ $raiz->portugues }}"
              data-en="{{ $raiz->ingles }}"
              data-es="{{ $raiz->espanhol }}"
              data-fr="{{ $raiz->frances }}">
          {{ $raiz->portugues ?: $raiz->ingles ?: $raiz->nome }}
        </span>
        <span class="tree-root-slug">{{ $raiz->nome }}</span>
        <span class="tree-root-count">{{ $raiz->filhos->count() }} pais</span>
        <a href="{{ route('admin.categorias.edit', $raiz) }}"
           class="btn btn-sm"
           onclick="event.stopPropagation()"
           style="margin-left:8px">Editar</a>
      </div>

      {{-- Pais --}}
      <div class="tree-root-body">
        @forelse($raiz->filhos as $pai)
          <div class="tree-pai">

            {{-- Cabeçalho do pai --}}
            <div class="tree-pai-head" onclick="toggleNode(this.closest('.tree-pai'))">
              <svg class="tree-pai-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
              </svg>
              <span class="tree-pai-label loc-name"
                    data-pt="{{ $pai->portugues }}"
                    data-en="{{ $pai->ingles }}"
                    data-es="{{ $pai->espanhol }}"
                    data-fr="{{ $pai->frances }}">
                {{ $pai->portugues ?: $pai->ingles ?: $pai->nome }}
              </span>
              <span class="tree-pai-slug">{{ $pai->nome }}</span>
              @if($pai->filhos->count())
                <span class="tree-pai-count">{{ $pai->filhos->count() }}</span>
              @endif
              <a href="{{ route('admin.categorias.edit', $pai) }}"
                 class="btn btn-sm"
                 onclick="event.stopPropagation()"
                 style="margin-left:8px">Editar</a>
            </div>

            {{-- Filhos --}}
            <div class="tree-pai-body">
              @if($pai->filhos->count())
                <div class="tree-filhos">
                  @foreach($pai->filhos as $filho)
                    <div class="tree-filho">
                      <span class="tree-filho-dot"></span>
                      <span class="tree-filho-label loc-name"
                            data-pt="{{ $filho->portugues }}"
                            data-en="{{ $filho->ingles }}"
                            data-es="{{ $filho->espanhol }}"
                            data-fr="{{ $filho->frances }}"
                            title="{{ $filho->nome }}">
                        {{ $filho->portugues ?: $filho->ingles ?: $filho->nome }}
                      </span>
                      <span class="tree-filho-slug">{{ $filho->nome }}</span>
                      <a href="{{ route('admin.categorias.edit', $filho) }}"
                         class="btn btn-sm"
                         style="padding:3px 8px;font-size:10px;flex:0 0 auto">Editar</a>
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
  function toggleNode(el) {
    el.classList.toggle('open');
  }

  document.getElementById('btnExpandAll').addEventListener('click', () => {
    document.querySelectorAll('.tree-root, .tree-pai').forEach(el => el.classList.add('open'));
  });

  document.getElementById('btnCollapseAll').addEventListener('click', () => {
    document.querySelectorAll('.tree-root, .tree-pai').forEach(el => el.classList.remove('open'));
  });
</script>
@endpush

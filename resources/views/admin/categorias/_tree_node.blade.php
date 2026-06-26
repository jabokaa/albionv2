@php $hasChildren = $categoria->filhos->count() > 0; @endphp
<li class="tree-node"
    data-id="{{ $categoria->id }}"
    data-pai="{{ $categoria->categoria_pai_id ?? '' }}"
    data-ordem="{{ $categoria->ordem ?? 0 }}">

  <div class="node-row">
    @if($hasChildren)
      <button class="node-toggle {{ $nivel === 0 ? 'expanded' : '' }}"
              type="button" title="Expandir/colapsar">▶</button>
    @else
      <span class="node-toggle-placeholder"></span>
    @endif

    <span class="node-drag" draggable="true" title="Arrastar para reorganizar">⠿</span>

    <span class="node-icon">{{ $hasChildren ? '📁' : '📄' }}</span>

    <span class="node-name">
      <span class="loc-name"
            data-pt="{{ $categoria->portugues }}"
            data-en="{{ $categoria->ingles }}"
            data-es="{{ $categoria->espanhol }}"
            data-fr="{{ $categoria->frances }}">
        {{ $categoria->portugues ?: $categoria->ingles ?: $categoria->nome }}
      </span>
      <span class="slug">{{ $categoria->nome }}</span>
    </span>

    <div class="node-actions">
      <button class="node-btn btn-ordem" data-dir="up" data-id="{{ $categoria->id }}" data-nivel="{{ $nivel }}" title="Mover para cima">↑</button>
      <button class="node-btn btn-ordem" data-dir="down" data-id="{{ $categoria->id }}" data-nivel="{{ $nivel }}" title="Mover para baixo">↓</button>
      @if($nivel < 2)
        <a href="{{ route('admin.categorias.create', ['pai' => $categoria->id]) }}"
           class="node-btn add" title="Adicionar subcategoria">+</a>
      @endif
      <a href="{{ route('admin.categorias.edit', $categoria) }}"
         class="node-btn" title="Editar">✏️</a>
      @if(!$hasChildren)
        <form method="POST" action="{{ route('admin.categorias.destroy', $categoria) }}"
              style="display:contents"
              onsubmit="return confirm('Inativar «{{ addslashes($categoria->nome) }}»?')">
          @csrf @method('DELETE')
          <input type="hidden" name="_from" value="tree">
          <button type="submit" class="node-btn del" title="Inativar categoria">🗑️</button>
        </form>
      @else
        <span class="node-btn del-disabled" title="Remova as subcategorias primeiro">🗑️</span>
      @endif
      <button type="button"
              class="node-btn del-force"
              data-id="{{ $categoria->id }}"
              data-nome="{{ addslashes($categoria->portugues ?: $categoria->ingles ?: $categoria->nome) }}"
              data-tem-filhos="{{ $hasChildren ? '1' : '0' }}"
              title="Excluir forçado — itens ficam sem categoria">⚡</button>
    </div>
  </div>

  @if($hasChildren)
    <ul class="tree-children {{ $nivel === 0 ? 'open' : '' }}">
      @foreach($categoria->filhos as $filho)
        @include('admin.categorias._tree_node', ['categoria' => $filho, 'nivel' => $nivel + 1])
      @endforeach
    </ul>
  @endif
</li>

@extends('admin.layout')

@section('title', 'Editar Categoria')
@section('page-title', 'Editar Categoria')

@section('content')
<div class="breadcrumb">
  <a href="{{ route('admin.categorias.index') }}">Categorias</a>
  <span class="sep">/</span><span>{{ $categoria->portugues ?: $categoria->nome }}</span>
</div>

<div style="display:grid;grid-template-columns:1fr 320px;gap:20px;align-items:start">

  <div class="card">
    <div class="card-head">
      <h2>Editar: <span class="mono" style="font-size:13px;font-family:'JetBrains Mono',monospace;font-weight:400">{{ $categoria->nome }}</span></h2>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('admin.categorias.update', $categoria) }}">
        @csrf @method('PATCH')
        <div class="form-grid">
          <div class="form-group full">
            <label for="nome">Nome / Slug *</label>
            <input type="text" id="nome" name="nome" value="{{ old('nome', $categoria->nome) }}" required />
            @error('nome')<span class="field-error">{{ $message }}</span>@enderror
          </div>

          <div class="form-group full">
            <label for="categoria_pai_id">Categoria Pai</label>
            <select id="categoria_pai_id" name="categoria_pai_id">
              <option value="">— Nenhuma (categoria raiz) —</option>
              @foreach($pais as $pai)
                <option value="{{ $pai->id }}"
                  {{ old('categoria_pai_id', $categoria->categoria_pai_id) == $pai->id ? 'selected' : '' }}>
                  {{ $pai->portugues ?: $pai->nome }}
                  @if($pai->pai) (filho de: {{ $pai->pai->portugues ?: $pai->pai->nome }}) @endif
                </option>
              @endforeach
            </select>
            @error('categoria_pai_id')<span class="field-error">{{ $message }}</span>@enderror
          </div>

          <div class="form-group">
            <label for="portugues">Português</label>
            <input type="text" id="portugues" name="portugues" value="{{ old('portugues', $categoria->portugues) }}" />
            @error('portugues')<span class="field-error">{{ $message }}</span>@enderror
          </div>

          <div class="form-group">
            <label for="ingles">Inglês</label>
            <input type="text" id="ingles" name="ingles" value="{{ old('ingles', $categoria->ingles) }}" />
            @error('ingles')<span class="field-error">{{ $message }}</span>@enderror
          </div>

          <div class="form-group">
            <label for="espanhol">Espanhol</label>
            <input type="text" id="espanhol" name="espanhol" value="{{ old('espanhol', $categoria->espanhol) }}" />
            @error('espanhol')<span class="field-error">{{ $message }}</span>@enderror
          </div>

          <div class="form-group">
            <label for="frances">Francês</label>
            <input type="text" id="frances" name="frances" value="{{ old('frances', $categoria->frances) }}" />
            @error('frances')<span class="field-error">{{ $message }}</span>@enderror
          </div>
        </div>

        <div style="display:flex;gap:10px;margin-top:24px;padding-top:20px;border-top:1px solid var(--line-soft)">
          <button type="submit" class="btn btn-gold">Salvar Alterações</button>
          <a href="{{ route('admin.categorias.index') }}" class="btn">Cancelar</a>
        </div>
      </form>
    </div>
  </div>

  {{-- Info sidebar --}}
  <div style="display:flex;flex-direction:column;gap:14px">

    {{-- Hierarquia --}}
    <div class="card">
      <div class="card-head"><h2>Hierarquia</h2></div>
      <div class="card-body" style="padding:16px">
        @php $ancestrais = $categoria->ancestrais() @endphp
        @if(count($ancestrais))
          @foreach($ancestrais as $i => $a)
            <div style="font-size:13px;color:var(--parch-faint);padding:4px 0;padding-left:{{ $i * 14 }}px">
              <span class="cat-indent">{{ $i > 0 ? str_repeat('└', 1) : '' }}</span>
              <a href="{{ route('admin.categorias.edit', $a) }}" style="color:var(--parch-dim)">{{ $a->portugues ?: $a->nome }}</a>
            </div>
          @endforeach
          <div style="font-size:13px;padding:4px 0;padding-left:{{ count($ancestrais) * 14 }}px;color:var(--gold-bright)">
            <span class="cat-indent">└</span> <strong>{{ $categoria->portugues ?: $categoria->nome }}</strong>
          </div>
        @else
          <span style="color:var(--parch-faint);font-size:13px">Categoria raiz</span>
        @endif

        @if($categoria->filhos()->count())
          <div style="margin-top:12px;padding-top:12px;border-top:1px solid var(--line-soft)">
            <div style="font-family:'Cinzel',serif;font-size:10px;letter-spacing:.2em;color:var(--gold);margin-bottom:8px;text-transform:uppercase">Subcategorias</div>
            @foreach($categoria->filhos()->orderBy('nome')->get() as $filho)
              <div style="font-size:13px;padding:3px 0;color:var(--parch-dim)">
                <span class="cat-indent">└</span>
                <a href="{{ route('admin.categorias.edit', $filho) }}">{{ $filho->portugues ?: $filho->nome }}</a>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>

    {{-- Estatísticas --}}
    <div class="card">
      <div class="card-head"><h2>Estatísticas</h2></div>
      <div class="card-body" style="padding:16px">
        <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid var(--line-soft);font-size:13px">
          <span style="color:var(--parch-faint)">Itens diretos</span>
          <span class="badge">{{ $categoria->itens()->count() }}</span>
        </div>
        <div style="display:flex;justify-content:space-between;padding:6px 0;font-size:13px">
          <span style="color:var(--parch-faint)">Subcategorias</span>
          <span class="badge">{{ $categoria->filhos()->count() }}</span>
        </div>
      </div>
    </div>

    {{-- Zona de perigo --}}
    <div class="card" style="border-color:rgba(200,80,60,.3)">
      <div class="card-head" style="border-color:rgba(200,80,60,.2)"><h2 style="color:var(--neg);font-size:14px">Zona de Perigo</h2></div>
      <div class="card-body" style="padding:16px">
        <form method="POST" action="{{ route('admin.categorias.destroy', $categoria) }}"
              onsubmit="return confirm('Excluir permanentemente «{{ $categoria->nome }}»? Esta ação não pode ser desfeita.')">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-danger btn-sm" style="width:100%;justify-content:center">Excluir Categoria</button>
        </form>
        <p style="font-size:12px;color:var(--parch-faint);margin-top:10px;line-height:1.5">
          Só é possível excluir se não houver subcategorias ou itens associados.
        </p>
      </div>
    </div>

  </div>
</div>

@push('styles')
<style>
  @media(max-width:900px){
    div[style*="grid-template-columns:1fr 320px"]{display:block}
    div[style*="grid-template-columns:1fr 320px"] > div:last-child{margin-top:16px}
  }
</style>
@endpush
@endsection

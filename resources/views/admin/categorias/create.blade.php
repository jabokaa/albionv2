@extends('admin.layout')

@section('title', 'Nova Categoria')
@section('page-title', 'Nova Categoria')

@section('content')
<div class="breadcrumb">
  <a href="{{ route('admin.categorias.index') }}">Categorias</a>
  <span class="sep">/</span><span>Nova</span>
</div>

<div class="card" style="max-width:780px">
  <div class="card-head">
    <h2>Criar Categoria</h2>
  </div>
  <div class="card-body">
    <form method="POST" action="{{ route('admin.categorias.store') }}">
      @csrf
      <div class="form-grid">
        <div class="form-group full">
          <label for="nome">Nome / Slug *</label>
          <input type="text" id="nome" name="nome" value="{{ old('nome') }}" placeholder="ex: weapons_sword" required />
          @error('nome')<span class="field-error">{{ $message }}</span>@enderror
          <span style="font-size:12px;color:var(--parch-faint);margin-top:2px">Identificador interno único (letras minúsculas, sem espaços)</span>
        </div>

        <div class="form-group full">
          <label for="categoria_pai_id">Categoria Pai</label>
          <select id="categoria_pai_id" name="categoria_pai_id">
            <option value="">— Nenhuma (categoria raiz) —</option>
            @foreach($pais as $pai)
              <option value="{{ $pai->id }}" {{ old('categoria_pai_id') == $pai->id ? 'selected' : '' }}>
                {{ $pai->portugues ?: $pai->nome }}
                @if($pai->pai) (filho de: {{ $pai->pai->portugues ?: $pai->pai->nome }}) @endif
              </option>
            @endforeach
          </select>
          @error('categoria_pai_id')<span class="field-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label for="portugues">Português</label>
          <input type="text" id="portugues" name="portugues" value="{{ old('portugues') }}" placeholder="ex: Espadas" />
          @error('portugues')<span class="field-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label for="ingles">Inglês</label>
          <input type="text" id="ingles" name="ingles" value="{{ old('ingles') }}" placeholder="ex: Swords" />
          @error('ingles')<span class="field-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label for="espanhol">Espanhol</label>
          <input type="text" id="espanhol" name="espanhol" value="{{ old('espanhol') }}" placeholder="ex: Espadas" />
          @error('espanhol')<span class="field-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label for="frances">Francês</label>
          <input type="text" id="frances" name="frances" value="{{ old('frances') }}" placeholder="ex: Épées" />
          @error('frances')<span class="field-error">{{ $message }}</span>@enderror
        </div>
      </div>

      <div style="display:flex;gap:10px;margin-top:24px;padding-top:20px;border-top:1px solid var(--line-soft)">
        <button type="submit" class="btn btn-gold">Criar Categoria</button>
        <a href="{{ route('admin.categorias.index') }}" class="btn">Cancelar</a>
      </div>
    </form>
  </div>
</div>
@endsection

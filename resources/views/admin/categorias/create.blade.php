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

<div class="card" style="max-width:780px">
  <div class="card-head">
    <h2 data-i18n="admin.cat.create.title">Criar Categoria</h2>
  </div>
  <div class="card-body">
    <form method="POST" action="{{ route('admin.categorias.store') }}">
      @csrf
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
              <option value="{{ $pai->id }}" {{ old('categoria_pai_id') == $pai->id ? 'selected' : '' }}>
                {{ $pai->portugues ?: $pai->nome }}
                @if($pai->pai) ({{ $pai->pai->portugues ?: $pai->pai->nome }}) @endif
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

      <div style="display:flex;gap:10px;margin-top:24px;padding-top:20px;border-top:1px solid var(--line-soft)">
        <button type="submit" class="btn btn-gold" data-i18n="admin.cat.btn.create">Criar Categoria</button>
        <a href="{{ route('admin.categorias.index') }}" class="btn" data-i18n="admin.cat.btn.cancel">Cancelar</a>
      </div>
    </form>
  </div>
</div>
@endsection

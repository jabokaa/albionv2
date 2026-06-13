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

@include('admin.categorias._item_picker', ['preloadId' => null, 'formId' => 'formCriarCategoria'])

<div style="display:flex;gap:10px">
  <button type="submit" class="btn btn-gold" data-i18n="admin.cat.btn.create">Criar Categoria</button>
  <a href="{{ route('admin.categorias.index') }}" class="btn" data-i18n="admin.cat.btn.cancel">Cancelar</a>
</div>

</form>
@endsection

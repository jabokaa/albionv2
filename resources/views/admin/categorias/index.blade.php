@extends('admin.layout')

@section('title', 'Categorias')
@section('page-title')
<span data-i18n="admin.cat.page_title">Categorias</span>
@endsection

@section('content')
<div class="breadcrumb">
  <span>Admin</span><span class="sep">/</span><span data-i18n="admin.cat.page_title">Categorias</span>
</div>

<div class="card">
  <div class="card-head">
    <h2>
      <span data-i18n="admin.cat.count_label">Todas as categorias</span>
      ({{ $categorias->total() }})
    </h2>
    <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
      <form method="GET" action="{{ route('admin.categorias.index') }}" class="search-bar">
        <div class="field">
          <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" style="width:16px;height:16px;opacity:.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/></svg>
          <input type="text" name="busca" value="{{ $busca }}"
                 data-i18n-placeholder="admin.cat.search_placeholder"
                 placeholder="Buscar por nome...">
        </div>
        <button type="submit" class="btn btn-sm" data-i18n="admin.cat.btn.search">Buscar</button>
        @if($busca)
          <a href="{{ route('admin.categorias.index') }}" class="btn btn-sm" data-i18n="admin.cat.btn.clear">Limpar</a>
        @endif
      </form>
      <a href="{{ route('admin.categorias.create') }}" class="btn btn-gold btn-sm" data-i18n="admin.cat.btn.new">+ Nova Categoria</a>
    </div>
  </div>

  <div class="tablewrap">
    <table>
      <thead>
        <tr>
          <th data-i18n="admin.cat.col.slug">Nome (slug)</th>
          <th data-i18n="admin.cat.col.pt">Português</th>
          <th data-i18n="admin.cat.col.en">Inglês</th>
          <th data-i18n="admin.cat.col.parent">Categoria Pai</th>
          <th data-i18n="admin.cat.col.sub">Subcategorias</th>
          <th data-i18n="admin.cat.col.items">Itens</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($categorias as $cat)
          <tr>
            <td><span class="mono" style="font-size:13px">{{ $cat->nome }}</span></td>
            <td>{{ $cat->portugues ?? '—' }}</td>
            <td>{{ $cat->ingles ?? '—' }}</td>
            <td>
              @if($cat->pai)
                <span class="badge badge-gold">{{ $cat->pai->portugues ?: $cat->pai->nome }}</span>
              @else
                <span style="color:var(--parch-faint);font-size:13px" data-i18n="admin.cat.root">raiz</span>
              @endif
            </td>
            <td><span class="badge">{{ $cat->filhos()->count() }}</span></td>
            <td><span class="badge">{{ $cat->itens()->count() }}</span></td>
            <td>
              <div style="display:flex;gap:6px">
                <a href="{{ route('admin.categorias.edit', $cat) }}" class="btn btn-sm" data-i18n="admin.cat.btn.edit">Editar</a>
                <form method="POST" action="{{ route('admin.categorias.destroy', $cat) }}"
                      onsubmit="return confirm(I18n.t('admin.cat.btn.delete') + ' «{{ $cat->nome }}»?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-danger" data-i18n="admin.cat.btn.delete">Excluir</button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" style="text-align:center;color:var(--parch-faint);padding:40px" data-i18n="admin.cat.empty">Nenhuma categoria encontrada.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($categorias->hasPages())
    <div style="padding:16px 22px;border-top:1px solid var(--line-soft)">
      <div class="pagination">
        @if($categorias->onFirstPage())
          <span class="disabled" data-i18n="admin.prev">‹ Anterior</span>
        @else
          <a href="{{ $categorias->previousPageUrl() }}" data-i18n="admin.prev">‹ Anterior</a>
        @endif

        @foreach($categorias->getUrlRange(max(1, $categorias->currentPage()-2), min($categorias->lastPage(), $categorias->currentPage()+2)) as $page => $url)
          @if($page == $categorias->currentPage())
            <span class="active">{{ $page }}</span>
          @else
            <a href="{{ $url }}">{{ $page }}</a>
          @endif
        @endforeach

        @if($categorias->hasMorePages())
          <a href="{{ $categorias->nextPageUrl() }}" data-i18n="admin.next">Próxima ›</a>
        @else
          <span class="disabled" data-i18n="admin.next">Próxima ›</span>
        @endif
      </div>
    </div>
  @endif
</div>
@endsection

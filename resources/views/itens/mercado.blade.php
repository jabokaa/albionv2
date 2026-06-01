@extends('layouts.app')

@section('title', ($item->portugues ?? $item->ingles) . ' — Mercado · AlbionHub')

@push('styles')
<style>
  /* ── quality palette (maps quality_id 1–5 to enchantment-like colors) ── */
  :root{
    --q1:#8a7f60;--q2:oklch(0.72 0.13 150);--q3:#6fa8c8;--q4:#b08fd6;--q5:#E8B84B;
  }

  /* ── page header ─────────────────────────────────── */
  .page-head{position:relative;border-bottom:1px solid var(--line);overflow:hidden;background:linear-gradient(180deg,#1d1910,var(--bg))}
  .page-head::before{content:"";position:absolute;right:-80px;top:-120px;width:420px;height:420px;border-radius:50%;background:radial-gradient(circle,rgba(232,184,75,.12),transparent 65%)}
  .page-head-inner{position:relative;z-index:2;padding:48px 28px 40px;max-width:1240px;margin:0 auto}
  .crumb{font-family:"JetBrains Mono",monospace;font-size:12px;letter-spacing:.08em;color:var(--parch-faint);margin-bottom:18px}
  .crumb a:hover{color:var(--gold-bright)}

  /* ── item hero ───────────────────────────────────── */
  .item-hero{display:flex;align-items:flex-start;gap:36px;margin-top:6px}
  .item-thumb-lg{position:relative;flex:0 0 160px;width:160px;height:160px;border-radius:8px;display:flex;align-items:center;justify-content:center;
    background:radial-gradient(circle at 50% 38%,rgba(200,148,42,.14),transparent 62%),
    repeating-linear-gradient(135deg,#221d10,#221d10 12px,#1d190d 12px,#1d190d 24px);
    border:1px solid var(--line-soft)}
  .item-thumb-lg img{width:80%;height:80%;object-fit:contain;filter:drop-shadow(0 8px 18px rgba(0,0,0,.6))}
  .item-thumb-lg .fallback{display:none;color:var(--parch-faint)}
  .item-thumb-lg .fallback svg{width:52px;height:52px;opacity:.4}
  .item-thumb-lg.err img{display:none}.item-thumb-lg.err .fallback{display:flex;align-items:center;justify-content:center}
  .tier-badge{position:absolute;top:10px;left:10px;font-family:"JetBrains Mono",monospace;font-weight:700;font-size:11px;color:var(--gold-bright);border:1px solid var(--line);background:rgba(13,12,8,.8);padding:3px 7px;border-radius:3px}
  .ench-badge{position:absolute;top:10px;right:10px;display:flex;align-items:center;gap:5px;font-family:"JetBrains Mono",monospace;font-weight:700;font-size:11px;color:var(--parch);border:1px solid var(--line);background:rgba(13,12,8,.8);padding:3px 8px;border-radius:3px}
  .ench-badge .gem{width:8px;height:8px;transform:rotate(45deg);border-radius:1px}
  .ench-badge[data-e="0"] .gem{background:var(--q1)}.ench-badge[data-e="1"] .gem{background:var(--q2)}
  .ench-badge[data-e="2"] .gem{background:var(--q3)}.ench-badge[data-e="3"] .gem{background:var(--q4)}
  .ench-badge[data-e="4"] .gem{background:var(--q5)}
  .item-info{flex:1;min-width:0}
  .item-cat{font-family:"JetBrains Mono",monospace;font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:var(--gold);margin-bottom:10px}
  .item-name{font-size:clamp(24px,4vw,40px);font-weight:900;line-height:1.15;color:var(--parch);margin-bottom:14px}
  .item-meta{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:22px}
  .meta-tag{font-family:"JetBrains Mono",monospace;font-size:12px;letter-spacing:.06em;padding:5px 12px;border:1px solid var(--line-soft);border-radius:20px;color:var(--parch-faint)}
  .meta-tag b{color:var(--parch)}
  .hero-actions{display:flex;gap:12px;flex-wrap:wrap}

  /* ── city cards ──────────────────────────────────── */
  .prices-section{padding:36px 0 60px}
  .prices-section .sec-head{margin-bottom:28px}
  .prices-section .sec-head h2{font-size:clamp(22px,3vw,30px);margin-top:10px}
  .city-grid{display:flex;flex-direction:column;gap:22px}
  .city-card{background:linear-gradient(180deg,#1d1a10,#15130b);border:1px solid var(--line-soft);border-radius:6px;overflow:hidden}
  .city-header{display:flex;align-items:center;gap:14px;padding:16px 22px;border-bottom:1px solid var(--line-soft);background:rgba(200,148,42,.04)}
  .city-dot{width:9px;height:9px;background:var(--gold-bright);transform:rotate(45deg);flex:0 0 auto}
  .city-name{font-family:"Cinzel",serif;font-weight:700;font-size:16px;letter-spacing:.06em;color:var(--parch)}
  .city-updated{margin-left:auto;font-family:"JetBrains Mono",monospace;font-size:11px;color:var(--parch-faint);letter-spacing:.04em}

  /* ── quality gem inside table ────────────────────── */
  .quality-cell{display:flex;align-items:center;gap:9px}
  .quality-gem{width:9px;height:9px;transform:rotate(45deg);border-radius:1px;flex:0 0 auto}
  .qgem-1{background:var(--q1)}.qgem-2{background:var(--q2)}.qgem-3{background:var(--q3)}
  .qgem-4{background:var(--q4)}.qgem-5{background:var(--q5)}

  /* ── silver value ────────────────────────────────── */
  .silver{color:var(--parch);font-family:"JetBrains Mono",monospace;font-size:14px}
  .silver .unit{font-size:10px;letter-spacing:.1em;color:var(--parch-faint);margin-left:3px;text-transform:uppercase}
  .silver.zero{color:var(--parch-faint);font-style:italic}

  /* ── empty state ─────────────────────────────────── */
  .empty-prices{text-align:center;padding:70px 20px;border:1px dashed var(--line-soft);border-radius:8px;color:var(--parch-faint);font-style:italic}

  @media(max-width:680px){
    .item-hero{flex-direction:column;align-items:center;text-align:center}
    .item-meta{justify-content:center}
    .hero-actions{justify-content:center}
    .item-thumb-lg{flex:0 0 120px;width:120px;height:120px}
    thead th:is(.hide-mobile),tbody td:is(.hide-mobile){display:none}
  }
</style>
@endpush

@section('content')

@php
  $ench    = (int) $item->encantamento;
  $tier    = preg_match('/^T(\d+)_/', $item->id_externo, $m) ? 'T'.$m[1] : '';
  $imgUrl  = 'https://render.albiononline.com/v1/item/'
           . $item->id_externo
           . ($ench > 0 ? '@'.$ench : '')
           . '.png?size=217&quality=1';
  $catNome = optional($item->categoria)->portugues ?? optional($item->categoria)->ingles ?? '';
@endphp

{{-- ── PAGE HEADER ──────────────────────────────────────── --}}
<div class="page-head">
  <div class="page-head-inner">
    <div class="crumb">
      <a href="{{ url('/') }}" data-i18n="items.crumb.home">Início</a>
      / <a href="{{ route('itens.index') }}" data-i18n="items.crumb.itens">Itens</a>
      / <span style="color:var(--parch-dim)" data-i18n="item.mercado.crumb">Mercado</span>
    </div>

    <div class="item-hero">
      {{-- Imagem --}}
      <div class="item-thumb-lg" id="itemThumb">
        @if($tier)
          <span class="tier-badge">{{ $tier }}</span>
        @endif
        <span class="ench-badge" data-e="{{ $ench }}">
          <span class="gem"></span>.{{ $ench }}
        </span>
        <img src="{{ $imgUrl }}" alt="{{ $item->ingles }}"
             onerror="document.getElementById('itemThumb').classList.add('err')">
        <span class="fallback">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <rect x="3" y="3" width="18" height="18" rx="2"/>
            <path d="M3 16l5-5 4 4 4-4 5 5"/>
          </svg>
        </span>
      </div>

      {{-- Info --}}
      <div class="item-info">
        @if($catNome)
          <div class="item-cat"
               data-cat-pt="{{ optional($item->categoria)->portugues }}"
               data-cat-en="{{ optional($item->categoria)->ingles }}"
               data-cat-es="{{ optional($item->categoria)->espanhol }}"
               data-cat-fr="{{ optional($item->categoria)->frances }}">
            {{ $catNome }}
          </div>
        @endif

        <h1 class="item-name"
            data-name-pt="{{ $item->portugues ?? $item->ingles }}"
            data-name-en="{{ $item->ingles }}"
            data-name-es="{{ $item->espanhol ?? $item->ingles }}"
            data-name-fr="{{ $item->frances ?? $item->ingles }}">
          {{ $item->portugues ?? $item->ingles }}
        </h1>

        <div class="item-meta">
          @if($tier)
            <span class="meta-tag"><b>{{ $tier }}</b></span>
          @endif
          <span class="meta-tag">
            <span data-i18n="items.filter.enchantment">Encantamento</span>&nbsp;<b>.{{ $ench }}</b>
          </span>
          <span class="meta-tag mono" style="font-size:11px;letter-spacing:.05em;opacity:.7">
            {{ $item->id_externo }}
          </span>
        </div>

        <div class="hero-actions">
          @if($item->receita)
            <button type="button" class="btn btn-gold" id="btnCraft"
                    data-name="{{ $item->portugues ?? $item->ingles }}"
                    data-i18n="item.mercado.btn.craft">
              Craftar este item
            </button>
          @endif
          <a href="{{ route('itens.index') }}" class="btn btn-ghost" data-i18n="items.crumb.itens">
            ← Itens
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ── PRICES ────────────────────────────────────────────── --}}
<div class="wrap prices-section">

  <div class="sec-head">
    <span class="eyebrow solo" data-i18n="item.mercado.eyebrow">Mercado</span>
    <h2 data-i18n-html="item.mercado.section_title">Preços por cidade</h2>
  </div>

  @if($precosPorCidade->isEmpty())
    <div class="empty-prices" data-i18n="item.mercado.empty">
      Nenhum dado de preço disponível para este item.
    </div>
  @else
    <div class="city-grid">
      @foreach($precosPorCidade as $cidadeId => $precos)
        @php
          $cidade     = $precos->first()->cidade;
          $nomeCity   = $cidade->portugues ?? $cidade->ingles ?? $cidade->nome;
          $lastUpdate = $precos->max('data_atualizacao');
        @endphp
        <div class="city-card">
          <div class="city-header">
            <span class="city-dot"></span>
            <span class="city-name"
                  data-city-pt="{{ $cidade->portugues }}"
                  data-city-en="{{ $cidade->ingles }}"
                  data-city-es="{{ $cidade->espanhol }}"
                  data-city-fr="{{ $cidade->frances }}">
              {{ $nomeCity }}
            </span>
            @if($lastUpdate)
              <span class="city-updated">
                <span data-i18n="item.mercado.col.updated">Atualizado</span>
                {{ $lastUpdate->diffForHumans() }}
              </span>
            @endif
          </div>

          <div class="tablewrap" style="border:0;border-radius:0;background:transparent">
            <table>
              <thead>
                <tr>
                  <th data-i18n="item.mercado.col.quality">Qualidade</th>
                  <th class="r" data-i18n="item.mercado.col.valor">Valor</th>
                  <th class="r hide-mobile" data-i18n="item.mercado.col.buy_order">Ordem de Compra</th>
                  <th class="r hide-mobile" data-i18n="item.mercado.col.avg_price">Preço Médio</th>
                  <th class="r" data-i18n="item.mercado.col.sold_day">Vendidos / dia</th>
                </tr>
              </thead>
              <tbody>
                @foreach($precos->sortBy('qualidade_id') as $preco)
                  @php
                    $qual     = $preco->qualidade;
                    $qualNome = $qual->portugues ?? $qual->ingles ?? $qual->nome;
                    $qid      = $preco->qualidade_id;
                  @endphp
                  <tr>
                    <td>
                      <div class="quality-cell">
                        <span class="quality-gem qgem-{{ $qid }}"></span>
                        <span data-qual-pt="{{ $qual->portugues }}"
                              data-qual-en="{{ $qual->ingles }}"
                              data-qual-es="{{ $qual->espanhol }}"
                              data-qual-fr="{{ $qual->frances }}">
                          {{ $qualNome }}
                        </span>
                      </div>
                    </td>
                    <td class="r">
                      @if($preco->valor > 0)
                        <span class="silver">
                          {{ number_format($preco->valor, 0, ',', '.') }}
                          <span class="unit" data-i18n="market.currency">prata</span>
                        </span>
                      @else
                        <span class="silver zero">—</span>
                      @endif
                    </td>
                    <td class="r hide-mobile">
                      @if($preco->ordem_de_compra > 0)
                        <span class="silver">
                          {{ number_format($preco->ordem_de_compra, 0, ',', '.') }}
                          <span class="unit" data-i18n="market.currency">prata</span>
                        </span>
                      @else
                        <span class="silver zero">—</span>
                      @endif
                    </td>
                    <td class="r hide-mobile">
                      @if($preco->preco_medio > 0)
                        <span class="silver">
                          {{ number_format($preco->preco_medio, 0, ',', '.') }}
                          <span class="unit" data-i18n="market.currency">prata</span>
                        </span>
                      @else
                        <span class="silver zero">—</span>
                      @endif
                    </td>
                    <td class="r">
                      @if($preco->quantidade_itens_vendidos > 0)
                        <span class="silver">
                          {{ number_format($preco->quantidade_itens_vendidos, 0, ',', '.') }}
                        </span>
                      @else
                        <span class="silver zero">—</span>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      @endforeach
    </div>
  @endif

</div>

@endsection

@push('scripts')
<script>
(function () {
  const LANG_COL = {
    'pt-BR':'pt','pt':'pt','en-US':'en','en':'en',
    'es-ES':'es','es':'es','fr-FR':'fr','fr':'fr','nl-NL':'en','nl':'en',
  };

  function applyLocale(locale) {
    const col = LANG_COL[locale] || LANG_COL[locale.split('-')[0]] || 'pt';
    const K   = col.charAt(0).toUpperCase() + col.slice(1);

    /* item name */
    const h1 = document.querySelector('.item-name');
    if (h1) h1.textContent = h1.dataset['name' + K] || h1.dataset.nameEn || h1.textContent;

    /* category */
    const cat = document.querySelector('.item-cat');
    if (cat) cat.textContent = cat.dataset['cat' + K] || cat.dataset.catEn || cat.textContent;

    /* craft button label */
    const btnCraft = document.getElementById('btnCraft');
    if (btnCraft) btnCraft.textContent = I18n.t('item.mercado.btn.craft');

    /* city names */
    document.querySelectorAll('[data-city-pt]').forEach(el => {
      el.textContent = el.dataset['city' + K] || el.dataset.cityEn || el.textContent;
    });

    /* quality names */
    document.querySelectorAll('[data-qual-pt]').forEach(el => {
      el.textContent = el.dataset['qual' + K] || el.dataset.qualEn || el.textContent;
    });
  }

  document.addEventListener('i18n:ready', e => applyLocale(e.detail.locale));

  /* craft button toast */
  const btnCraft = document.getElementById('btnCraft');
  if (btnCraft) {
    const toast    = document.getElementById('toast');
    const toastMsg = document.getElementById('toastMsg');
    let toastTimer;
    btnCraft.addEventListener('click', () => {
      toastMsg.innerHTML = I18n.t('items.toast.craft', { name: `<b>${btnCraft.dataset.name}</b>` });
      toast.classList.add('show');
      clearTimeout(toastTimer);
      toastTimer = setTimeout(() => toast.classList.remove('show'), 2800);
    });
  }
})();
</script>
@endpush

@extends('layouts.app')

@section('title', ($item->portugues ?? $item->ingles) . ' — Craft · AlbionHub')

@push('styles')
<style>
  /* ── enchantment palette ─────────────────────────── */
  :root{
    --e0:#8a7f60;--e1:oklch(0.72 0.13 150);--e2:#6fa8c8;--e3:#b08fd6;--e4:#E8B84B;
  }

  /* ── page header ─────────────────────────────────── */
  .page-head{position:relative;border-bottom:1px solid var(--line);overflow:hidden;background:linear-gradient(180deg,#1d1910,var(--bg))}
  .page-head::before{content:"";position:absolute;right:-80px;top:-120px;width:420px;height:420px;border-radius:50%;background:radial-gradient(circle,rgba(232,184,75,.12),transparent 65%)}
  .page-head-inner{position:relative;z-index:2;padding:48px 28px 40px;max-width:1240px;margin:0 auto}
  .crumb{font-family:"JetBrains Mono",monospace;font-size:12px;letter-spacing:.08em;color:var(--parch-faint);margin-bottom:18px}
  .crumb a:hover{color:var(--gold-bright)}

  /* ── item hero ───────────────────────────────────── */
  .item-hero{display:flex;align-items:flex-start;gap:28px;margin-top:6px}
  .item-thumb-lg{position:relative;flex:0 0 120px;width:120px;height:120px;border-radius:8px;display:flex;align-items:center;justify-content:center;
    background:radial-gradient(circle at 50% 38%,rgba(200,148,42,.14),transparent 62%),
    repeating-linear-gradient(135deg,#221d10,#221d10 12px,#1d190d 12px,#1d190d 24px);
    border:1px solid var(--line-soft)}
  .item-thumb-lg img{width:80%;height:80%;object-fit:contain;filter:drop-shadow(0 8px 18px rgba(0,0,0,.6))}
  .item-thumb-lg .fallback{display:none;color:var(--parch-faint)}
  .item-thumb-lg.err img{display:none}.item-thumb-lg.err .fallback{display:flex;align-items:center;justify-content:center}
  .tier-badge{position:absolute;top:8px;left:8px;font-family:"JetBrains Mono",monospace;font-weight:700;font-size:11px;color:var(--gold-bright);border:1px solid var(--line);background:rgba(13,12,8,.8);padding:2px 6px;border-radius:3px}
  .ench-badge{position:absolute;top:8px;right:8px;display:flex;align-items:center;gap:5px;font-family:"JetBrains Mono",monospace;font-weight:700;font-size:11px;color:var(--parch);border:1px solid var(--line);background:rgba(13,12,8,.8);padding:2px 6px;border-radius:3px}
  .ench-badge .gem{width:7px;height:7px;transform:rotate(45deg);border-radius:1px}
  .ench-badge[data-e="0"] .gem{background:var(--e0)}.ench-badge[data-e="1"] .gem{background:var(--e1)}
  .ench-badge[data-e="2"] .gem{background:var(--e2)}.ench-badge[data-e="3"] .gem{background:var(--e3)}
  .ench-badge[data-e="4"] .gem{background:var(--e4)}
  .item-info{flex:1;min-width:0}
  .item-cat{font-family:"JetBrains Mono",monospace;font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:var(--gold);margin-bottom:8px}
  .item-name{font-size:clamp(20px,3.5vw,34px);font-weight:900;line-height:1.2;color:var(--parch);margin-bottom:12px}
  .item-meta{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:18px}
  .meta-tag{font-family:"JetBrains Mono",monospace;font-size:12px;letter-spacing:.06em;padding:4px 10px;border:1px solid var(--line-soft);border-radius:20px;color:var(--parch-faint)}
  .meta-tag b{color:var(--parch)}
  .hero-actions{display:flex;gap:10px;flex-wrap:wrap}

  /* ── craft sections ──────────────────────────────── */
  .craft-section{padding:36px 0 0}
  .result-section{padding:28px 0 0}
  .profit-section{padding:28px 0 60px}
  .sec-head{margin-bottom:20px}
  .sec-head h2{font-size:clamp(18px,2.8vw,26px);margin-top:6px}

  /* ── ingredient table ────────────────────────────── */
  .ing-thumb{position:relative;width:44px;height:44px;border-radius:4px;display:inline-flex;align-items:center;justify-content:center;flex:0 0 auto;
    background:radial-gradient(circle at 50% 38%,rgba(200,148,42,.1),transparent 60%),
    repeating-linear-gradient(135deg,#221d10,#221d10 8px,#1d190d 8px,#1d190d 16px);
    border:1px solid var(--line-soft)}
  .ing-thumb img{width:80%;height:80%;object-fit:contain}
  .ing-thumb .ing-ench{position:absolute;bottom:-3px;right:-3px;font-family:"JetBrains Mono",monospace;font-size:9px;font-weight:700;
    background:rgba(13,12,8,.92);border:1px solid var(--line-soft);border-radius:2px;padding:1px 3px;color:var(--parch-faint)}
  .ing-cell{display:flex;align-items:center;gap:12px;min-width:0;text-decoration:none;color:inherit}
  .ing-cell:hover .ing-name{color:var(--gold-bright)}
  .ing-cell:hover .ing-thumb{border-color:var(--gold)}
  .ing-name{color:var(--parch);font-size:14px;font-weight:600;line-height:1.2}
  .ing-id{font-family:"JetBrains Mono",monospace;font-size:10px;color:var(--parch-faint);margin-top:2px;letter-spacing:.04em}

  /* ── silver values ───────────────────────────────── */
  .silver{color:var(--parch);font-family:"JetBrains Mono",monospace;font-size:13px}
  .silver .unit{font-size:10px;letter-spacing:.1em;color:var(--parch-faint);margin-left:2px;text-transform:uppercase}
  .silver.zero{color:var(--parch-faint);font-style:italic}
  .city-tag{font-family:"JetBrains Mono",monospace;font-size:11px;color:var(--gold);letter-spacing:.04em;white-space:nowrap}

  /* ── result card ─────────────────────────────────── */
  .result-card{display:flex;align-items:center;gap:22px;background:linear-gradient(180deg,#1e1b10,#15130b);border:1px solid var(--line-soft);border-radius:6px;padding:22px 26px}
  .res-thumb{position:relative;flex:0 0 64px;width:64px;height:64px;border-radius:5px;display:flex;align-items:center;justify-content:center;
    background:radial-gradient(circle at 50% 38%,rgba(200,148,42,.14),transparent 62%),
    repeating-linear-gradient(135deg,#221d10,#221d10 8px,#1d190d 8px,#1d190d 16px);
    border:1px solid var(--line-soft)}
  .res-thumb img{width:80%;height:80%;object-fit:contain}
  .res-thumb .fallback{display:none;align-items:center;justify-content:center;color:var(--parch-faint)}
  .res-thumb.err img{display:none}.res-thumb.err .fallback{display:flex}
  .result-label{font-family:"Cinzel",serif;font-size:11px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--parch-faint);margin-bottom:6px}
  .result-val{font-family:"JetBrains Mono",monospace;font-size:24px;font-weight:700;color:var(--gold-bright);line-height:1}
  .result-val .unit{font-size:12px;color:var(--parch-faint);margin-left:4px;text-transform:uppercase;font-weight:400}
  .result-city{margin-top:5px}

  /* ── profit box ──────────────────────────────────── */
  .profit-box{background:linear-gradient(180deg,#1e1b10,#15130b);border:1px solid var(--line-soft);border-radius:6px;overflow:hidden}
  .profit-row{display:flex;align-items:center;justify-content:space-between;padding:16px 26px;border-bottom:1px solid var(--line-soft)}
  .profit-row:last-child{border-bottom:0}
  .profit-row.net{background:rgba(200,148,42,.06)}
  .profit-label{font-family:"Cinzel",serif;font-size:12px;font-weight:600;letter-spacing:.1em;text-transform:uppercase;color:var(--parch-dim)}
  .profit-val{font-family:"JetBrains Mono",monospace;font-size:16px;font-weight:700;display:flex;align-items:baseline;gap:5px}
  .profit-val .unit{font-size:10px;letter-spacing:.1em;color:var(--parch-faint);font-weight:400;text-transform:uppercase}
  .profit-val.positive{color:oklch(0.72 0.13 150)}
  .profit-val.negative{color:#c87272}
  .profit-val.neutral{color:var(--parch-faint)}
  .profit-row.net .profit-label{font-size:13px;color:var(--parch)}
  .profit-row.net .profit-val{font-size:22px}
  .profit-note{font-size:12px;color:var(--parch-faint);font-style:italic;padding:12px 26px;border-top:1px solid var(--line-soft);font-family:"Spectral",serif;line-height:1.6}

  @media(max-width:768px){
    .item-hero{flex-direction:column;align-items:center;text-align:center}
    .item-meta{justify-content:center}
    .hero-actions{justify-content:center}
    thead th.hide-mobile,tbody td.hide-mobile{display:none}
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
      / <a href="{{ route('itens.mercado', $item->id) }}" data-i18n="item.mercado.crumb">Mercado</a>
      / <span style="color:var(--parch-dim)" data-i18n="item.craft.crumb">Craft</span>
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
          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" opacity=".4">
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
          @if($receita->foco > 0)
            <span class="meta-tag">
              <span data-i18n="item.craft.meta.foco">Foco</span>&nbsp;<b>{{ number_format((float)$receita->foco, 0, ',', '.') }}</b>
            </span>
          @endif
        </div>

        <div class="hero-actions">
          <a href="{{ route('itens.mercado', $item->id) }}" class="btn btn-ghost" data-i18n="item.craft.btn.back_market">
            ← Mercado
          </a>
          <a href="{{ route('itens.index') }}" class="btn btn-ghost" data-i18n="items.crumb.itens">
            Itens
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ── INGREDIENTES ─────────────────────────────────────── --}}
<div class="wrap craft-section">
  <div class="sec-head">
    <span class="eyebrow solo" data-i18n="item.craft.eyebrow">Calculadora de Craft</span>
    <h2 data-i18n="item.craft.section.recipe">Ingredientes</h2>
  </div>

  <div class="tablewrap">
    <table>
      <thead>
        <tr>
          <th data-i18n="item.craft.col.ingredient">Ingrediente</th>
          <th class="r" data-i18n="item.craft.col.qty">Qtd</th>
          <th class="r" data-i18n="item.craft.col.min_order">Menor Ordem de Compra</th>
          <th class="r" data-i18n="item.craft.col.min_valor">Menor Valor</th>
          <th class="r hide-mobile" data-i18n="item.craft.col.subtotal">Subtotal</th>
        </tr>
      </thead>
      <tbody>
        @foreach($ingredientesData as $d)
          @php
            $ingItem    = $d->item;
            $ingEnch    = $ingItem ? (int) $ingItem->encantamento : 0;
            $ingImgUrl  = $ingItem
              ? 'https://render.albiononline.com/v1/item/' . $ingItem->id_externo . ($ingEnch > 0 ? '@'.$ingEnch : '') . '.png?size=80&quality=1'
              : null;
            $ingNome    = $ingItem ? ($ingItem->portugues ?? $ingItem->ingles ?? '—') : '—';
            $qty        = (float) $d->quantidade;
            $qtyDisplay = ($qty == (int) $qty) ? (int) $qty : number_format($qty, 2, ',', '.');
            $subtotal   = ($d->min_valor?->valor ?? 0) * $qty;
          @endphp
          <tr>
            {{-- Ingrediente --}}
            <td>
              <a class="ing-cell" @if($ingItem) href="{{ route('itens.mercado', $ingItem->id) }}" @endif>
                @if($ingItem)
                  <div class="ing-thumb">
                    @if($ingEnch > 0)
                      <span class="ing-ench">.{{ $ingEnch }}</span>
                    @endif
                    <img src="{{ $ingImgUrl }}" alt="{{ $ingItem->ingles ?? '' }}" loading="lazy"
                         onerror="this.style.opacity='.15'">
                  </div>
                @endif
                <div>
                  <div class="ing-name"
                       data-name-pt="{{ $ingItem?->portugues ?? $ingItem?->ingles }}"
                       data-name-en="{{ $ingItem?->ingles }}"
                       data-name-es="{{ $ingItem?->espanhol ?? $ingItem?->ingles }}"
                       data-name-fr="{{ $ingItem?->frances ?? $ingItem?->ingles }}">
                    {{ $ingNome }}
                  </div>
                  @if($ingItem)
                    <div class="ing-id">{{ $ingItem->id_externo }}</div>
                  @endif
                </div>
              </a>
            </td>
            <td class="r">
              <span style="font-family:'JetBrains Mono',monospace;font-weight:700;color:var(--gold-bright);font-size:15px">
                {{ $qtyDisplay }}
              </span>
            </td>

            {{-- Min ordem de compra --}}
            <td class="r">
              @if($d->min_ordem && $d->min_ordem->ordem_de_compra > 0)
                <span class="silver">
                  {{ number_format($d->min_ordem->ordem_de_compra, 0, ',', '.') }}
                </span>
                @if($d->min_ordem?->cidade)
                  <br><span class="city-tag"
                        data-city-pt="{{ $d->min_ordem->cidade->portugues }}"
                        data-city-en="{{ $d->min_ordem->cidade->ingles }}"
                        data-city-es="{{ $d->min_ordem->cidade->espanhol }}"
                        data-city-fr="{{ $d->min_ordem->cidade->frances }}">
                    {{ $d->min_ordem->cidade->portugues ?? $d->min_ordem->cidade->ingles ?? $d->min_ordem->cidade->nome }}
                  </span>
                @endif
              @else
                <span class="silver zero">—</span>
              @endif
            </td>

            {{-- Min valor --}}
            <td class="r">
              @if($d->min_valor && $d->min_valor->valor > 0)
                <span class="silver">
                  {{ number_format($d->min_valor->valor, 0, ',', '.') }}
                </span>
                @if($d->min_valor?->cidade)
                  <br><span class="city-tag"
                        data-city-pt="{{ $d->min_valor->cidade->portugues }}"
                        data-city-en="{{ $d->min_valor->cidade->ingles }}"
                        data-city-es="{{ $d->min_valor->cidade->espanhol }}"
                        data-city-fr="{{ $d->min_valor->cidade->frances }}">
                    {{ $d->min_valor->cidade->portugues ?? $d->min_valor->cidade->ingles ?? $d->min_valor->cidade->nome }}
                  </span>
                @endif
              @else
                <span class="silver zero">—</span>
              @endif
            </td>

            {{-- Subtotal (qty × min_valor) --}}
            <td class="r hide-mobile">
              @if($subtotal > 0)
                <span class="silver" style="color:var(--gold-bright)">
                  {{ number_format($subtotal, 0, ',', '.') }}
                  <span class="unit" data-i18n="market.currency">prata</span>
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

{{-- ── ITEM CRAFTADO (melhor venda) ────────────────────────── --}}
<div class="wrap result-section">
  <div class="sec-head">
    <h2 data-i18n="item.craft.section.result">Item Craftado</h2>
  </div>

  <div class="result-card">
    <div class="res-thumb" id="resThumbnail">
      <img src="{{ $imgUrl }}" alt="{{ $item->ingles }}"
           onerror="document.getElementById('resThumbnail').classList.add('err')">
      <span class="fallback">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" opacity=".4">
          <rect x="3" y="3" width="18" height="18" rx="2"/>
          <path d="M3 16l5-5 4 4 4-4 5 5"/>
        </svg>
      </span>
    </div>

    <div style="flex:1;min-width:0">
      <div class="result-label" data-i18n="item.craft.result.label">Melhor preço de venda</div>
      @if($maxVenda && $maxVenda->valor > 0)
        <div class="result-val">
          {{ number_format($maxVenda->valor, 0, ',', '.') }}
          <span class="unit" data-i18n="market.currency">prata</span>
        </div>
        @if($maxVenda->cidade)
          <div class="result-city">
            <span class="city-tag"
                  data-city-pt="{{ $maxVenda->cidade->portugues }}"
                  data-city-en="{{ $maxVenda->cidade->ingles }}"
                  data-city-es="{{ $maxVenda->cidade->espanhol }}"
                  data-city-fr="{{ $maxVenda->cidade->frances }}">
              {{ $maxVenda->cidade->portugues ?? $maxVenda->cidade->ingles ?? $maxVenda->cidade->nome }}
            </span>
          </div>
        @endif
      @else
        <div class="result-val" style="color:var(--parch-faint);font-size:16px;font-style:italic" data-i18n="item.craft.no_price">
          Sem dados de preço
        </div>
      @endif
    </div>
  </div>
</div>

{{-- ── RESUMO DE LUCRO ──────────────────────────────────────── --}}
<div class="wrap profit-section">
  <div class="sec-head">
    <h2 data-i18n="item.craft.profit.title">Resumo de Lucro</h2>
  </div>

  <div class="profit-box">
    <div class="profit-row">
      <span class="profit-label" data-i18n="item.craft.profit.cost">Custo Total (compra imediata)</span>
      <span class="profit-val neutral">
        @if($totalCusto > 0)
          {{ number_format($totalCusto, 0, ',', '.') }}
          <span class="unit" data-i18n="market.currency">prata</span>
        @else
          —
        @endif
      </span>
    </div>

    <div class="profit-row">
      <span class="profit-label" data-i18n="item.craft.profit.revenue">Receita (melhor venda)</span>
      <span class="profit-val" style="color:var(--parch)">
        @if($totalVenda > 0)
          {{ number_format($totalVenda, 0, ',', '.') }}
          <span class="unit" data-i18n="market.currency">prata</span>
        @else
          —
        @endif
      </span>
    </div>

    <div class="profit-row net">
      <span class="profit-label" data-i18n="item.craft.profit.net">Lucro Líquido</span>
      <span class="profit-val {{ $totalLucro > 0 ? 'positive' : ($totalLucro < 0 ? 'negative' : 'neutral') }}">
        @if($totalCusto > 0 || $totalVenda > 0)
          {{ $totalLucro > 0 ? '+' : '' }}{{ number_format($totalLucro, 0, ',', '.') }}
          <span class="unit" data-i18n="market.currency">prata</span>
        @else
          —
        @endif
      </span>
    </div>

    <div class="profit-note" data-i18n="item.craft.profit.note">
      * Custo calculado com base no menor valor de venda imediata de cada ingrediente. Receita baseada no maior valor de venda disponível para o item craftado.
    </div>
  </div>
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

    /* ingredient names */
    document.querySelectorAll('.ing-name[data-name-pt]').forEach(el => {
      el.textContent = el.dataset['name' + K] || el.dataset.nameEn || el.textContent;
    });

    /* city tags */
    document.querySelectorAll('[data-city-pt]').forEach(el => {
      el.textContent = el.dataset['city' + K] || el.dataset.cityEn || el.textContent;
    });
  }

  document.addEventListener('i18n:ready', e => applyLocale(e.detail.locale));
})();
</script>
@endpush

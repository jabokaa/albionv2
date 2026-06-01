@extends('layouts.app')

@section('title', 'Como Fazer Dinheiro no Albion — Guia de Transporte Black Market')

@push('styles')
<style>
  /* ── page header ─────────────────────────────────── */
  .page-head{position:relative;border-bottom:1px solid var(--line);overflow:hidden;background:linear-gradient(180deg,#1d1910,var(--bg))}
  .page-head::before{content:"";position:absolute;right:-80px;top:-120px;width:420px;height:420px;border-radius:50%;background:radial-gradient(circle,rgba(232,184,75,.12),transparent 65%)}
  .page-head-inner{position:relative;z-index:2;padding:48px 28px 40px;max-width:1240px;margin:0 auto}
  .crumb{font-family:"JetBrains Mono",monospace;font-size:12px;letter-spacing:.08em;color:var(--parch-faint);margin-bottom:18px}
  .crumb a:hover{color:var(--gold-bright)}
  .page-head h1{font-size:clamp(28px,4vw,48px);font-weight:900;margin-top:12px}
  .page-head .lead{margin-top:12px;color:var(--parch-dim);max-width:700px;font-weight:300;font-size:17px;line-height:1.65}

  /* ── guide layout ────────────────────────────────── */
  .guide-wrap{max-width:1240px;margin:0 auto;padding:48px 28px 80px;display:grid;grid-template-columns:1fr 320px;gap:48px;align-items:start}
  .guide-main{min-width:0}
  .guide-aside{position:sticky;top:94px}

  /* ── sections ────────────────────────────────────── */
  .gsec{margin-bottom:56px}
  .gsec-title{font-family:"Cinzel",serif;font-size:clamp(20px,2.4vw,26px);font-weight:800;color:var(--parch);margin-bottom:6px;padding-bottom:12px;border-bottom:1px solid var(--line-soft);display:flex;align-items:center;gap:12px}
  .gsec-title .ic{width:36px;height:36px;display:flex;align-items:center;justify-content:center;border:1px solid var(--line);border-radius:50%;color:var(--gold-bright);background:rgba(200,148,42,.07);flex:0 0 auto}
  .gsec-title .ic svg{width:18px;height:18px}
  .gsec p{color:var(--parch-dim);font-size:16.5px;line-height:1.75;margin-bottom:16px}
  .gsec p:last-child{margin-bottom:0}

  /* ── highlight callouts ──────────────────────────── */
  .callout{border-left:3px solid var(--gold);background:rgba(200,148,42,.07);border-radius:0 6px 6px 0;padding:18px 22px;margin:22px 0;font-size:15.5px;color:var(--parch-dim);line-height:1.7}
  .callout strong{color:var(--gold-bright);font-family:"Cinzel",serif;font-size:14px;letter-spacing:.06em;text-transform:uppercase;display:block;margin-bottom:6px}
  .callout.green{border-left-color:var(--pos);background:rgba(111,200,111,.06)}
  .callout.green strong{color:var(--pos)}
  .callout.red{border-left-color:var(--neg);background:rgba(200,80,60,.06)}
  .callout.red strong{color:var(--neg)}
  .callout.blue{border-left-color:#6fa8c8;background:rgba(111,168,200,.07)}
  .callout.blue strong{color:#6fa8c8}

  /* ── profit card ─────────────────────────────────── */
  .profit-banner{position:relative;overflow:hidden;background:linear-gradient(135deg,#241d0d,#1a1508);border:1px solid var(--gold);border-radius:8px;padding:32px 36px;margin:28px 0;display:flex;align-items:center;gap:32px;flex-wrap:wrap}
  .profit-banner::before{content:"";position:absolute;right:-40px;top:-40px;width:260px;height:260px;border-radius:50%;background:radial-gradient(circle,rgba(232,184,75,.14),transparent 65%)}
  .profit-banner .pico{width:72px;height:72px;flex:0 0 auto;border:2px solid var(--gold);border-radius:50%;display:flex;align-items:center;justify-content:center;color:var(--gold-bright);background:rgba(200,148,42,.08);position:relative;z-index:1}
  .profit-banner .pico svg{width:34px;height:34px}
  .profit-banner .ptext{position:relative;z-index:1;flex:1;min-width:220px}
  .profit-banner .ptext .label{font-family:"JetBrains Mono",monospace;font-size:12px;letter-spacing:.14em;text-transform:uppercase;color:var(--gold);margin-bottom:6px}
  .profit-banner .ptext .amount{font-family:"Cinzel",serif;font-size:clamp(28px,4vw,42px);font-weight:900;color:var(--gold-bright);line-height:1}
  .profit-banner .ptext .sublabel{font-size:14px;color:var(--parch-dim);margin-top:6px}
  .profit-stats{display:flex;gap:24px;flex-wrap:wrap;position:relative;z-index:1}
  .profit-stat{text-align:center}
  .profit-stat .sv{font-family:"Cinzel",serif;font-weight:800;font-size:22px;color:var(--parch);display:block}
  .profit-stat .sl{font-family:"JetBrains Mono",monospace;font-size:11px;letter-spacing:.1em;text-transform:uppercase;color:var(--parch-faint);display:block;margin-top:3px}

  /* ── weird things list ───────────────────────────── */
  .weird-list{display:flex;flex-direction:column;gap:16px;margin:22px 0}
  .weird-item{display:flex;gap:16px;align-items:flex-start;background:linear-gradient(135deg,var(--panel),#1a160a);border:1px solid var(--line-soft);border-radius:6px;padding:18px 20px;transition:.2s}
  .weird-item:hover{border-color:var(--line)}
  .weird-num{width:36px;height:36px;flex:0 0 auto;border:1px solid var(--gold);border-radius:50%;display:flex;align-items:center;justify-content:center;font-family:"Cinzel",serif;font-weight:800;font-size:14px;color:var(--gold-bright);background:rgba(200,148,42,.08)}
  .weird-body .title{font-family:"Cinzel",serif;font-weight:700;font-size:15px;color:var(--parch);margin-bottom:5px}
  .weird-body p{font-size:14.5px;color:var(--parch-dim);line-height:1.6;margin:0}
  .weird-body .tag{display:inline-flex;align-items:center;gap:5px;font-family:"JetBrains Mono",monospace;font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:var(--gold);background:rgba(200,148,42,.1);border:1px solid var(--line-soft);border-radius:3px;padding:3px 9px;margin-top:8px}

  /* ── step-by-step ────────────────────────────────── */
  .steps{display:flex;flex-direction:column;gap:0;margin:22px 0;position:relative}
  .steps::before{content:"";position:absolute;left:20px;top:20px;bottom:20px;width:2px;background:linear-gradient(180deg,var(--gold),transparent)}
  .step{display:flex;gap:20px;align-items:flex-start;padding:0 0 28px 0}
  .step:last-child{padding-bottom:0}
  .step-num{width:42px;height:42px;flex:0 0 auto;border:2px solid var(--gold);border-radius:50%;display:flex;align-items:center;justify-content:center;font-family:"Cinzel",serif;font-weight:900;font-size:15px;color:var(--gold-bright);background:#1d1910;position:relative;z-index:1}
  .step-body{padding-top:8px;flex:1}
  .step-body .stitle{font-family:"Cinzel",serif;font-weight:700;font-size:16px;color:var(--parch);margin-bottom:6px}
  .step-body p{font-size:15px;color:var(--parch-dim);line-height:1.65;margin:0}
  .step-body .stip{background:rgba(200,148,42,.06);border:1px solid var(--line-soft);border-radius:4px;padding:10px 14px;margin-top:10px;font-size:13.5px;color:var(--parch-faint);display:flex;align-items:flex-start;gap:9px}
  .step-body .stip::before{content:"⚡";flex:0 0 auto;font-size:14px}

  /* ── route cards ─────────────────────────────────── */
  .routes-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px;margin:22px 0}
  .route-card{background:linear-gradient(180deg,var(--panel),#19150a);border:1px solid var(--line-soft);border-radius:6px;padding:18px 20px;transition:.2s}
  .route-card:hover{border-color:var(--line);transform:translateY(-2px)}
  .route-card .rtitle{font-family:"Cinzel",serif;font-weight:700;font-size:14px;color:var(--parch);display:flex;align-items:center;gap:8px;margin-bottom:8px}
  .route-card .rtitle .dot{width:8px;height:8px;border-radius:50%;flex:0 0 auto}
  .route-card .rtitle .dot.safe{background:var(--pos)}
  .route-card .rtitle .dot.risk{background:oklch(0.75 0.14 60)}
  .route-card .rtitle .dot.danger{background:var(--neg)}
  .route-card .rpath{font-family:"JetBrains Mono",monospace;font-size:13px;color:var(--gold-bright);margin-bottom:8px;display:flex;align-items:center;gap:6px}
  .route-card .rarrow{color:var(--parch-faint);font-size:12px}
  .route-card .rdesc{font-size:13px;color:var(--parch-faint);line-height:1.55}
  .route-card .rtags{display:flex;gap:6px;flex-wrap:wrap;margin-top:10px}
  .rtag{font-family:"JetBrains Mono",monospace;font-size:11px;letter-spacing:.06em;padding:3px 9px;border-radius:3px;border:1px solid}
  .rtag.yellow{color:oklch(0.75 0.14 60);border-color:rgba(200,160,60,.3);background:rgba(200,160,60,.06)}
  .rtag.red{color:var(--neg);border-color:rgba(200,80,60,.3);background:rgba(200,80,60,.05)}
  .rtag.fast{color:var(--pos);border-color:rgba(111,200,111,.3);background:rgba(111,200,111,.06)}

  /* ── items table ─────────────────────────────────── */
  .items-table{width:100%;border-collapse:collapse;border:1px solid var(--line-soft);border-radius:6px;overflow:hidden;margin:20px 0}
  .items-table thead th{background:#1a1710;border-bottom:1px solid var(--line);font-family:"Cinzel",serif;font-size:11px;font-weight:600;letter-spacing:.1em;text-transform:uppercase;color:var(--parch-faint);padding:11px 16px;text-align:left}
  .items-table tbody td{padding:10px 16px;border-bottom:1px solid var(--line-soft);font-size:14px;color:var(--parch-dim);vertical-align:middle}
  .items-table tbody tr:last-child td{border-bottom:0}
  .items-table tbody tr:hover{background:rgba(200,148,42,.04)}
  .items-table .t{font-family:"JetBrains Mono",monospace;font-size:12px;font-weight:700;color:var(--gold-bright);background:rgba(200,148,42,.1);border:1px solid var(--line-soft);padding:3px 8px;border-radius:3px;display:inline-block}
  .items-table .profit-high{color:var(--pos);font-family:"JetBrains Mono",monospace;font-weight:700;font-size:13px}
  .items-table .profit-mid{color:oklch(0.75 0.14 60);font-family:"JetBrains Mono",monospace;font-weight:700;font-size:13px}

  /* ── aside toc ───────────────────────────────────── */
  .toc{background:linear-gradient(180deg,var(--panel),#1a160a);border:1px solid var(--line-soft);border-radius:6px;padding:22px 20px}
  .toc-title{font-family:"Cinzel",serif;font-weight:700;font-size:13px;letter-spacing:.1em;text-transform:uppercase;color:var(--gold);margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid var(--line-soft)}
  .toc-list{list-style:none;display:flex;flex-direction:column;gap:2px}
  .toc-list a{display:flex;align-items:center;gap:9px;padding:8px 10px;font-family:"Spectral",serif;font-size:14px;color:var(--parch-dim);border-radius:3px;border-left:2px solid transparent;transition:.15s}
  .toc-list a:hover{background:rgba(200,148,42,.08);color:var(--parch);border-left-color:var(--gold)}
  .toc-list a .tn{font-family:"JetBrains Mono",monospace;font-size:11px;color:var(--parch-faint);margin-left:auto}

  /* ── profit badge ────────────────────────────────── */
  .badge-profit{display:inline-flex;align-items:center;gap:6px;font-family:"Cinzel",serif;font-size:12px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;padding:5px 13px;border-radius:20px;color:#1a1200;background:var(--gold-bright);border:1px solid var(--gold-bright)}
  .badge-risk{display:inline-flex;align-items:center;gap:6px;font-family:"Cinzel",serif;font-size:12px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;padding:5px 13px;border-radius:20px;color:var(--neg);background:rgba(200,80,60,.08);border:1px solid rgba(200,80,60,.3)}

  /* ── mount comparison ────────────────────────────── */
  .mount-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin:20px 0}
  .mount-card{background:linear-gradient(180deg,var(--panel),#19150a);border:1px solid var(--line-soft);border-radius:6px;padding:18px;text-align:center;transition:.2s}
  .mount-card:hover{border-color:var(--line)}
  .mount-card.rec{border-color:var(--gold);background:linear-gradient(180deg,#241d0a,#1a1508)}
  .mount-card .mrec{font-family:"Cinzel",serif;font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#1a1200;background:var(--gold-bright);padding:3px 9px;border-radius:20px;margin-bottom:10px;display:inline-block}
  .mount-card .mname{font-family:"Cinzel",serif;font-weight:700;font-size:16px;color:var(--parch);margin-bottom:8px}
  .mount-card .mstats{display:flex;flex-direction:column;gap:6px;margin-top:10px}
  .mount-card .mstat{display:flex;justify-content:space-between;font-size:13px;color:var(--parch-dim)}
  .mount-card .mstat b{color:var(--parch);font-family:"JetBrains Mono",monospace;font-size:13px}

  /* ── faq ─────────────────────────────────────────── */
  .faq-list{display:flex;flex-direction:column;gap:10px;margin-top:16px}
  .faq-item{border:1px solid var(--line-soft);border-radius:6px;overflow:hidden}
  .faq-q{width:100%;background:linear-gradient(135deg,var(--panel),#1a160a);border:0;text-align:left;padding:16px 20px;font-family:"Cinzel",serif;font-weight:600;font-size:14.5px;color:var(--parch);cursor:pointer;display:flex;justify-content:space-between;align-items:center;gap:12px;transition:.15s}
  .faq-q:hover{color:var(--gold-bright)}
  .faq-q .qa{width:20px;height:20px;border:1px solid var(--line);border-radius:50%;flex:0 0 auto;display:flex;align-items:center;justify-content:center;font-size:14px;color:var(--gold);transition:.2s;font-family:"JetBrains Mono",monospace;font-weight:400}
  .faq-item.open .faq-q .qa{transform:rotate(45deg)}
  .faq-a{max-height:0;overflow:hidden;transition:max-height .3s ease;background:#14120a}
  .faq-item.open .faq-a{max-height:400px}
  .faq-a p{padding:16px 20px;font-size:15px;color:var(--parch-dim);line-height:1.7;margin:0;border-top:1px solid var(--line-soft)}

  /* ── responsive ──────────────────────────────────── */
  @media(max-width:960px){
    .guide-wrap{grid-template-columns:1fr}
    .guide-aside{display:none}
  }
  @media(max-width:680px){
    .routes-grid{grid-template-columns:1fr}
    .mount-grid{grid-template-columns:1fr}
    .profit-banner{flex-direction:column;padding:24px 20px;gap:20px}
    .profit-stats{gap:16px;width:100%}
  }
</style>
@endpush

@section('content')

{{-- Page header --}}
<div class="page-head">
  <div class="page-head-inner">
    <div class="crumb">
      <a href="{{ url('/') }}">Início</a>
      <span style="margin:0 8px;opacity:.4">›</span>
      <span>Guias</span>
      <span style="margin:0 8px;opacity:.4">›</span>
      <span>Black Market Transport</span>
    </div>
    <span class="eyebrow solo">Guia de Economia</span>
    <h1>Como Fazer <span class="gold">20M+</span> por Dia<br>no Albion Online</h1>
    <p class="lead">Transporte de cidades reais para o Black Market de Caerleon é a estratégia mais lucrativa e menos explicada do jogo. Aqui você descobre como funciona, por que parece esquisito e como começar hoje mesmo.</p>
    <div style="display:flex;align-items:center;gap:12px;margin-top:20px;flex-wrap:wrap">
      <span class="badge-profit">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
        20M – 50M por dia
      </span>
      <span class="badge-risk">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        Risco PvP · Zona Vermelha
      </span>
      <span style="font-family:'JetBrains Mono',monospace;font-size:12px;color:var(--parch-faint)">Atualizado Jun 2025 · Patch 32</span>
    </div>
  </div>
</div>

{{-- Main guide --}}
<div class="guide-wrap wrap">

  {{-- Main content --}}
  <div class="guide-main">

    {{-- Profit highlight --}}
    <div class="profit-banner">
      <div class="pico">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
        </svg>
      </div>
      <div class="ptext">
        <div class="label">Potencial de Lucro Diário</div>
        <div class="amount">20M – 50M silver</div>
        <div class="sublabel">Por conta, com equipamento básico de transporte</div>
      </div>
      <div class="profit-stats">
        <div class="profit-stat">
          <span class="sv">2–4h</span>
          <span class="sl">Tempo/dia</span>
        </div>
        <div class="profit-stat">
          <span class="sv">T4+</span>
          <span class="sl">Tier mínimo</span>
        </div>
        <div class="profit-stat">
          <span class="sv">1M</span>
          <span class="sl">Capital inicial</span>
        </div>
      </div>
    </div>

    {{-- Section 1: O que é o Black Market --}}
    <div class="gsec" id="o-que-e">
      <h2 class="gsec-title">
        <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></span>
        O que é o Black Market?
      </h2>
      <p>O <strong style="color:var(--parch)">Black Market de Caerleon</strong> é um NPC único no jogo que compra itens dos jogadores para distribuir como loot de monstros em todo Albion. Quando um jogador mata uma criatura que deveria dropar uma Espada de Ferro T4, o jogo sorteia um item que foi vendido recentemente ao Black Market — essa é a lógica que move toda a economia.</p>
      <p>Isso cria uma demanda <em>constante e previsível</em> por quase todos os itens do jogo. O Black Market não negocia player-to-player: ele é um NPC com ordens de compra abertas, esperando que alguém traga itens de fora.</p>

      <div class="callout blue">
        <strong>Como funciona na prática</strong>
        O Black Market fica em Caerleon (centro do mapa). Ele exibe uma lista de buy orders — exatamente como o mercado comum, mas o comprador é o próprio NPC do jogo. Você traz itens baratos das cidades reais, vende ao NPC com lucro, e vai embora com silver no bolso.
      </div>
    </div>

    {{-- Section 2: Coisas que soam esquisitas --}}
    <div class="gsec" id="esquisito">
      <h2 class="gsec-title">
        <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><circle cx="12" cy="12" r="10"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></span>
        Coisas que Soam Esquisitas (mas fazem sentido)
      </h2>
      <p>Quem começa a pesquisar sobre Black Market transport inevitavelmente fica confuso. Aqui vão as dúvidas mais comuns — e por que na verdade fazem todo o sentido:</p>

      <div class="weird-list">
        <div class="weird-item">
          <div class="weird-num">1</div>
          <div class="weird-body">
            <div class="title">Você compra itens para <em>vender mais barato</em> do que no mercado comum?</div>
            <p>Não exatamente. Você compra em cidades baratas (Fort Sterling, Bridgewatch) onde há excesso de oferta e preço baixo, e vende em Caerleon onde a buy order do Black Market é mais alta. O lucro está no diferencial de preço entre cidades — não no fato de vender "barato".</p>
            <span class="tag">↑ Arbitragem geográfica</span>
          </div>
        </div>

        <div class="weird-item">
          <div class="weird-num">2</div>
          <div class="weird-body">
            <div class="title">Por que vender para um NPC em vez de players?</div>
            <p>O NPC do Black Market tem ordens de compra fixas e frequentemente paga acima do preço das cidades reais. Jogadores no mercado comum competem entre si e derrubam o preço — o Black Market NPC não faz isso, ele simplesmente repõe o estoque conforme a demanda de drops de mobs.</p>
            <span class="tag">↑ Demanda constante do NPC</span>
          </div>
        </div>

        <div class="weird-item">
          <div class="weird-num">3</div>
          <div class="weird-body">
            <div class="title">Como um item "comprado" vai parar como drop de mob?</div>
            <p>Esse é o design econômico mais inteligente do Albion. Cada mob tem uma tabela de loot ligada ao Black Market. Quando o mob morre, o servidor sorteia um item que foi vendido ao Black Market — aquele item some do inventário do NPC e aparece no chão como loot. O ciclo é: traders compram → vendem ao BM → players farmam → itens voltam ao mercado.</p>
            <span class="tag">↑ Economia circular</span>
          </div>
        </div>

        <div class="weird-item">
          <div class="weird-num">4</div>
          <div class="weird-body">
            <div class="title">Por que não todo mundo faz isso se é tão lucrativo?</div>
            <p>Porque exige atravessar zonas amarelas e vermelhas (risco de PvP), capital inicial para comprar os itens, e conhecimento de quais itens têm margem positiva. Muitos jogadores têm medo do PvP no percurso — o que, paradoxalmente, mantém a margem alta para quem faz.</p>
            <span class="tag">↑ Barreira = proteção de margem</span>
          </div>
        </div>

        <div class="weird-item">
          <div class="weird-num">5</div>
          <div class="weird-body">
            <div class="title">Itens de baixo tier realmente valem a pena?</div>
            <p>Sim, especialmente T4–T6. Contra o que parece intuitivo, itens de tier médio têm altíssima rotatividade no Black Market porque a maioria dos players faz conteúdo nesses tiers. O volume compensa a margem menor por unidade — e o risco de perda é menor se você for ganked.</p>
            <span class="tag">↑ Volume bate margem unitária</span>
          </div>
        </div>
      </div>
    </div>

    {{-- Section 3: Passo a passo --}}
    <div class="gsec" id="como-fazer">
      <h2 class="gsec-title">
        <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></span>
        Como Fazer: Passo a Passo
      </h2>

      <div class="steps">
        <div class="step">
          <div class="step-num">1</div>
          <div class="step-body">
            <div class="stitle">Verifique as Buy Orders do Black Market</div>
            <p>Em Caerleon, acesse o mercado e filtre pela aba <strong style="color:var(--parch)">Black Market</strong>. Você verá a lista de itens que o NPC quer comprar e o preço que paga. Anote os itens com melhor preço — especialmente T4 a T6 de armas e armaduras.</p>
            <div class="stip">Use o site albiononline2d.com ou albion-online-data.com para checar as buy orders remotamente antes de viajar.</div>
          </div>
        </div>

        <div class="step">
          <div class="step-num">2</div>
          <div class="step-body">
            <div class="stitle">Encontre a cidade com o menor preço de venda</div>
            <p>Cada item está disponível mais barato em alguma cidade real (Fort Sterling, Bridgewatch, Lymhurst, Martlock ou Thetford). Compare o preço de compra na cidade com a buy order do Black Market. A diferença entre os dois é a sua margem bruta.</p>
            <div class="stip">Margem mínima recomendada: pelo menos 8–10% acima do preço de compra para cobrir o risco e o tempo de viagem.</div>
          </div>
        </div>

        <div class="step">
          <div class="step-num">3</div>
          <div class="step-body">
            <div class="stitle">Monte sua carga e escolha o transporte</div>
            <p>Com a lista em mãos, viaje à cidade mais barata, compre os itens e encha sua montaria. Um <strong style="color:var(--parch)">Boi de Transporte</strong> leva mais volume; um <strong style="color:var(--parch)">Cavalo de Carga</strong> é mais rápido mas carrega menos. Equipe armadura de Trabalhador (não atraia atenção com gear caro).</p>
          </div>
        </div>

        <div class="step">
          <div class="step-num">4</div>
          <div class="step-body">
            <div class="stitle">Viaje até Caerleon com cuidado</div>
            <p>A rota passa por zonas amarelas e vermelhas. Mantenha o foco, evite portais de guildas inimigas e não pare em nenhum lugar suspeito. Se for ganked, corra — você tem itens baratos, então a perda é recuperável.</p>
            <div class="stip">Dica: viaje em horários de menor movimento (madrugada no horário do servidor) para reduzir encontros PvP.</div>
          </div>
        </div>

        <div class="step">
          <div class="step-num">5</div>
          <div class="step-body">
            <div class="stitle">Venda ao Black Market e repita</div>
            <p>Em Caerleon, abra o Black Market, encontre os itens e venda pelas buy orders abertas. Se a ordem encheu enquanto você viajava, você ainda pode listar no mercado comum de Caerleon — a demanda lá também costuma ser boa.</p>
            <div class="stip">Reinvista o lucro imediatamente. Com capital crescente, você faz mais viagens e escala o lucro diário rapidamente.</div>
          </div>
        </div>
      </div>

      <div class="callout green">
        <strong>Meta de lucro realista</strong>
        Um iniciante com 1–2M de capital pode fazer 3–5M de lucro por dia nas primeiras semanas. Com 10M+ de capital e 2–4 viagens diárias, chegar a 20–50M por dia é completamente alcançável após dominar as rotas e os itens mais lucrativos.
      </div>
    </div>

    {{-- Section 4: Rotas --}}
    <div class="gsec" id="rotas">
      <h2 class="gsec-title">
        <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11l19-9-9 19-2-8-8-2z"/></svg></span>
        Rotas das Cidades para Caerleon
      </h2>
      <p>Cada cidade real tem uma rota de travessia diferente para Caerleon. Escolha a rota com base no item mais barato, não apenas na cidade mais próxima geograficamente.</p>

      <div class="routes-grid">
        <div class="route-card">
          <div class="rtitle"><span class="dot risk"></span> Fort Sterling → Caerleon</div>
          <div class="rpath">Fort Sterling <span class="rarrow">→</span> Zona Amarela <span class="rarrow">→</span> Zona Vermelha <span class="rarrow">→</span> Caerleon</div>
          <div class="rdesc">Rota Norte. Boa para armas de gelo, armaduras de couro, itens de neve. Atravessa zonas de média dificuldade — risco moderado.</div>
          <div class="rtags">
            <span class="rtag yellow">Zona Amarela</span>
            <span class="rtag red">Zona Vermelha</span>
          </div>
        </div>

        <div class="route-card">
          <div class="rtitle"><span class="dot risk"></span> Bridgewatch → Caerleon</div>
          <div class="rpath">Bridgewatch <span class="rarrow">→</span> Zona Amarela <span class="rarrow">→</span> Zona Vermelha <span class="rarrow">→</span> Caerleon</div>
          <div class="rdesc">Rota Leste. Popular para armas de fogo e equipamentos de deserto. Uma das mais movimentadas — espere encontrar outros transportadores (e gankers).</div>
          <div class="rtags">
            <span class="rtag yellow">Zona Amarela</span>
            <span class="rtag red">Zona Vermelha</span>
          </div>
        </div>

        <div class="route-card">
          <div class="rtitle"><span class="dot risk"></span> Lymhurst → Caerleon</div>
          <div class="rpath">Lymhurst <span class="rarrow">→</span> Zona Amarela <span class="rarrow">→</span> Zona Vermelha <span class="rarrow">→</span> Caerleon</div>
          <div class="rdesc">Rota Sul-Oeste. Excelente para itens de natureza, cajados e armaduras de tecido. Frequentemente tem boa margem em gear de healer.</div>
          <div class="rtags">
            <span class="rtag yellow">Zona Amarela</span>
            <span class="rtag red">Zona Vermelha</span>
          </div>
        </div>

        <div class="route-card">
          <div class="rtitle"><span class="dot risk"></span> Martlock → Caerleon</div>
          <div class="rpath">Martlock <span class="rarrow">→</span> Zona Amarela <span class="rarrow">→</span> Zona Vermelha <span class="rarrow">→</span> Caerleon</div>
          <div class="rdesc">Rota Oeste. Boa para armas de arcano e itens de natureza. Menos trafegada que Bridgewatch — menor risco de gankers emboscados.</div>
          <div class="rtags">
            <span class="rtag yellow">Zona Amarela</span>
            <span class="rtag red">Zona Vermelha</span>
          </div>
        </div>

        <div class="route-card">
          <div class="rtitle"><span class="dot risk"></span> Thetford → Caerleon</div>
          <div class="rpath">Thetford <span class="rarrow">→</span> Zona Amarela <span class="rarrow">→</span> Zona Vermelha <span class="rarrow">→</span> Caerleon</div>
          <div class="rdesc">Rota Sul. Popular para armaduras de couro e armas de maldição. Travessia mais curta em número de zonas — boa para quem está começando.</div>
          <div class="rtags">
            <span class="rtag yellow">Zona Amarela</span>
            <span class="rtag red">Zona Vermelha</span>
          </div>
        </div>

        <div class="route-card" style="border-color:rgba(111,200,111,.3);background:linear-gradient(180deg,#151f14,#111509)">
          <div class="rtitle"><span class="dot safe"></span> Dica: Use Cidades de Ilha</div>
          <div class="rpath" style="color:var(--pos)">Cidade Real <span class="rarrow">→</span> Portal de Cidade <span class="rarrow">→</span> Caerleon</div>
          <div class="rdesc">Algumas rotas passam por portais de cidades que conectam direto a Caerleon, evitando zonas intermediárias. Pesquise a rota atual — os portais mudam.</div>
          <div class="rtags">
            <span class="rtag fast">Mais Rápida</span>
            <span class="rtag yellow">Varia</span>
          </div>
        </div>
      </div>
    </div>

    {{-- Section 5: Melhores itens --}}
    <div class="gsec" id="melhores-itens">
      <h2 class="gsec-title">
        <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></span>
        Melhores Itens para Transportar
      </h2>
      <p>Nem todo item tem margem positiva no Black Market. Estes categorias historicamente oferecem as melhores margens:</p>

      <table class="items-table">
        <thead>
          <tr>
            <th>Categoria</th>
            <th>Tier Ideal</th>
            <th>Margem Típica</th>
            <th>Volume/Dia</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><strong style="color:var(--parch)">Armaduras de couro</strong></td>
            <td><span class="t">T4–T6</span></td>
            <td class="profit-high">12–25%</td>
            <td>Alto</td>
          </tr>
          <tr>
            <td><strong style="color:var(--parch)">Armas de uma mão</strong></td>
            <td><span class="t">T4–T6</span></td>
            <td class="profit-high">10–22%</td>
            <td>Alto</td>
          </tr>
          <tr>
            <td><strong style="color:var(--parch)">Cajados (mágicos)</strong></td>
            <td><span class="t">T4–T5</span></td>
            <td class="profit-mid">8–18%</td>
            <td>Médio</td>
          </tr>
          <tr>
            <td><strong style="color:var(--parch)">Armaduras de tecido</strong></td>
            <td><span class="t">T4–T6</span></td>
            <td class="profit-mid">9–20%</td>
            <td>Médio</td>
          </tr>
          <tr>
            <td><strong style="color:var(--parch)">Capas e offhands</strong></td>
            <td><span class="t">T5–T7</span></td>
            <td class="profit-high">15–30%</td>
            <td>Médio</td>
          </tr>
          <tr>
            <td><strong style="color:var(--parch)">Botas e luvas</strong></td>
            <td><span class="t">T4–T6</span></td>
            <td class="profit-mid">8–15%</td>
            <td>Alto</td>
          </tr>
        </tbody>
      </table>

      <div class="callout">
        <strong>Atenção: preços mudam diariamente</strong>
        As margens acima são históricas. Verifique sempre os preços atuais antes de comprar. Uma buy order pode encher (outro trader chegou antes) e zerar sua margem — por isso checar antes de viajar é essencial.
      </div>
    </div>

    {{-- Section 6: Montar --}}
    <div class="gsec" id="montaria">
      <h2 class="gsec-title">
        <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
        Qual Montaria Usar
      </h2>
      <p>A escolha da montaria define o equilíbrio entre volume de carga, velocidade e resistência a ganking:</p>

      <div class="mount-grid">
        <div class="mount-card">
          <div class="mname">Cavalo Comum</div>
          <div class="mstats">
            <div class="mstat"><span>Carga</span><b>270</b></div>
            <div class="mstat"><span>Velocidade</span><b>Alta</b></div>
            <div class="mstat"><span>Custo</span><b>~50k silver</b></div>
            <div class="mstat"><span>Ideal</span><b>Iniciante</b></div>
          </div>
        </div>

        <div class="mount-card rec">
          <div class="mrec">Recomendado</div>
          <div class="mname">Boi de Transporte</div>
          <div class="mstats">
            <div class="mstat"><span>Carga</span><b>1620</b></div>
            <div class="mstat"><span>Velocidade</span><b>Baixa</b></div>
            <div class="mstat"><span>Custo</span><b>~200k silver</b></div>
            <div class="mstat"><span>Ideal</span><b>Máximo volume</b></div>
          </div>
        </div>

        <div class="mount-card">
          <div class="mname">Cavalo de Carga</div>
          <div class="mstats">
            <div class="mstat"><span>Carga</span><b>810</b></div>
            <div class="mstat"><span>Velocidade</span><b>Média</b></div>
            <div class="mstat"><span>Custo</span><b>~150k silver</b></div>
            <div class="mstat"><span>Ideal</span><b>Equilíbrio</b></div>
          </div>
        </div>
      </div>

      <div class="callout red">
        <strong>Armadura do transportador</strong>
        Use a armadura mais barata que tiver. Equipamento caro chama atenção e te torna um alvo mais valioso. A ideia é parecer que não vale a pena te atacar — o valor real está na carga, não no gear.
      </div>
    </div>

    {{-- FAQ --}}
    <div class="gsec" id="faq">
      <h2 class="gsec-title">
        <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><circle cx="12" cy="12" r="10"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></span>
        Dúvidas Frequentes
      </h2>

      <div class="faq-list">
        <div class="faq-item">
          <button class="faq-q">
            Preciso passar por zonas vermelhas obrigatoriamente?
            <span class="qa">+</span>
          </button>
          <div class="faq-a">
            <p>Sim, todas as rotas para Caerleon passam por pelo menos uma zona vermelha (full loot PvP). É possível minimizar o risco escolhendo horários de menor movimento, usando rotas menos populares e viajando com itens de valor moderado para que a perda não seja catastrófica caso você seja ganked.</p>
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-q">
            O Black Market esgota rapidamente?
            <span class="qa">+</span>
          </button>
          <div class="faq-a">
            <p>Sim. As buy orders do Black Market são preenchidas conforme traders vendem. Uma order popular pode ser preenchida em minutos. É fundamental checar os preços <em>logo antes</em> de comprar os itens e sair para viajar. Se chegar e a order estiver cheia, você pode listar no mercado de Caerleon e normalmente ainda terá uma margem positiva, porém menor.</p>
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-q">
            Vale fazer sozinho ou em grupo?
            <span class="qa">+</span>
          </button>
          <div class="faq-a">
            <p>Ambos têm mérito. Sozinho você fica com 100% do lucro e pode ser mais furtivo. Em grupo, divide o lucro mas aumenta drasticamente a segurança — especialmente se tiver um personagem PvP escoltando o transportador. Grupos de transport são comuns e muito eficientes para volumes maiores.</p>
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-q">
            Quanto capital inicial preciso para começar?
            <span class="qa">+</span>
          </button>
          <div class="faq-a">
            <p>É possível começar com apenas 500k–1M de silver. Com esse capital, você consegue encher um cavalo comum com itens T4 e fazer uma margem de 50–150k por viagem. O importante é não arriscar todo o capital numa única viagem no início. Reinvista gradualmente e aumente o capital conforme ganha confiança nas rotas.</p>
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-q">
            Como evitar ser ganked sempre no mesmo lugar?
            <span class="qa">+</span>
          </button>
          <div class="faq-a">
            <p>Varie as rotas. Gangues de gankers tendem a se estabelecer nos pontos de passagem obrigatória mais populares. Use mapas alternativos, passe por zonas menos trafegadas, e troque de rota com frequência. Se um local ficou perigoso, desvie e use outra cidade de origem — a margem pode ser um pouco menor, mas a segurança compensa.</p>
          </div>
        </div>
      </div>
    </div>

  </div>{{-- /guide-main --}}

  {{-- Aside: Table of Contents --}}
  <aside class="guide-aside">
    <div class="toc">
      <div class="toc-title">Neste Guia</div>
      <ul class="toc-list">
        <li><a href="#o-que-e">O que é o Black Market<span class="tn">01</span></a></li>
        <li><a href="#esquisito">Coisas que Soam Esquisitas<span class="tn">02</span></a></li>
        <li><a href="#como-fazer">Como Fazer: Passo a Passo<span class="tn">03</span></a></li>
        <li><a href="#rotas">Rotas para Caerleon<span class="tn">04</span></a></li>
        <li><a href="#melhores-itens">Melhores Itens<span class="tn">05</span></a></li>
        <li><a href="#montaria">Qual Montaria Usar<span class="tn">06</span></a></li>
        <li><a href="#faq">Dúvidas Frequentes<span class="tn">07</span></a></li>
      </ul>
    </div>

    <div style="margin-top:16px;background:linear-gradient(180deg,var(--panel),#1a160a);border:1px solid var(--line-soft);border-radius:6px;padding:20px">
      <div style="font-family:'Cinzel',serif;font-weight:700;font-size:13px;letter-spacing:.1em;text-transform:uppercase;color:var(--gold);margin-bottom:12px">Ferramentas Úteis</div>
      <div style="display:flex;flex-direction:column;gap:8px">
        <a href="{{ route('transporte.index') }}" style="display:flex;align-items:center;gap:9px;padding:10px 12px;background:rgba(200,148,42,.06);border:1px solid var(--line-soft);border-radius:4px;font-size:14px;color:var(--parch-dim);transition:.15s" onmouseover="this.style.borderColor='var(--gold)';this.style.color='var(--parch)'" onmouseout="this.style.borderColor='var(--line-soft)';this.style.color='var(--parch-dim)'">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 4h3l2.5 12h11"/><path d="M7 8h14l-1.5 7H8.5"/><circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/></svg>
          Calculadora de Transporte
        </a>
        <a href="{{ route('itens.index') }}" style="display:flex;align-items:center;gap:9px;padding:10px 12px;background:rgba(200,148,42,.06);border:1px solid var(--line-soft);border-radius:4px;font-size:14px;color:var(--parch-dim);transition:.15s" onmouseover="this.style.borderColor='var(--gold)';this.style.color='var(--parch)'" onmouseout="this.style.borderColor='var(--line-soft)';this.style.color='var(--parch-dim)'">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
          Preços de Itens
        </a>
      </div>
    </div>
  </aside>

</div>{{-- /guide-wrap --}}

@endsection

@push('scripts')
<script>
  document.querySelectorAll('.faq-q').forEach(btn => {
    btn.addEventListener('click', () => {
      const item = btn.closest('.faq-item');
      const wasOpen = item.classList.contains('open');
      document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('open'));
      if (!wasOpen) item.classList.add('open');
    });
  });
</script>
@endpush

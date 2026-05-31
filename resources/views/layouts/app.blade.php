<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'AlbionHub — Economia, Builds e Mapas de Albion Online')</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700;800;900&family=Spectral:ital,wght@0,300;0,400;0,500;0,600;1,400&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet" />
  <style>
    :root{
      --ink:#0A0A0A;
      --bg:#161410;
      --bg-2:#1c1a13;
      --panel:#26210F;
      --panel-2:#2f2913;
      --gold:#C8942A;
      --gold-bright:#E8B84B;
      --parch:#F5E6C0;
      --parch-dim:#b6a880;
      --parch-faint:#8a7f60;
      --line:rgba(200,148,42,.28);
      --line-soft:rgba(200,148,42,.14);
      --pos:oklch(0.72 0.13 150);
      --neg:oklch(0.62 0.16 28);
      --shadow:0 18px 40px -18px rgba(0,0,0,.85);
    }
    *{box-sizing:border-box;margin:0;padding:0}
    html{scroll-behavior:smooth}
    body{
      background:var(--bg);
      color:var(--parch-dim);
      font-family:"Spectral",Georgia,serif;
      font-size:17px;
      line-height:1.6;
      -webkit-font-smoothing:antialiased;
      overflow-x:hidden;
      background-image:
        radial-gradient(900px 500px at 80% -5%, rgba(200,148,42,.10), transparent 60%),
        radial-gradient(700px 500px at 0% 30%, rgba(200,148,42,.05), transparent 55%);
      background-attachment:fixed;
    }
    h1,h2,h3,h4,.cinzel{font-family:"Cinzel",serif;color:var(--parch);letter-spacing:.02em;line-height:1.15}
    a{color:inherit;text-decoration:none}
    .mono{font-family:"JetBrains Mono",monospace}
    .wrap{max-width:1240px;margin:0 auto;padding:0 28px}
    .gold{color:var(--gold-bright)}
    .eyebrow{
      font-family:"Cinzel",serif;font-weight:600;font-size:12.5px;letter-spacing:.32em;
      text-transform:uppercase;color:var(--gold);display:inline-flex;align-items:center;gap:12px;
    }
    .eyebrow::before,.eyebrow::after{content:"";width:26px;height:1px;background:linear-gradient(90deg,transparent,var(--gold))}
    .eyebrow.solo::after{display:none}
    .eyebrow.solo::before{background:var(--gold);width:18px}

    /* Buttons */
    .btn{
      font-family:"Cinzel",serif;font-weight:600;font-size:13.5px;letter-spacing:.08em;text-transform:uppercase;
      display:inline-flex;align-items:center;justify-content:center;gap:9px;
      padding:11px 22px;cursor:pointer;border:1px solid var(--line);background:transparent;color:var(--parch);
      transition:.22s ease;position:relative;border-radius:2px;
    }
    .btn:hover{border-color:var(--gold);color:var(--gold-bright);background:rgba(200,148,42,.07)}
    .btn-gold{
      background:linear-gradient(180deg,var(--gold-bright),var(--gold));
      color:#2a2007;border-color:var(--gold-bright);font-weight:700;
      box-shadow:0 8px 22px -10px rgba(232,184,75,.7), inset 0 1px 0 rgba(255,255,255,.35);
    }
    .btn-gold:hover{background:linear-gradient(180deg,#f3d27a,var(--gold-bright));color:#221a06}
    .btn-ghost{padding:10px 18px}

    /* Navbar */
    header.nav{
      position:sticky;top:0;z-index:200;
      background:rgba(13,12,8,.86);
      backdrop-filter:blur(14px) saturate(1.1);
      border-bottom:1px solid var(--line);
    }
    .nav-inner{display:flex;align-items:center;gap:14px;height:74px}
    .brand{display:flex;align-items:center;gap:13px;margin-right:8px}
    .brand .sigil{width:38px;height:38px;display:block;flex:0 0 auto}
    .brand .word{font-family:"Cinzel",serif;font-weight:900;font-size:23px;letter-spacing:.04em;color:var(--parch);line-height:1}
    .brand .word b{color:var(--gold-bright);font-weight:900}
    .brand small{display:block;font-family:"JetBrains Mono",monospace;font-size:9px;letter-spacing:.42em;color:var(--gold);margin-top:3px;text-transform:uppercase}
    .menu{display:flex;align-items:center;gap:4px;margin-left:18px}
    .menu-item{position:relative}
    .menu-trigger{
      display:flex;align-items:center;gap:7px;padding:10px 15px;cursor:pointer;
      font-family:"Cinzel",serif;font-weight:600;font-size:14px;letter-spacing:.04em;color:var(--parch-dim);
      background:none;border:0;transition:.18s;white-space:nowrap;
    }
    .menu-trigger:hover,.menu-item.open .menu-trigger{color:var(--gold-bright)}
    .menu-trigger .caret{width:9px;height:9px;border-right:1.6px solid currentColor;border-bottom:1.6px solid currentColor;transform:rotate(45deg) translateY(-2px);transition:.2s}
    .menu-item.open .menu-trigger .caret{transform:rotate(225deg) translateY(-1px)}
    .dropdown{
      position:absolute;top:calc(100% + 10px);left:0;min-width:268px;
      background:linear-gradient(180deg,#211c0f,#181508);
      border:1px solid var(--line);border-radius:4px;box-shadow:var(--shadow);
      padding:8px;opacity:0;visibility:hidden;transform:translateY(8px);transition:.2s ease;
    }
    .dropdown::before{content:"";position:absolute;top:-6px;left:24px;width:11px;height:11px;background:#211c0f;border-left:1px solid var(--line);border-top:1px solid var(--line);transform:rotate(45deg)}
    .menu-item.open .dropdown{opacity:1;visibility:visible;transform:translateY(0)}
    .dropdown a{
      display:flex;align-items:center;gap:11px;padding:10px 13px;font-family:"Spectral",serif;font-size:15px;
      color:var(--parch-dim);border-radius:3px;transition:.15s;border-left:2px solid transparent;
    }
    .dropdown a:hover{background:rgba(200,148,42,.1);color:var(--parch);border-left-color:var(--gold)}
    .dropdown a .dot{width:5px;height:5px;background:var(--gold);transform:rotate(45deg);flex:0 0 auto;opacity:.6}
    .dropdown a:hover .dot{opacity:1}
    .nav-link{padding:10px 15px;font-family:"Cinzel",serif;font-weight:600;font-size:14px;color:var(--parch-dim);transition:.18s}
    .nav-link:hover{color:var(--gold-bright)}
    .nav-right{margin-left:auto;display:flex;align-items:center;gap:10px}
    .burger{display:none;background:none;border:1px solid var(--line);width:42px;height:42px;cursor:pointer;flex-direction:column;gap:5px;align-items:center;justify-content:center;border-radius:3px}
    .burger span{width:20px;height:1.8px;background:var(--gold-bright);display:block}

    /* Hero */
    .hero{position:relative;overflow:hidden;border-bottom:1px solid var(--line)}
    .hero-bg{position:absolute;inset:0;background-position:center top;background-size:cover;background-repeat:no-repeat;filter:saturate(.85)}
    .hero-bg::after{content:"";position:absolute;inset:0;background:
      linear-gradient(180deg,rgba(13,12,8,.55) 0%,rgba(13,12,8,.72) 45%,var(--bg) 100%),
      radial-gradient(120% 90% at 50% 0%,transparent 30%,rgba(13,12,8,.6) 100%)}
    .hero-inner{position:relative;z-index:2;padding:96px 28px 84px;max-width:1240px;margin:0 auto;text-align:center}
    .hero h1{font-size:clamp(42px,7vw,86px);font-weight:900;color:var(--parch);text-shadow:0 4px 30px rgba(0,0,0,.6);letter-spacing:.02em}
    .hero h1 .em{color:var(--gold-bright);text-shadow:0 0 40px rgba(232,184,75,.45)}
    .hero .sub{font-size:clamp(17px,2.1vw,21px);color:var(--parch-dim);max-width:680px;margin:22px auto 0;font-weight:300}
    .divider-orn{display:flex;align-items:center;justify-content:center;gap:14px;margin:30px 0 4px}
    .divider-orn .line{width:90px;height:1px;background:linear-gradient(90deg,transparent,var(--gold))}
    .divider-orn .line.r{background:linear-gradient(90deg,var(--gold),transparent)}
    .divider-orn .gem{width:9px;height:9px;background:var(--gold-bright);transform:rotate(45deg);box-shadow:0 0 14px var(--gold)}

    /* Search */
    .search{max-width:720px;margin:34px auto 0;background:rgba(13,12,8,.78);border:1px solid var(--line);border-radius:5px;padding:8px;backdrop-filter:blur(6px);box-shadow:var(--shadow)}
    .search-tabs{display:flex;gap:4px;margin-bottom:8px}
    .search-tab{flex:1;padding:9px;font-family:"Cinzel",serif;font-weight:600;font-size:12.5px;letter-spacing:.1em;text-transform:uppercase;color:var(--parch-faint);background:none;border:0;cursor:pointer;border-radius:3px;transition:.18s}
    .search-tab.active{background:rgba(200,148,42,.14);color:var(--gold-bright)}
    .search-row{display:flex;gap:8px}
    .search-row .field{flex:1;display:flex;align-items:center;gap:11px;padding:0 16px;background:rgba(0,0,0,.35);border:1px solid var(--line-soft);border-radius:3px}
    .search-row input{flex:1;background:none;border:0;outline:none;color:var(--parch);font-family:"Spectral",serif;font-size:16.5px;padding:14px 0}
    .search-row input::placeholder{color:var(--parch-faint)}
    .hero-stats{display:flex;justify-content:center;gap:38px;margin-top:30px;flex-wrap:wrap}
    .hero-stats .s b{font-family:"Cinzel",serif;font-weight:800;font-size:26px;color:var(--gold-bright);display:block}
    .hero-stats .s span{font-size:12.5px;letter-spacing:.16em;text-transform:uppercase;color:var(--parch-faint);font-family:"JetBrains Mono",monospace}

    /* Sections */
    section{padding:78px 0;position:relative}
    .sec-head{margin-bottom:42px}
    .sec-head h2{font-size:clamp(28px,3.6vw,40px);font-weight:800;margin-top:14px}
    .sec-head .lead{margin-top:10px;color:var(--parch-dim);max-width:620px;font-weight:300;font-size:18px}
    .sec-head.row{display:flex;justify-content:space-between;align-items:flex-end;gap:24px;flex-wrap:wrap}

    /* Quick access */
    .quick-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:18px}
    .qcard{
      position:relative;padding:30px 26px 28px;background:linear-gradient(180deg,var(--panel),#1b1709);
      border:1px solid var(--line-soft);border-radius:5px;overflow:hidden;cursor:pointer;transition:.25s ease;
    }
    .qcard::before{content:"";position:absolute;inset:0;border:1px solid transparent;border-radius:5px;transition:.25s}
    .qcard:hover{transform:translateY(-5px);border-color:var(--line)}
    .qcard:hover::before{border-color:rgba(232,184,75,.35);box-shadow:inset 0 0 30px rgba(200,148,42,.08)}
    .qcard .ic{width:52px;height:52px;display:flex;align-items:center;justify-content:center;border:1px solid var(--line);border-radius:50%;color:var(--gold-bright);margin-bottom:20px;background:rgba(200,148,42,.06)}
    .qcard .ic svg{width:26px;height:26px}
    .qcard h3{font-size:20px;font-weight:700;margin-bottom:7px}
    .qcard p{font-size:14.5px;color:var(--parch-faint);line-height:1.5}
    .qcard .go{margin-top:18px;font-family:"Cinzel",serif;font-size:12px;font-weight:600;letter-spacing:.16em;text-transform:uppercase;color:var(--gold);display:flex;align-items:center;gap:8px;transition:.2s}
    .qcard:hover .go{gap:13px;color:var(--gold-bright)}
    .corner{position:absolute;width:13px;height:13px;border:1px solid var(--gold);opacity:.5}
    .corner.tl{top:9px;left:9px;border-right:0;border-bottom:0}
    .corner.tr{top:9px;right:9px;border-left:0;border-bottom:0}
    .corner.bl{bottom:9px;left:9px;border-right:0;border-top:0}
    .corner.br{bottom:9px;right:9px;border-left:0;border-top:0}

    /* Price table */
    .tablewrap{background:linear-gradient(180deg,#1d1a10,#15130b);border:1px solid var(--line);border-radius:6px;overflow:hidden;box-shadow:var(--shadow)}
    .table-top{display:flex;align-items:center;justify-content:space-between;padding:16px 22px;border-bottom:1px solid var(--line-soft);gap:16px;flex-wrap:wrap}
    .table-top .live{display:flex;align-items:center;gap:9px;font-family:"JetBrains Mono",monospace;font-size:12px;letter-spacing:.1em;color:var(--parch-faint);text-transform:uppercase}
    .live .pulse{width:8px;height:8px;border-radius:50%;background:var(--pos);box-shadow:0 0 0 0 rgba(120,200,120,.6);animation:pulse 2s infinite}
    @keyframes pulse{70%{box-shadow:0 0 0 8px rgba(120,200,120,0)}100%{box-shadow:0 0 0 0 rgba(120,200,120,0)}}
    .tfilter{display:flex;align-items:center;gap:11px}
    .tsearch{display:flex;align-items:center;gap:9px;background:rgba(0,0,0,.35);border:1px solid var(--line-soft);border-radius:3px;padding:0 13px}
    .tsearch input{background:none;border:0;outline:none;color:var(--parch);font-family:"Spectral",serif;font-size:14.5px;padding:9px 0;width:170px}
    .tsearch input::placeholder{color:var(--parch-faint)}
    .chip{padding:7px 14px;font-family:"Cinzel",serif;font-size:12px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--parch-faint);border:1px solid var(--line-soft);background:none;cursor:pointer;border-radius:20px;transition:.18s}
    .chip.active{color:#241b06;background:var(--gold-bright);border-color:var(--gold-bright)}
    .chip:not(.active):hover{color:var(--gold-bright);border-color:var(--line)}
    table{width:100%;border-collapse:collapse}
    thead th{font-family:"Cinzel",serif;font-size:11.5px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;color:var(--gold);text-align:left;padding:14px 22px;border-bottom:1px solid var(--line-soft)}
    thead th.r,tbody td.r{text-align:right}
    tbody td{padding:15px 22px;border-bottom:1px solid rgba(200,148,42,.07);font-size:15.5px;color:var(--parch);vertical-align:middle}
    tbody tr{transition:.15s}
    tbody tr:hover{background:rgba(200,148,42,.05)}
    tbody tr:last-child td{border-bottom:0}
    .item-cell{display:flex;align-items:center;gap:13px}
    .tier{width:36px;height:36px;flex:0 0 auto;display:flex;align-items:center;justify-content:center;font-family:"JetBrains Mono",monospace;font-weight:700;font-size:12px;color:var(--gold-bright);border:1px solid var(--line);background:rgba(200,148,42,.07);border-radius:4px}
    .item-cell .nm{font-family:"Spectral",serif;font-weight:500}
    .city{font-family:"JetBrains Mono",monospace;font-size:13px;color:var(--parch-dim);letter-spacing:.02em}
    .price{font-family:"JetBrains Mono",monospace;font-weight:500;color:var(--parch)}
    .price .ag{color:var(--gold);font-size:12px}
    .var{font-family:"JetBrains Mono",monospace;font-weight:700;display:inline-flex;align-items:center;gap:5px;justify-content:flex-end}
    .var.up{color:var(--pos)}
    .var.down{color:var(--neg)}
    .var .arr{font-size:11px}
    .badge{font-family:"Cinzel",serif;font-size:11px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;padding:5px 12px;border-radius:20px;border:1px solid var(--line)}
    .badge.craft{color:var(--gold-bright);background:rgba(200,148,42,.1)}
    .badge.refino{color:#9fc3d6;background:rgba(120,170,200,.1);border-color:rgba(120,170,200,.25)}
    .no-rows td{text-align:center;color:var(--parch-faint);padding:40px;font-style:italic}

    /* Builds */
    .builds-bg{background:linear-gradient(180deg,var(--bg),#100e09 50%,var(--bg));border-top:1px solid var(--line-soft);border-bottom:1px solid var(--line-soft)}
    .role-filter{display:flex;gap:8px;flex-wrap:wrap}
    .builds-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}
    .build{
      position:relative;background:linear-gradient(180deg,var(--panel),#19150a);border:1px solid var(--line-soft);
      border-radius:6px;overflow:hidden;transition:.25s ease;display:flex;flex-direction:column;
    }
    .build:hover{transform:translateY(-5px);border-color:var(--line);box-shadow:var(--shadow)}
    .build .art{height:158px;position:relative;overflow:hidden;border-bottom:1px solid var(--line-soft)}
    .build .art img{width:100%;height:100%;object-fit:cover;filter:saturate(.9) contrast(1.05);transition:.4s}
    .build:hover .art img{transform:scale(1.06)}
    .build .art.placeholder{background:repeating-linear-gradient(135deg,#231e10,#231e10 14px,#1d190d 14px,#1d190d 28px)}
    .build .art::after{content:"";position:absolute;inset:0;background:linear-gradient(180deg,transparent 35%,rgba(20,17,9,.85))}
    .role-tag{position:absolute;top:12px;left:12px;z-index:2;font-family:"Cinzel",serif;font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;padding:5px 12px;border-radius:3px;color:#241b06;background:var(--gold-bright)}
    .role-tag.tank{background:#9fc3d6}
    .role-tag.healer{background:oklch(0.78 0.12 150)}
    .role-tag.solo{background:#d6a99f}
    .build .body{padding:20px 22px 22px;flex:1;display:flex;flex-direction:column}
    .build h3{font-size:21px;font-weight:700;margin-bottom:6px}
    .build .content-tag{font-family:"JetBrains Mono",monospace;font-size:12px;letter-spacing:.05em;color:var(--gold);text-transform:uppercase;margin-bottom:14px}
    .build .desc{font-size:14.5px;color:var(--parch-faint);line-height:1.55;flex:1}
    .build .foot{display:flex;align-items:center;justify-content:space-between;margin-top:18px;padding-top:16px;border-top:1px solid var(--line-soft)}
    .build .cost{font-family:"JetBrains Mono",monospace}
    .build .cost b{color:var(--gold-bright);font-size:17px;font-weight:700}
    .build .cost span{display:block;font-size:10.5px;letter-spacing:.16em;text-transform:uppercase;color:var(--parch-faint);margin-top:2px}
    .build .view{font-family:"Cinzel",serif;font-size:12px;font-weight:600;letter-spacing:.1em;text-transform:uppercase;color:var(--gold);display:flex;align-items:center;gap:7px;transition:.2s}
    .build:hover .view{gap:11px;color:var(--gold-bright)}

    /* Blog */
    .blog-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:18px}
    .post{background:linear-gradient(180deg,var(--panel),#19150a);border:1px solid var(--line-soft);border-radius:6px;overflow:hidden;transition:.25s;cursor:pointer;display:flex;flex-direction:column}
    .post:hover{transform:translateY(-5px);border-color:var(--line)}
    .post .pic{height:130px;background:repeating-linear-gradient(135deg,#231e10,#231e10 14px,#1d190d 14px,#1d190d 28px);position:relative;overflow:hidden}
    .post .pic img{width:100%;height:100%;object-fit:cover;filter:saturate(.85)}
    .post .pic .ph{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-family:"JetBrains Mono",monospace;font-size:11px;letter-spacing:.12em;text-transform:uppercase;color:var(--parch-faint)}
    .post .ptag{position:absolute;top:11px;left:11px;font-family:"Cinzel",serif;font-size:10.5px;font-weight:600;letter-spacing:.1em;text-transform:uppercase;color:#241b06;background:var(--gold-bright);padding:4px 10px;border-radius:3px;z-index:2}
    .post .pbody{padding:17px 18px 20px;flex:1;display:flex;flex-direction:column}
    .post h3{font-size:18px;font-weight:600;line-height:1.3;color:var(--parch);font-family:"Cinzel",serif}
    .post:hover h3{color:var(--gold-bright)}
    .post .date{margin-top:auto;padding-top:14px;font-family:"JetBrains Mono",monospace;font-size:11.5px;color:var(--parch-faint);letter-spacing:.06em}

    /* Donation */
    .donate{position:relative;overflow:hidden;border:1px solid var(--line);border-radius:8px;
      background:linear-gradient(120deg,#241d0d,#16130b 70%);padding:52px 56px;display:flex;align-items:center;gap:40px;flex-wrap:wrap}
    .donate::before{content:"";position:absolute;right:-60px;top:-60px;width:340px;height:340px;border-radius:50%;background:radial-gradient(circle,rgba(232,184,75,.16),transparent 65%)}
    .donate .dico{width:74px;height:74px;flex:0 0 auto;border:1px solid var(--gold);border-radius:50%;display:flex;align-items:center;justify-content:center;color:var(--gold-bright);background:rgba(200,148,42,.08)}
    .donate .dico svg{width:36px;height:36px}
    .donate .dtext{flex:1;min-width:260px;position:relative;z-index:2}
    .donate h2{font-size:clamp(24px,3vw,32px);font-weight:800;margin-bottom:10px}
    .donate p{color:var(--parch-dim);font-weight:300;max-width:560px}
    .donate .dact{position:relative;z-index:2;display:flex;gap:12px;flex-wrap:wrap}

    /* Footer */
    footer{background:linear-gradient(180deg,var(--bg),#0c0b07);border-top:1px solid var(--line);padding:56px 0 30px;margin-top:20px}
    .foot-grid{display:grid;grid-template-columns:1.5fr 1fr 1fr 1fr;gap:40px;padding-bottom:40px;border-bottom:1px solid var(--line-soft)}
    .foot-brand .word{font-family:"Cinzel",serif;font-weight:900;font-size:22px;color:var(--parch)}
    .foot-brand .word b{color:var(--gold-bright)}
    .foot-brand p{margin-top:14px;font-size:14.5px;color:var(--parch-faint);max-width:300px;font-weight:300}
    .foot-col h4{font-family:"Cinzel",serif;font-size:12.5px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;color:var(--gold);margin-bottom:16px}
    .foot-col a{display:block;font-size:14.5px;color:var(--parch-dim);padding:6px 0;transition:.15s}
    .foot-col a:hover{color:var(--gold-bright)}
    .foot-bottom{display:flex;align-items:center;justify-content:space-between;padding-top:24px;gap:18px;flex-wrap:wrap}
    .foot-bottom .cr{font-family:"JetBrains Mono",monospace;font-size:12.5px;color:var(--parch-faint);letter-spacing:.03em}
    .foot-bottom .made{font-size:13.5px;color:var(--parch-dim)}
    .foot-bottom .made b{color:var(--gold-bright);font-weight:600}
    .foot-social{display:flex;align-items:center;gap:10px}
    .foot-social a{width:40px;height:40px;border:1px solid var(--line);border-radius:50%;display:flex;align-items:center;justify-content:center;color:var(--parch-dim);transition:.2s}
    .foot-social a:hover{color:var(--gold-bright);border-color:var(--gold);background:rgba(200,148,42,.08)}
    .foot-social a svg{width:19px;height:19px}

    /* Mobile nav panel */
    .mobile{display:none}

    @media(max-width:1080px){
      .menu{display:none}
      .nav-right .btn-ghost{display:none}
      .burger{display:flex}
      .blog-grid{grid-template-columns:repeat(2,1fr)}
      .quick-grid{grid-template-columns:repeat(2,1fr)}
      .builds-grid{grid-template-columns:repeat(2,1fr)}
      .foot-grid{grid-template-columns:1fr 1fr;gap:32px}
    }
    @media(max-width:680px){
      .wrap{padding:0 18px}
      .quick-grid,.builds-grid,.blog-grid{grid-template-columns:1fr}
      .hero-inner{padding:64px 18px 60px}
      .search-row{flex-direction:column}
      .donate{padding:34px 26px}
      .foot-grid{grid-template-columns:1fr;gap:26px}
      .table-top{flex-direction:column;align-items:stretch}
      .tsearch input{width:100%}
      thead th.hide,tbody td.hide{display:none}
      .hero-stats{gap:24px}
    }
    .mobile.open{display:block;position:fixed;inset:74px 0 0;z-index:190;background:rgba(13,12,8,.98);overflow-y:auto;padding:18px 22px 40px}
    .mobile .mgroup{border-bottom:1px solid var(--line-soft);padding:6px 0}
    .mobile .mgroup>div{font-family:"Cinzel",serif;font-weight:700;color:var(--gold-bright);padding:14px 4px;font-size:15px;letter-spacing:.04em}
    .mobile .mgroup a{display:block;padding:10px 16px;color:var(--parch-dim);font-size:15px}
    .mobile .mact{display:flex;gap:12px;margin-top:22px}
    .mobile .mact .btn{flex:1}

    /* Language selector */
    .lang-dropdown{min-width:210px}
    .lang-opt{display:flex !important;align-items:center;gap:10px}
    .lang-flag{font-size:17px;flex:0 0 auto;line-height:1}
    .lang-name{flex:1}
    .lang-tag{font-family:"JetBrains Mono",monospace;font-size:11px;color:var(--parch-faint);letter-spacing:.06em}
    .lang-active{color:var(--gold-bright) !important;background:rgba(200,148,42,.1) !important;border-left-color:var(--gold) !important}
    .lang-active .lang-flag,.lang-active .lang-tag{opacity:1}
    #langBtn{gap:6px;min-width:0;padding:10px 12px}
    #langBtn svg{flex:0 0 auto;opacity:.75}
    #langCode{font-family:"Cinzel",serif;font-weight:700;font-size:13px;letter-spacing:.06em}
  </style>
  @stack('styles')
</head>
<body>

  @include('layouts.partials.header')

  <main>
    @yield('content')
  </main>

  @include('layouts.partials.footer')

  <script>
    /* ── i18n engine ──────────────────────────────────────────── */
    window.I18n = (function () {
      const SUPPORTED = ['pt-BR', 'es-ES', 'en-US', 'fr-FR', 'nl-NL'];
      const FALLBACK   = 'pt-BR';
      const KEY        = 'albionhub_locale';
      let _locale = FALLBACK;
      let _tr     = {};

      function _detect() {
        const lang = (navigator.language || '').trim();
        if (SUPPORTED.includes(lang)) return lang;
        const prefix = lang.split('-')[0].toLowerCase();
        return SUPPORTED.find(s => s.split('-')[0].toLowerCase() === prefix) || FALLBACK;
      }

      function _resolve() {
        const saved = localStorage.getItem(KEY);
        return (saved && SUPPORTED.includes(saved)) ? saved : _detect();
      }

      async function _load(loc) {
        const r = await fetch('/locales/' + loc + '.json');
        if (!r.ok) throw new Error('locale ' + loc + ' failed');
        return r.json();
      }

      function t(key, vars) {
        let s = _tr[key];
        if (s === undefined) s = _tr[key.replace(/\./g, '_')] || key;
        if (vars) Object.keys(vars).forEach(k => { s = s.split('{' + k + '}').join(String(vars[k])); });
        return s;
      }

      function _applyDOM() {
        document.querySelectorAll('[data-i18n]').forEach(el => { el.textContent = t(el.dataset.i18n); });
        document.querySelectorAll('[data-i18n-html]').forEach(el => { el.innerHTML = t(el.dataset.i18nHtml); });
        document.querySelectorAll('[data-i18n-placeholder]').forEach(el => { el.placeholder = t(el.dataset.i18nPlaceholder); });
        /* copyright with {year} */
        const cr = document.getElementById('copyright');
        if (cr) cr.textContent = t('footer.copyright', { year: new Date().getFullYear() });
        /* lang button */
        const lc = document.getElementById('langCode');
        if (lc) lc.textContent = _locale.split('-')[0].toUpperCase();
        /* active option */
        document.querySelectorAll('.lang-opt').forEach(el => {
          el.classList.toggle('lang-active', el.dataset.lang === _locale);
        });
        document.documentElement.lang = _locale;
        window._locale = _locale;
        document.dispatchEvent(new CustomEvent('i18n:ready', { detail: { locale: _locale } }));
      }

      async function setLocale(newLocale, persist) {
        if (!SUPPORTED.includes(newLocale)) newLocale = FALLBACK;
        try { _tr = await _load(newLocale); }
        catch (_) {
          if (newLocale !== FALLBACK) _tr = await _load(FALLBACK);
          newLocale = FALLBACK;
        }
        _locale = newLocale;
        if (persist !== false) localStorage.setItem(KEY, newLocale);
        _applyDOM();
      }

      async function init() { await setLocale(_resolve(), false); }

      return { init, setLocale, t, get locale() { return _locale; }, SUPPORTED };
    })();

    /* utility fetch wrapper that sends Accept-Language */
    window.apiFetch = (url, opts = {}) =>
      fetch(url, { ...opts, headers: { 'Accept-Language': window._locale || 'pt-BR', ...(opts.headers || {}) } });

    /* ── Navbar dropdowns ─────────────────────────────────────── */
    const items = [...document.querySelectorAll('.menu-item')];
    items.forEach(it => {
      const trg = it.querySelector('.menu-trigger');
      trg.addEventListener('click', e => {
        e.stopPropagation();
        const wasOpen = it.classList.contains('open');
        items.forEach(o => o.classList.remove('open'));
        if (!wasOpen) it.classList.add('open');
      });
      it.addEventListener('mouseenter', () => { items.forEach(o => o.classList.remove('open')); it.classList.add('open'); });
      it.addEventListener('mouseleave', () => it.classList.remove('open'));
    });
    document.addEventListener('click', () => items.forEach(o => o.classList.remove('open')));

    /* ── Mobile menu ──────────────────────────────────────────── */
    const burger = document.getElementById('burger');
    const mobile = document.getElementById('mobile');
    burger.addEventListener('click', () => {
      mobile.classList.toggle('open');
      document.body.style.overflow = mobile.classList.contains('open') ? 'hidden' : '';
    });
    mobile.querySelectorAll('a').forEach(a => a.addEventListener('click', () => {
      mobile.classList.remove('open');
      document.body.style.overflow = '';
    }));

    /* ── Language selector clicks ─────────────────────────────── */
    document.querySelectorAll('.lang-opt').forEach(opt => {
      opt.addEventListener('click', e => {
        e.preventDefault();
        I18n.setLocale(opt.dataset.lang);
        document.getElementById('langItem').classList.remove('open');
      });
    });

    /* ── Boot ─────────────────────────────────────────────────── */
    I18n.init();
  </script>

  @stack('scripts')
</body>
</html>

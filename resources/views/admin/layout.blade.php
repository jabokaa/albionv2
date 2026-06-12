<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Admin') — AlbionHub</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700;800&family=Spectral:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet" />
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
      --sidebar:240px;
    }
    *{box-sizing:border-box;margin:0;padding:0}
    html{scroll-behavior:smooth}
    body{background:var(--bg);color:var(--parch-dim);font-family:"Spectral",Georgia,serif;font-size:15px;line-height:1.6;-webkit-font-smoothing:antialiased;min-height:100vh;display:flex;flex-direction:column}
    h1,h2,h3,h4,.cinzel{font-family:"Cinzel",serif;color:var(--parch);letter-spacing:.02em;line-height:1.15}
    a{color:inherit;text-decoration:none}
    .mono{font-family:"JetBrains Mono",monospace}

    /* Layout */
    .admin-wrap{display:flex;min-height:100vh}

    /* Sidebar */
    .sidebar{
      width:var(--sidebar);flex:0 0 var(--sidebar);
      background:linear-gradient(180deg,#1a1609,#120f07);
      border-right:1px solid var(--line);
      display:flex;flex-direction:column;
      position:sticky;top:0;height:100vh;overflow-y:auto;
    }
    .sidebar-brand{padding:24px 20px 20px;border-bottom:1px solid var(--line-soft)}
    .sidebar-brand .word{font-family:"Cinzel",serif;font-weight:900;font-size:18px;color:var(--parch)}
    .sidebar-brand .word b{color:var(--gold-bright)}
    .sidebar-brand small{display:block;font-family:"JetBrains Mono",monospace;font-size:9px;letter-spacing:.32em;color:var(--gold);margin-top:3px;text-transform:uppercase}
    .sidebar-nav{padding:14px 10px;flex:1}
    .nav-group{margin-bottom:6px}
    .nav-group-label{font-family:"Cinzel",serif;font-size:9.5px;font-weight:600;letter-spacing:.3em;text-transform:uppercase;color:var(--parch-faint);padding:10px 10px 4px}
    .nav-link{
      display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:4px;
      font-family:"Cinzel",serif;font-size:12.5px;font-weight:600;letter-spacing:.04em;
      color:var(--parch-dim);transition:.15s;border-left:2px solid transparent;
    }
    .nav-link:hover{background:rgba(200,148,42,.08);color:var(--parch);border-left-color:var(--line)}
    .nav-link.active{background:rgba(200,148,42,.12);color:var(--gold-bright);border-left-color:var(--gold)}
    .nav-link svg{width:16px;height:16px;flex:0 0 auto;opacity:.8}
    .sidebar-foot{padding:14px 10px;border-top:1px solid var(--line-soft)}
    .sidebar-foot form{display:block}
    .sidebar-foot button{
      width:100%;display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:4px;
      font-family:"Cinzel",serif;font-size:12.5px;font-weight:600;letter-spacing:.04em;
      color:var(--parch-faint);background:none;border:none;cursor:pointer;transition:.15s;
    }
    .sidebar-foot button:hover{color:var(--neg);background:rgba(200,80,60,.07)}

    /* Main content */
    .admin-main{flex:1;display:flex;flex-direction:column;min-width:0}
    .admin-topbar{
      display:flex;align-items:center;justify-content:space-between;gap:16px;
      padding:16px 30px;border-bottom:1px solid var(--line-soft);
      background:rgba(13,12,8,.6);backdrop-filter:blur(8px);
      position:sticky;top:0;z-index:10;
    }
    .admin-topbar h1{font-size:20px;font-weight:700}
    .admin-content{padding:28px 30px;flex:1}

    /* Alert banners */
    .alert{padding:12px 18px;border-radius:4px;font-size:14px;margin-bottom:20px;display:flex;align-items:center;gap:10px}
    .alert-success{background:rgba(100,180,100,.12);border:1px solid rgba(100,180,100,.3);color:oklch(0.78 0.12 150)}
    .alert-error{background:rgba(200,80,60,.12);border:1px solid rgba(200,80,60,.3);color:oklch(0.72 0.16 28)}

    /* Card */
    .card{background:linear-gradient(180deg,var(--panel),#1b1709);border:1px solid var(--line-soft);border-radius:6px;overflow:hidden;box-shadow:var(--shadow)}
    .card-head{padding:16px 22px;border-bottom:1px solid var(--line-soft);display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap}
    .card-head h2{font-size:15px;font-weight:700}
    .card-body{padding:22px}

    /* Buttons */
    .btn{
      font-family:"Cinzel",serif;font-weight:600;font-size:12.5px;letter-spacing:.08em;text-transform:uppercase;
      display:inline-flex;align-items:center;justify-content:center;gap:8px;
      padding:9px 18px;cursor:pointer;border:1px solid var(--line);background:transparent;color:var(--parch);
      transition:.2s ease;border-radius:3px;
    }
    .btn:hover{border-color:var(--gold);color:var(--gold-bright);background:rgba(200,148,42,.07)}
    .btn-gold{background:linear-gradient(180deg,var(--gold-bright),var(--gold));color:#2a2007;border-color:var(--gold-bright);font-weight:700;box-shadow:0 6px 18px -8px rgba(232,184,75,.6),inset 0 1px 0 rgba(255,255,255,.3)}
    .btn-gold:hover{background:linear-gradient(180deg,#f3d27a,var(--gold-bright));color:#221a06}
    .btn-danger{border-color:rgba(200,80,60,.5);color:oklch(0.72 0.16 28)}
    .btn-danger:hover{background:rgba(200,80,60,.1);border-color:rgba(200,80,60,.8)}
    .btn-sm{padding:6px 13px;font-size:11px}

    /* Table */
    .tablewrap{overflow-x:auto}
    table{width:100%;border-collapse:collapse}
    thead th{font-family:"Cinzel",serif;font-size:10.5px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;color:var(--gold);text-align:left;padding:12px 16px;border-bottom:1px solid var(--line-soft)}
    tbody td{padding:12px 16px;border-bottom:1px solid rgba(200,148,42,.07);font-size:14px;color:var(--parch);vertical-align:middle}
    tbody tr:hover{background:rgba(200,148,42,.04)}
    tbody tr:last-child td{border-bottom:0}
    td.actions{display:flex;gap:6px;align-items:center}

    /* Form */
    .form-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
    .form-grid.cols-1{grid-template-columns:1fr}
    .form-group{display:flex;flex-direction:column;gap:6px}
    .form-group.full{grid-column:1/-1}
    label{font-family:"Cinzel",serif;font-size:11px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;color:var(--gold)}
    input[type=text],input[type=email],input[type=password],select,textarea{
      background:rgba(0,0,0,.35);border:1px solid var(--line-soft);border-radius:3px;
      color:var(--parch);font-family:"Spectral",serif;font-size:14.5px;padding:10px 14px;
      outline:none;width:100%;transition:.18s;
    }
    input:focus,select:focus,textarea:focus{border-color:var(--gold);background:rgba(0,0,0,.5)}
    .field-error{font-size:12px;color:oklch(0.72 0.16 28);margin-top:2px}
    select option{background:#211c0f;color:var(--parch)}

    /* Pagination */
    .pagination{display:flex;gap:4px;align-items:center;flex-wrap:wrap;margin-top:20px}
    .pagination a,.pagination span{
      padding:6px 12px;font-family:"Cinzel",serif;font-size:11.5px;font-weight:600;letter-spacing:.06em;
      border:1px solid var(--line-soft);border-radius:3px;color:var(--parch-dim);transition:.15s;
    }
    .pagination a:hover{border-color:var(--gold);color:var(--gold-bright);background:rgba(200,148,42,.07)}
    .pagination span.active{background:rgba(200,148,42,.15);border-color:var(--gold);color:var(--gold-bright)}
    .pagination span.disabled{opacity:.4;pointer-events:none}

    /* Breadcrumb */
    .breadcrumb{display:flex;align-items:center;gap:8px;font-family:"JetBrains Mono",monospace;font-size:12px;color:var(--parch-faint);margin-bottom:22px}
    .breadcrumb a:hover{color:var(--gold-bright)}
    .breadcrumb .sep{opacity:.4}

    /* Search bar */
    .search-bar{display:flex;gap:8px;align-items:center}
    .search-bar .field{display:flex;align-items:center;gap:9px;background:rgba(0,0,0,.35);border:1px solid var(--line-soft);border-radius:3px;padding:0 13px}
    .search-bar input{background:none;border:0;outline:none;color:var(--parch);font-family:"Spectral",serif;font-size:14px;padding:9px 0;width:240px}
    .search-bar input::placeholder{color:var(--parch-faint)}

    /* Badge */
    .badge{font-family:"JetBrains Mono",monospace;font-size:11px;padding:3px 9px;border-radius:20px;border:1px solid var(--line-soft);color:var(--parch-faint)}
    .badge-gold{color:var(--gold-bright);border-color:rgba(200,148,42,.4);background:rgba(200,148,42,.07)}

    /* Tree indent */
    .cat-indent{color:var(--parch-faint);margin-right:4px;font-family:"JetBrains Mono",monospace;font-size:11px}

    @media(max-width:900px){
      .sidebar{display:none}
      .admin-content{padding:18px}
      .form-grid{grid-template-columns:1fr}
    }
  </style>
  @stack('styles')
</head>
<body>
<div class="admin-wrap">

  {{-- Sidebar --}}
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="word">Albion<b>Hub</b></div>
      <small>Painel Admin</small>
    </div>
    <nav class="sidebar-nav">
      <div class="nav-group">
        <div class="nav-group-label">Catálogo</div>
        <a href="{{ route('admin.categorias.index') }}"
           class="nav-link {{ request()->routeIs('admin.categorias.*') ? 'active' : '' }}">
          <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h7v5H3zM14 7h7v5h-7zM3 16h7v2H3zM14 16h7v2h-7z"/></svg>
          Categorias
        </a>
        <a href="{{ route('admin.itens.index') }}"
           class="nav-link {{ request()->routeIs('admin.itens.*') ? 'active' : '' }}">
          <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7H4a1 1 0 00-1 1v10a1 1 0 001 1h16a1 1 0 001-1V8a1 1 0 00-1-1zM16 3H8l-1 4h10l-1-4z"/></svg>
          Itens / Categorias
        </a>
      </div>
      <div class="nav-group">
        <div class="nav-group-label">Site</div>
        <a href="{{ url('/') }}" class="nav-link" target="_blank">
          <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
          Ver Site
        </a>
      </div>
    </nav>
    <div class="sidebar-foot">
      <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button type="submit">
          <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" style="width:16px;height:16px"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2h5a2 2 0 012 2v1"/></svg>
          Sair
        </button>
      </form>
    </div>
  </aside>

  {{-- Main --}}
  <div class="admin-main">
    <div class="admin-topbar">
      <h1>@yield('page-title', 'Admin')</h1>
      <span style="font-family:'JetBrains Mono',monospace;font-size:12px;color:var(--parch-faint)">
        {{ auth()->user()->name }}
      </span>
    </div>
    <div class="admin-content">
      @if(session('success'))
        <div class="alert alert-success">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:18px;height:18px;flex:0 0 auto"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
          {{ session('success') }}
        </div>
      @endif
      @if(session('error'))
        <div class="alert alert-error">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:18px;height:18px;flex:0 0 auto"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
          {{ session('error') }}
        </div>
      @endif
      @yield('content')
    </div>
  </div>

</div>
@stack('scripts')
</body>
</html>

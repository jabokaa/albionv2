<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Login — AlbionHub</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700;800;900&family=Spectral:wght@300;400;500&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
  <style>
    :root{
      --bg:#161410;--panel:#26210F;--gold:#C8942A;--gold-bright:#E8B84B;
      --parch:#F5E6C0;--parch-dim:#b6a880;--parch-faint:#8a7f60;
      --line:rgba(200,148,42,.28);--line-soft:rgba(200,148,42,.14);
      --neg:oklch(0.62 0.16 28);
    }
    *{box-sizing:border-box;margin:0;padding:0}
    body{
      background:var(--bg);color:var(--parch-dim);font-family:"Spectral",Georgia,serif;
      min-height:100vh;display:flex;align-items:center;justify-content:center;
      background-image:radial-gradient(900px 500px at 50% 0%,rgba(200,148,42,.08),transparent 60%);
    }
    .login-box{width:100%;max-width:400px;padding:20px}
    .brand{text-align:center;margin-bottom:36px}
    .brand .word{font-family:"Cinzel",serif;font-weight:900;font-size:28px;color:var(--parch)}
    .brand .word b{color:var(--gold-bright)}
    .brand small{display:block;font-family:"JetBrains Mono",monospace;font-size:10px;letter-spacing:.4em;color:var(--gold);margin-top:5px;text-transform:uppercase}
    .card{background:linear-gradient(180deg,var(--panel),#1b1709);border:1px solid var(--line-soft);border-radius:8px;padding:32px;box-shadow:0 24px 50px -20px rgba(0,0,0,.9)}
    h2{font-family:"Cinzel",serif;font-size:17px;font-weight:700;color:var(--parch);margin-bottom:24px;text-align:center}
    .form-group{display:flex;flex-direction:column;gap:7px;margin-bottom:16px}
    label{font-family:"Cinzel",serif;font-size:11px;font-weight:600;letter-spacing:.18em;text-transform:uppercase;color:var(--gold)}
    input{
      background:rgba(0,0,0,.4);border:1px solid var(--line-soft);border-radius:3px;
      color:var(--parch);font-family:"Spectral",serif;font-size:15px;padding:11px 14px;
      outline:none;width:100%;transition:.18s;
    }
    input:focus{border-color:var(--gold);background:rgba(0,0,0,.55)}
    .field-error{font-size:12.5px;color:var(--neg)}
    .remember{display:flex;align-items:center;gap:8px;font-size:13.5px;color:var(--parch-faint);margin-bottom:22px;cursor:pointer}
    .remember input{width:auto;accent-color:var(--gold-bright)}
    .btn-login{
      width:100%;font-family:"Cinzel",serif;font-weight:700;font-size:14px;letter-spacing:.1em;
      text-transform:uppercase;padding:13px;cursor:pointer;border-radius:3px;
      background:linear-gradient(180deg,var(--gold-bright),var(--gold));color:#2a2007;
      border:1px solid var(--gold-bright);box-shadow:0 8px 22px -10px rgba(232,184,75,.7),inset 0 1px 0 rgba(255,255,255,.3);
      transition:.2s;
    }
    .btn-login:hover{background:linear-gradient(180deg,#f3d27a,var(--gold-bright));color:#221a06}
    .alert-error{background:rgba(200,80,60,.1);border:1px solid rgba(200,80,60,.35);border-radius:4px;padding:11px 14px;font-size:13.5px;color:var(--neg);margin-bottom:20px}
    .back{text-align:center;margin-top:20px;font-size:13px;color:var(--parch-faint)}
    .back a:hover{color:var(--gold-bright)}
  </style>
</head>
<body>
  <div class="login-box">
    <div class="brand">
      <div class="word">Albion<b>Hub</b></div>
      <small data-i18n="admin.login.area">Área Administrativa</small>
    </div>
    <div class="card">
      <h2 data-i18n="admin.login.title">Acesso Restrito</h2>

      @if($errors->has('email'))
        <div class="alert-error">{{ $errors->first('email') }}</div>
      @endif

      <form method="POST" action="{{ route('admin.login.post') }}">
        @csrf
        <div class="form-group">
          <label for="email" data-i18n="admin.login.email">E-mail</label>
          <input type="email" id="email" name="email" value="{{ old('email') }}" autocomplete="email" autofocus required />
        </div>
        <div class="form-group">
          <label for="password" data-i18n="admin.login.password">Senha</label>
          <input type="password" id="password" name="password" autocomplete="current-password" required />
        </div>
        <label class="remember">
          <input type="checkbox" name="remember" value="1" />
          <span data-i18n="admin.login.remember">Manter conectado</span>
        </label>
        <button type="submit" class="btn-login" data-i18n="admin.login.btn">Entrar</button>
      </form>
    </div>
    <div class="back"><a href="{{ url('/') }}" data-i18n="admin.login.back">← Voltar ao site</a></div>
  </div>

  <script>
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
      function t(key) { return _tr[key] || key; }
      function _applyDOM() {
        document.querySelectorAll('[data-i18n]').forEach(el => { el.textContent = t(el.dataset.i18n); });
        document.querySelectorAll('[data-i18n-placeholder]').forEach(el => { el.placeholder = t(el.dataset.i18nPlaceholder); });
        document.documentElement.lang = _locale;
      }
      async function init() {
        const loc = _resolve();
        try { _tr = await _load(loc); } catch (_) { try { _tr = await _load(FALLBACK); } catch(__) {} }
        _locale = loc;
        _applyDOM();
      }
      return { init };
    })();
    I18n.init();
  </script>
</body>
</html>

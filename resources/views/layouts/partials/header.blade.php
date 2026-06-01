<header class="nav">
  <div class="wrap nav-inner">
    <a href="{{ url('/') }}" class="brand" aria-label="AlbionHub — início">
      <svg class="sigil" viewBox="0 0 40 40" fill="none" aria-hidden="true">
        <path d="M20 2 L25 13 L20 11 L15 13 Z" fill="#E8B84B"/>
        <rect x="18.4" y="11" width="3.2" height="20" fill="#C8942A"/>
        <path d="M14 31 L26 31 L20 39 Z" fill="#E8B84B"/>
        <circle cx="20" cy="20" r="18.5" stroke="#C8942A" stroke-opacity="0.5"/>
        <circle cx="6" cy="20" r="1.8" fill="#C8942A"/>
        <circle cx="34" cy="20" r="1.8" fill="#C8942A"/>
      </svg>
      <span class="word">Albion<b>Hub</b><small data-i18n="brand.tagline">Companion</small></span>
    </a>

    <nav class="menu" id="menu">
      <a href="{{ route('itens.index') }}" class="nav-link{{ request()->routeIs('itens.*') ? ' active' : '' }}" data-i18n="nav.economy.items">Itens / Preços</a>
      <a href="{{ route('transporte.index') }}" class="nav-link{{ request()->routeIs('transporte.*') ? ' active' : '' }}" data-i18n="nav.economy.transport">Transporte</a>
      <a href="{{ route('crafting.index') }}" class="nav-link{{ request()->routeIs('crafting.*') ? ' active' : '' }}" data-i18n="nav.economy.craft">Calculadora de Craft</a>
      <a href="{{ route('guias.blackmarket') }}" class="nav-link{{ request()->routeIs('guias.*') ? ' active' : '' }}" style="{{ request()->routeIs('guias.*') ? 'color:var(--gold-bright)' : '' }}">Guias</a>
    </nav>

    <div class="nav-right">
      {{-- Language selector --}}
      <div class="menu-item" id="langItem">
        <button class="menu-trigger" id="langBtn" aria-label="Selecionar idioma">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
          </svg>
          <span id="langCode">PT</span>
          <i class="caret"></i>
        </button>
        <div class="dropdown lang-dropdown">
          <a href="#" class="lang-opt" data-lang="pt-BR">
            <span class="lang-flag">🇧🇷</span>
            <span class="lang-name">Português</span>
            <span class="lang-tag">pt-BR</span>
          </a>
          <a href="#" class="lang-opt" data-lang="es-ES">
            <span class="lang-flag">🇪🇸</span>
            <span class="lang-name">Español</span>
            <span class="lang-tag">es-ES</span>
          </a>
          <a href="#" class="lang-opt" data-lang="en-US">
            <span class="lang-flag">🇬🇧</span>
            <span class="lang-name">English</span>
            <span class="lang-tag">en-US</span>
          </a>
          <a href="#" class="lang-opt" data-lang="fr-FR">
            <span class="lang-flag">🇫🇷</span>
            <span class="lang-name">Français</span>
            <span class="lang-tag">fr-FR</span>
          </a>
          <a href="#" class="lang-opt" data-lang="nl-NL">
            <span class="lang-flag">🇳🇱</span>
            <span class="lang-name">Nederlands</span>
            <span class="lang-tag">nl-NL</span>
          </a>
        </div>
      </div>

      <button class="burger" id="burger" aria-label="Menu"><span></span><span></span><span></span></button>
    </div>
  </div>
</header>

{{-- Mobile panel --}}
<div class="mobile" id="mobile">
  <div class="mgroup">
    <a href="{{ route('itens.index') }}" data-i18n="nav.economy.items">Itens / Preços</a>
    <a href="{{ route('transporte.index') }}" data-i18n="nav.economy.transport">Transporte</a>
    <a href="{{ route('crafting.index') }}" data-i18n="nav.economy.craft">Calculadora de Craft</a>
    <a href="{{ route('guias.blackmarket') }}">Guias</a>
  </div>
</div>

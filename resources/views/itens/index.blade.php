<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Itens - Albion Online</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>
            *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
            body { font-family: ui-sans-serif, system-ui, sans-serif; background: #0a0a0a; color: #ededec; min-height: 100vh; }
            a { color: inherit; text-decoration: none; }
        </style>
    @endif
    <style>
        [data-lang] { display: none; }
        [data-lang].active { display: block; }

        .lang-btn {
            padding: 0.375rem 0.875rem;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            border: 1px solid #3e3e3a;
            background: transparent;
            color: #a1a09a;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .lang-btn:first-child { border-radius: 6px 0 0 6px; }
        .lang-btn:last-child  { border-radius: 0 6px 6px 0; }
        .lang-btn:not(:first-child) { border-left: none; }
        .lang-btn.active {
            background: #f8b803;
            border-color: #f8b803;
            color: #1b1b18;
        }
        .lang-btn:not(.active):hover {
            border-color: #62605b;
            color: #ededec;
        }

        .card {
            background: #161615;
            border: 1px solid #2a2a27;
            border-radius: 10px;
            overflow: hidden;
            transition: border-color 0.15s ease, transform 0.15s ease;
        }
        .card:hover {
            border-color: #3e3e3a;
            transform: translateY(-2px);
        }
        .card-img {
            width: 100%;
            aspect-ratio: 1;
            object-fit: contain;
            background: #1d1d1a;
            padding: 0.75rem;
        }
        .card-body {
            padding: 0.625rem 0.75rem 0.75rem;
        }
        .card-name {
            font-size: 0.8rem;
            color: #ededec;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            min-height: 1.2em;
        }
        .card-id {
            font-size: 0.65rem;
            color: #62605b;
            margin-top: 0.25rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .enchant-badge {
            display: inline-block;
            font-size: 0.6rem;
            font-weight: 700;
            padding: 1px 5px;
            border-radius: 3px;
            margin-left: 4px;
            vertical-align: middle;
        }
        .enchant-1 { background: #2d5a27; color: #7ec671; }
        .enchant-2 { background: #1a3a6e; color: #6fa3e0; }
        .enchant-3 { background: #5a1a6e; color: #c97fe0; }
        .enchant-4 { background: #6e3a00; color: #e09f3e; }

        .search-input {
            background: #161615;
            border: 1px solid #3e3e3a;
            border-radius: 6px;
            color: #ededec;
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
            width: 100%;
            max-width: 380px;
            outline: none;
            transition: border-color 0.15s ease;
        }
        .search-input::placeholder { color: #62605b; }
        .search-input:focus { border-color: #f8b803; }

        .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2rem;
            height: 2rem;
            padding: 0 0.5rem;
            font-size: 0.8rem;
            border: 1px solid #3e3e3a;
            border-radius: 5px;
            color: #a1a09a;
            transition: all 0.15s ease;
        }
        .page-link:hover { border-color: #62605b; color: #ededec; }
        .page-link.current { background: #f8b803; border-color: #f8b803; color: #1b1b18; font-weight: 700; }
        .page-link.disabled { opacity: 0.3; cursor: default; pointer-events: none; }
    </style>
</head>
<body>
    <div style="max-width: 1400px; margin: 0 auto; padding: 1.5rem 1.25rem;">

        {{-- Header --}}
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
            <h1 style="font-size: 1.25rem; font-weight: 700; color: #f8b803;">
                Albion Online
                <span style="font-weight: 400; color: #706f6c; font-size: 0.9rem; margin-left: 0.5rem;">Itens</span>
            </h1>

            {{-- Language toggle --}}
            <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                <div style="display: flex;">
                    <button class="lang-btn active" data-lang-select="pt">PT</button>
                    <button class="lang-btn" data-lang-select="en">EN</button>
                    <button class="lang-btn" data-lang-select="es">ES</button>
                </div>

                {{-- Search --}}
                <form method="GET" action="{{ route('itens.index') }}" style="display: flex; gap: 0.5rem;">
                    <input
                        type="text"
                        name="busca"
                        class="search-input"
                        placeholder="Buscar item..."
                        value="{{ $busca }}"
                        autocomplete="off"
                    >
                </form>
            </div>
        </div>

        {{-- Count --}}
        <p style="font-size: 0.8rem; color: #62605b; margin-bottom: 1.25rem;">
            {{ number_format($itens->total(), 0, ',', '.') }} itens encontrados
            @if($busca) &nbsp;·&nbsp; filtro: <em style="color: #a1a09a;">"{{ $busca }}"</em> @endif
        </p>

        {{-- Grid --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 0.75rem;">
            @forelse ($itens as $item)
                <div class="card">
                    <img
                        class="card-img"
                        src="{{ $item->imagem_url ?? 'https://render.albiononline.com/v1/item/' . $item->id_externo . '.png' }}"
                        alt="{{ $item->ingles ?? $item->id_externo }}"
                        loading="lazy"
                        onerror="this.style.opacity='0.2'"
                    >
                    <div class="card-body">
                        <div class="card-name">
                            <span data-lang="pt" class="active">{{ $item->portugues ?: ($item->ingles ?: $item->id_externo) }}</span>
                            <span data-lang="en">{{ $item->ingles ?: $item->id_externo }}</span>
                            <span data-lang="es">{{ $item->espanhol ?: ($item->ingles ?: $item->id_externo) }}</span>
                            @if ($item->encantamento > 0)
                                <span class="enchant-badge enchant-{{ $item->encantamento }}">@{{ $item->encantamento }}</span>
                            @endif
                        </div>
                        <div class="card-id">{{ $item->id_externo }}</div>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 0; color: #62605b;">
                    Nenhum item encontrado.
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if ($itens->hasPages())
            <div style="display: flex; flex-wrap: wrap; gap: 0.375rem; margin-top: 2rem; justify-content: center;">
                {{-- Previous --}}
                @if ($itens->onFirstPage())
                    <span class="page-link disabled">&lsaquo;</span>
                @else
                    <a class="page-link" href="{{ $itens->previousPageUrl() }}">&lsaquo;</a>
                @endif

                {{-- Page numbers --}}
                @foreach ($itens->getUrlRange(max(1, $itens->currentPage() - 3), min($itens->lastPage(), $itens->currentPage() + 3)) as $page => $url)
                    @if ($page == $itens->currentPage())
                        <span class="page-link current">{{ $page }}</span>
                    @else
                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach

                {{-- Next --}}
                @if ($itens->hasMorePages())
                    <a class="page-link" href="{{ $itens->nextPageUrl() }}">&rsaquo;</a>
                @else
                    <span class="page-link disabled">&rsaquo;</span>
                @endif
            </div>
            <p style="text-align: center; font-size: 0.75rem; color: #62605b; margin-top: 0.75rem;">
                Página {{ $itens->currentPage() }} de {{ $itens->lastPage() }}
            </p>
        @endif

    </div>

    <script>
        (function () {
            const STORAGE_KEY = 'albion_lang';
            const buttons = document.querySelectorAll('[data-lang-select]');
            const spans = document.querySelectorAll('[data-lang]');

            function setLang(lang) {
                buttons.forEach(btn => btn.classList.toggle('active', btn.dataset.langSelect === lang));
                spans.forEach(span => span.classList.toggle('active', span.dataset.lang === lang));
                localStorage.setItem(STORAGE_KEY, lang);
            }

            buttons.forEach(btn => btn.addEventListener('click', () => setLang(btn.dataset.langSelect)));

            const saved = localStorage.getItem(STORAGE_KEY);
            if (saved && ['pt', 'en', 'es'].includes(saved)) {
                setLang(saved);
            }
        })();
    </script>
</body>
</html>

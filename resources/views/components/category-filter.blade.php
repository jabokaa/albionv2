@props([
    'tree'        => [],
    'selectedId'  => null,
    'inputName'   => 'categoria',
    'autosubmit'  => false,
])

@php
$uid = 'catmenu_' . str_replace('-', '', $inputName);

// Resolve selected label
$selectedLabel    = null;
$selectedLabelEn  = null;
$selectedLabelEs  = null;
$selectedLabelFr  = null;
if ($selectedId) {
    foreach ($tree as $avo) {
        if ((string) $avo->id === (string) $selectedId) {
            $selectedLabel   = $avo->portugues ?? $avo->ingles;
            $selectedLabelEn = $avo->ingles;
            $selectedLabelEs = $avo->espanhol;
            $selectedLabelFr = $avo->frances;
            break;
        }
        foreach ($avo->filhos as $pai) {
            if ((string) $pai->id === (string) $selectedId) {
                $selectedLabel   = $pai->portugues ?? $pai->ingles;
                $selectedLabelEn = $pai->ingles;
                $selectedLabelEs = $pai->espanhol;
                $selectedLabelFr = $pai->frances;
                break 2;
            }
            foreach ($pai->filhos as $filho) {
                if ((string) $filho->id === (string) $selectedId) {
                    $selectedLabel   = $filho->portugues ?? $filho->ingles;
                    $selectedLabelEn = $filho->ingles;
                    $selectedLabelEs = $filho->espanhol;
                    $selectedLabelFr = $filho->frances;
                    break 3;
                }
            }
        }
    }
}
@endphp

<style>
.cf-wrap{position:relative;display:inline-block;min-width:200px;width:100%}
.cf-trigger{width:100%;height:36px;display:flex;align-items:center;justify-content:space-between;gap:8px;padding:0 28px 0 10px;background:rgba(0,0,0,.38) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%23C8942A'/%3E%3C/svg%3E") no-repeat right 10px center;border:1px solid var(--line-soft);border-radius:3px;color:var(--parch);font-family:"Cinzel",serif;font-size:11px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;cursor:pointer;transition:.18s;text-align:left;outline:none;appearance:none;-webkit-appearance:none}
.cf-trigger:hover,.cf-trigger:focus,.cf-wrap.open .cf-trigger{border-color:var(--gold);box-shadow:0 0 0 1px rgba(232,184,75,.12)}
.cf-trigger-label{flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.cf-dropdown{display:none;position:absolute;top:calc(100% + 4px);left:0;min-width:220px;background:#1a1710;border:1px solid var(--line);border-radius:4px;z-index:300;box-shadow:0 8px 24px rgba(0,0,0,.6)}
.cf-wrap.open .cf-dropdown{display:block}
.cf-item{position:relative;padding:8px 28px 8px 12px;font-family:"Cinzel",serif;font-size:11px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--parch-dim);cursor:pointer;transition:.15s;white-space:nowrap;display:flex;align-items:center;justify-content:space-between;gap:6px;border-bottom:1px solid rgba(255,255,255,.04)}
.cf-item:last-child{border-bottom:0}
.cf-item:hover{background:rgba(232,184,75,.08);color:var(--gold-bright)}
.cf-item.cf-active{color:var(--gold-bright);background:rgba(232,184,75,.06)}
.cf-item.cf-all{color:var(--parch-faint);font-style:italic;font-size:10px;letter-spacing:.05em;border-bottom:1px solid var(--line-soft)}
.cf-item.cf-all:hover{color:var(--parch)}
.cf-arrow{font-size:13px;opacity:.55;flex:0 0 auto;line-height:1}
.cf-sub{display:none;position:absolute;left:calc(100% + 2px);top:-1px;min-width:220px;background:#1e1b10;border:1px solid var(--line);border-radius:4px;z-index:301;box-shadow:6px 6px 20px rgba(0,0,0,.5)}
.cf-item:hover>.cf-sub{display:block}
.cf-sub .cf-item{padding-left:14px}
.cf-sub .cf-sub{left:calc(100% + 2px);top:-1px;background:#22200e;z-index:302}
@media(max-width:900px){
  .cf-sub{left:0;top:calc(100% + 2px);position:relative;background:#1e1b10;border-left:2px solid var(--gold);margin-left:8px;border-radius:0 0 4px 4px;box-shadow:none;display:none}
  .cf-item.cf-open>.cf-sub{display:block}
  .cf-item:hover>.cf-sub{display:none}
  .cf-item.cf-open>.cf-sub{display:block}
}
</style>

<div class="cf-wrap" id="{{ $uid }}">
  <input type="hidden" name="{{ $inputName }}" id="{{ $uid }}_val" value="{{ $selectedId ?? '' }}">

  <button type="button" class="cf-trigger" id="{{ $uid }}_btn"
          aria-haspopup="true" aria-expanded="false">
    <span class="cf-trigger-label"
          id="{{ $uid }}_lbl"
          @if($selectedLabelEn) data-name-pt="{{ $selectedLabel }}"
          data-name-en="{{ $selectedLabelEn }}"
          data-name-es="{{ $selectedLabelEs }}"
          data-name-fr="{{ $selectedLabelFr }}" @endif>
      {{ $selectedLabel ?? __('Todos') }}
    </span>
  </button>

  <div class="cf-dropdown" id="{{ $uid }}_drop" role="menu">
    {{-- Opção "Todos" --}}
    <div class="cf-item cf-all {{ !$selectedId ? 'cf-active' : '' }}"
         data-val="" data-label-pt="Todos" data-label-en="All" data-label-es="Todos" data-label-fr="Tous"
         data-i18n="items.filter.all">
      Todos
    </div>

    @foreach($tree as $avo)
      @php $avoActive = (string)($selectedId ?? '') === (string)$avo->id; @endphp
      <div class="cf-item {{ $avoActive ? 'cf-active' : '' }}"
           data-val="{{ $avo->id }}"
           data-label-pt="{{ $avo->portugues }}"
           data-label-en="{{ $avo->ingles }}"
           data-label-es="{{ $avo->espanhol }}"
           data-label-fr="{{ $avo->frances }}">
        <span>{{ $avo->portugues ?? $avo->ingles }}</span>
        @if($avo->filhos->isNotEmpty())
          <span class="cf-arrow">›</span>
          <div class="cf-sub" role="menu">
            {{-- Opção do avó como "todos dentro" --}}
            <div class="cf-item {{ $avoActive ? 'cf-active' : '' }}"
                 data-val="{{ $avo->id }}"
                 data-label-pt="{{ $avo->portugues }}"
                 data-label-en="{{ $avo->ingles }}"
                 data-label-es="{{ $avo->espanhol }}"
                 data-label-fr="{{ $avo->frances }}"
                 style="font-size:10px;opacity:.7;font-style:italic">
              Todos em {{ $avo->portugues ?? $avo->ingles }}
            </div>
            @foreach($avo->filhos as $pai)
              @php $paiActive = (string)($selectedId ?? '') === (string)$pai->id; @endphp
              <div class="cf-item {{ $paiActive ? 'cf-active' : '' }}"
                   data-val="{{ $pai->id }}"
                   data-label-pt="{{ $pai->portugues }}"
                   data-label-en="{{ $pai->ingles }}"
                   data-label-es="{{ $pai->espanhol }}"
                   data-label-fr="{{ $pai->frances }}">
                <span>{{ $pai->portugues ?? $pai->ingles }}</span>
                @if($pai->filhos->isNotEmpty())
                  <span class="cf-arrow">›</span>
                  <div class="cf-sub" role="menu">
                    {{-- Opção do pai como "todos dentro" --}}
                    <div class="cf-item {{ $paiActive ? 'cf-active' : '' }}"
                         data-val="{{ $pai->id }}"
                         data-label-pt="{{ $pai->portugues }}"
                         data-label-en="{{ $pai->ingles }}"
                         data-label-es="{{ $pai->espanhol }}"
                         data-label-fr="{{ $pai->frances }}"
                         style="font-size:10px;opacity:.7;font-style:italic">
                      Todos em {{ $pai->portugues ?? $pai->ingles }}
                    </div>
                    @foreach($pai->filhos as $filho)
                      @php $filhoActive = (string)($selectedId ?? '') === (string)$filho->id; @endphp
                      <div class="cf-item {{ $filhoActive ? 'cf-active' : '' }}"
                           data-val="{{ $filho->id }}"
                           data-label-pt="{{ $filho->portugues }}"
                           data-label-en="{{ $filho->ingles }}"
                           data-label-es="{{ $filho->espanhol }}"
                           data-label-fr="{{ $filho->frances }}">
                        <span>{{ $filho->portugues ?? $filho->ingles }}</span>
                      </div>
                    @endforeach
                  </div>
                @endif
              </div>
            @endforeach
          </div>
        @endif
      </div>
    @endforeach
  </div>
</div>

<script>
(function(){
  const uid      = {!! json_encode($uid) !!};
  const autoSub  = {{ $autosubmit ? 'true' : 'false' }};
  const wrap     = document.getElementById(uid);
  const btn      = document.getElementById(uid + '_btn');
  const lbl      = document.getElementById(uid + '_lbl');
  const drop     = document.getElementById(uid + '_drop');
  const valInput = document.getElementById(uid + '_val');
  if (!wrap || !btn || !drop || !valInput) return;

  const LANG_COL = {
    'pt-BR':'Pt','pt':'Pt','en-US':'En','en':'En',
    'es-ES':'Es','es':'Es','fr-FR':'Fr','fr':'Fr','nl-NL':'En','nl':'En',
  };

  function currentLang() {
    const l = document.documentElement.lang || navigator.language || 'pt';
    return LANG_COL[l] || LANG_COL[l.split('-')[0]] || 'Pt';
  }

  function itemLabel(el, K) {
    return el.dataset['label' + K] || el.dataset.labelEn || el.textContent.trim();
  }

  function select(el) {
    const val = el.dataset.val ?? '';
    const K   = currentLang();
    const label = val === '' ? (el.dataset['i18n'] ? 'Todos' : itemLabel(el, K)) : itemLabel(el, K);

    valInput.value = val;
    lbl.textContent = label;
    // carry i18n attrs so locale switcher can update
    if (val !== '') {
      lbl.dataset.namePt = el.dataset.labelPt || '';
      lbl.dataset.nameEn = el.dataset.labelEn || '';
      lbl.dataset.nameEs = el.dataset.labelEs || '';
      lbl.dataset.nameFr = el.dataset.labelFr || '';
    } else {
      delete lbl.dataset.namePt;
      delete lbl.dataset.nameEn;
      delete lbl.dataset.nameEs;
      delete lbl.dataset.nameFr;
    }

    close();

    if (autoSub) {
      const form = wrap.closest('form');
      if (form) form.submit();
    }
  }

  function open()  { wrap.classList.add('open'); btn.setAttribute('aria-expanded','true'); }
  function close() { wrap.classList.remove('open'); btn.setAttribute('aria-expanded','false'); }
  function toggle(){ wrap.classList.contains('open') ? close() : open(); }

  btn.addEventListener('click', e => { e.stopPropagation(); toggle(); });

  drop.addEventListener('click', e => {
    e.stopPropagation();
    const item = e.target.closest('.cf-item');
    if (!item) return;
    const sub = item.querySelector(':scope > .cf-sub');
    if (sub && window.innerWidth <= 900) {
      item.classList.toggle('cf-open');
      return;
    }
    select(item);
  });

  document.addEventListener('click', close);

  // Apply locale to trigger label
  document.addEventListener('i18n:ready', e => {
    const locale = e.detail.locale;
    const K = LANG_COL[locale] || LANG_COL[locale.split('-')[0]] || 'Pt';
    if (lbl.dataset['name' + K]) {
      lbl.textContent = lbl.dataset['name' + K];
    }
  });
})();
</script>

<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>{{ $title ?? 'HCBP Area 3 Apps' }}</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" type="image/webp" href="{{ asset('images/logo_pln.webp') }}"/>
    <link
      href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
      rel="stylesheet"
    />
    <style>
      body {
        font-family: "Plus Jakarta Sans", sans-serif;
        background-color: #f8fafc;
        color: #0a192f;
      }
      .material-symbols-outlined {
        font-variation-settings:
          "FILL" 0,
          "wght" 400,
          "GRAD" 0,
          "opsz" 24;
      }
      .material-symbols-outlined.filled {
        font-variation-settings: "FILL" 1;
      }
      ::-webkit-scrollbar {
        width: 6px;
      }
      ::-webkit-scrollbar-track {
        background: transparent;
      }
      ::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
      }
      ::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
      }
      table {
        border-collapse: collapse;
      }
      th,
      td {
        border: 1px solid #cbd5e1;
      }
      table.tbl {
        border-collapse: collapse;
      }
      table.tbl th,
      table.tbl td {
        border: 0;
        border-bottom: 1px solid #e2e8f0;
      }
      table.tbl thead th {
        background-color: #31599b;
        color: #fff;
        font-weight: 600;
        text-align: left;
        padding: 1rem;
        border-bottom: 0;
      }
      table.tbl tbody td {
        padding: 0.75rem 1rem;
      }
      table.tbl tbody tr:hover {
        background-color: #f8fafc;
      }
      table.tbl tfoot td {
        background-color: #f1f5f9;
        font-weight: 700;
        padding: 0.75rem 1rem;
      }
      table.tbl-plain th,
      table.tbl-plain td {
        border: 1px solid #cbd5e1;
        padding: 0.5rem 0.75rem;
      }
      /* Tabel KKJ: selalu muat di container, tanpa min-width & tanpa padding paksa */
      table.tbl-kkj {
        min-width: 0 !important;
        table-layout: fixed;
        width: 100%;
      }
      table.tbl-kkj th,
      table.tbl-kkj td {
        padding: 0.5rem 0.625rem !important;
        white-space: normal !important;
        overflow-wrap: anywhere;
        word-break: break-word;
      }
      @media (max-width: 767px) {
        .h-\[220px\] {
          height: auto;
          min-height: 160px;
          padding-top: 2rem;
          padding-bottom: 2rem;
        }
        .px-8.max-w-xl {
          padding-left: 1.25rem;
          padding-right: 1.25rem;
        }
        .rounded-\[2rem\].p-8 {
          padding: 1.25rem;
          border-radius: 1rem;
        }
        table {
          font-size: 0.8125rem;
        }
        table th,
        table td {
          padding: 0.625rem !important;
          white-space: normal !important;
          overflow-wrap: anywhere;
        }
        table.min-w-\[1850px\],
        table.min-w-\[1550px\],
        table.min-w-\[1400px\],
        table.min-w-\[1200px\],
        table.min-w-\[900px\],
        table.min-w-\[760px\] {
          min-width: 680px;
        }
        .flex.justify-between {
          flex-wrap: wrap;
          gap: 0.75rem;
        }
        .flex.justify-between > :last-child:not(:first-child) {
          max-width: 100%;
        }
      }
    </style>
  </head>
  <body class="flex h-dvh max-h-dvh overflow-hidden antialiased">
    <div data-mobile-nav-backdrop class="fixed inset-0 z-40 hidden bg-slate-950/40 md:hidden"></div>
    <aside data-mobile-nav class="fixed inset-y-0 left-0 z-50 hidden h-dvh w-[min(300px,calc(100vw-2rem))] flex-shrink-0 flex-col overflow-y-auto bg-white border-r border-slate-200 shadow-xl md:static md:flex md:w-[300px] md:overflow-hidden md:shadow-none">
      <div class="px-6 pt-7 pb-4 flex-shrink-0">
        <h2 class="text-[22px] font-bold tracking-tight text-[#0a192f]">HCBP Area 3 Apps</h2>
        <p class="text-xs text-slate-400 mt-1">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</p>
      </div>
      <nav class="px-4 space-y-1 flex-1 md:overflow-y-auto">
        @if(auth()->user()->role === 'admin_master')
          <a class="flex items-center gap-3 px-4 py-3 rounded-xl font-semibold {{ request()->routeIs('admin-master.dashboard') ? 'bg-slate-100' : '' }}" href="{{ route('admin-master.dashboard') }}">
            <span class="material-symbols-outlined filled text-[20px]">dashboard</span>
            <span class="text-[15px]">Dashboard</span>
          </a>
          <details class="open:group" @if(($activeSection ?? '') === 'idp') open @endif>
            <summary class="flex items-center justify-between gap-3 px-4 py-3 text-[#0a192f]/80 hover:bg-slate-50 rounded-xl transition-colors cursor-pointer group">
              <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-[20px]">people</span>
                <span class="text-[15px] leading-tight">IDP<br /><span class="text-[11px] font-normal text-slate-400">(Individual Development Plan)</span></span>
              </div>
              <span class="material-symbols-outlined text-[16px] text-slate-300 group-open:rotate-90">chevron_right</span>
            </summary>
            <nav class="pl-10 space-y-1 mt-1">
              <a class="flex items-center gap-3 px-4 py-2.5 text-[14px] {{ ($activePage ?? '') === 'daftar' ? 'font-semibold text-[#0a192f] bg-slate-50' : 'text-[#0a192f]/70' }} hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('admin-master.idp.daftar') }}">Daftar IDP</a>
              <a class="flex items-center gap-3 px-4 py-2.5 text-[14px] {{ ($activePage ?? '') === 'pemantauan' ? 'font-semibold text-[#0a192f] bg-slate-50' : 'text-[#0a192f]/70' }} hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('admin-master.idp.pemantauan') }}">Penetapan dan Pemantauan IDP</a>
              <a class="flex items-center gap-3 px-4 py-2.5 text-[14px] {{ ($activePage ?? '') === 'pemantauan-coaching' ? 'font-semibold text-[#0a192f] bg-slate-50' : 'text-[#0a192f]/70' }} hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('admin-master.coaching.pemantauan') }}">Pemantauan Coaching</a>
              <a class="flex items-center gap-3 px-4 py-2.5 text-[14px] {{ ($activePage ?? '') === 'evaluasi' ? 'font-semibold text-[#0a192f] bg-slate-50' : 'text-[#0a192f]/70' }} hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('admin-master.idp.evaluasi') }}">Evaluasi IDP</a>
            </nav>
          </details>
          <a class="flex items-center justify-between gap-3 px-4 py-3 text-[#0a192f]/80 hover:bg-slate-50 rounded-xl transition-colors group" href="{{ route('admin-master.setting-role') }}">
            <div class="flex items-center gap-3">
              <span class="material-symbols-outlined text-[20px]">settings_account_box</span>
              <span class="text-[15px]">Setting Role</span>
            </div>
            <span class="material-symbols-outlined text-[16px] text-slate-300">chevron_right</span>
          </a>
        @elseif(auth()->user()->role === 'admin_area')
          <a class="flex items-center gap-3 px-4 py-3 rounded-xl font-semibold {{ request()->routeIs('admin-area.dashboard') ? 'bg-slate-100' : '' }}" href="{{ route('admin-area.dashboard') }}">
            <span class="material-symbols-outlined filled text-[20px]">dashboard</span>
            <span class="text-[15px]">Dashboard</span>
          </a>
          <details class="open:group" @if(($activeSection ?? '') === 'idp') open @endif>
            <summary class="flex items-center justify-between gap-3 px-4 py-3 text-[#0a192f]/80 hover:bg-slate-50 rounded-xl transition-colors cursor-pointer group">
              <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-[20px]">people</span>
                <span class="text-[15px] leading-tight">IDP<br /><span class="text-[11px] font-normal text-slate-400">(Individual Development Plan)</span></span>
              </div>
              <span class="material-symbols-outlined text-[16px] text-slate-300 group-open:rotate-90">chevron_right</span>
            </summary>
            <nav class="pl-10 space-y-1 mt-1">
              <a class="flex items-center gap-3 px-4 py-2.5 text-[14px] {{ ($activePage ?? '') === 'daftar' ? 'font-semibold text-[#0a192f] bg-slate-50' : 'text-[#0a192f]/70' }} hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('admin-area.idp.daftar') }}">Daftar IDP</a>
              <a class="flex items-center gap-3 px-4 py-2.5 text-[14px] {{ ($activePage ?? '') === 'pemantauan' ? 'font-semibold text-[#0a192f] bg-slate-50' : 'text-[#0a192f]/70' }} hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('admin-area.idp.pemantauan') }}">Penetapan dan Pemantauan IDP</a>
              <a class="flex items-center gap-3 px-4 py-2.5 text-[14px] {{ ($activePage ?? '') === 'pemantauan-coaching' ? 'font-semibold text-[#0a192f] bg-slate-50' : 'text-[#0a192f]/70' }} hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('admin-area.coaching.pemantauan') }}">Pemantauan Coaching</a>
              <a class="flex items-center gap-3 px-4 py-2.5 text-[14px] {{ ($activePage ?? '') === 'evaluasi' ? 'font-semibold text-[#0a192f] bg-slate-50' : 'text-[#0a192f]/70' }} hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('admin-area.idp.evaluasi') }}">Evaluasi IDP</a>
            </nav>
          </details>
        @elseif(auth()->user()->role === 'atasan')
          <details class="open:group" @if(in_array(($activeSection ?? ''), ['idp', 'coaching'])) open @endif>
            <summary class="flex items-center justify-between gap-3 px-4 py-3 text-[#0a192f]/80 hover:bg-slate-50 rounded-xl transition-colors cursor-pointer group">
              <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-[20px]">people</span>
                <span class="text-[15px] leading-tight">IDP<br /><span class="text-[11px] font-normal text-slate-400">(Individual Development Plan)</span></span>
              </div>
              <span class="material-symbols-outlined text-[16px] text-slate-300 group-open:rotate-90">chevron_right</span>
            </summary>
            <nav class="pl-10 space-y-1 mt-1">
              <a class="flex items-center gap-3 px-4 py-2.5 text-[14px] {{ ($activePage ?? '') === 'daftar' ? 'font-semibold text-[#0a192f] bg-slate-50' : 'text-[#0a192f]/70' }} hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('atasan.idp.daftar') }}">Daftar IDP</a>
              <a class="flex items-center gap-3 px-4 py-2.5 text-[14px] {{ ($activePage ?? '') === 'penetapan' ? 'font-semibold text-[#0a192f] bg-slate-50' : 'text-[#0a192f]/70' }} hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('atasan.idp.penetapan') }}">Penetapan IDP</a>
              <a class="flex items-center gap-3 px-4 py-2.5 text-[14px] {{ ($activePage ?? '') === 'pemantauan' ? 'font-semibold text-[#0a192f] bg-slate-50' : 'text-[#0a192f]/70' }} hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('atasan.idp.pemantauan') }}">Pemantauan IDP</a>
              <a class="flex items-center gap-3 px-4 py-2.5 text-[14px] {{ ($activePage ?? '') === 'coaching' ? 'font-semibold text-[#0a192f] bg-slate-50' : 'text-[#0a192f]/70' }} hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('atasan.coaching.index') }}">Coaching Bawahan</a>
              <a class="flex items-center gap-3 px-4 py-2.5 text-[14px] {{ ($activePage ?? '') === 'evaluasi' ? 'font-semibold text-[#0a192f] bg-slate-50' : 'text-[#0a192f]/70' }} hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('atasan.idp.evaluasi') }}">Evaluasi IDP</a>
            </nav>
          </details>
        @elseif(auth()->user()->role === 'bawahan')
          <details class="open:group" @if(in_array(($activeSection ?? ''), ['idp', 'coaching'])) open @endif>
            <summary class="flex items-center justify-between gap-3 px-4 py-3 text-[#0a192f]/80 hover:bg-slate-50 rounded-xl transition-colors cursor-pointer group">
              <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-[20px]">people</span>
                <span class="text-[15px] leading-tight">IDP Saya<br /><span class="text-[11px] font-normal text-slate-400">(Individual Development Plan)</span></span>
              </div>
              <span class="material-symbols-outlined text-[16px] text-slate-300 group-open:rotate-90">chevron_right</span>
            </summary>
            <nav class="pl-10 space-y-1 mt-1">
              <a class="flex items-center gap-3 px-4 py-2.5 text-[14px] {{ ($activePage ?? '') === 'daftar' ? 'font-semibold text-[#0a192f] bg-slate-50' : 'text-[#0a192f]/70' }} hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('bawahan.idp.daftar') }}">IDP Saya</a>
              <a class="flex items-center gap-3 px-4 py-2.5 text-[14px] {{ ($activePage ?? '') === 'penetapan' ? 'font-semibold text-[#0a192f] bg-slate-50' : 'text-[#0a192f]/70' }} hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('bawahan.idp.penetapan') }}">Penetapan IDP</a>
              <a class="flex items-center gap-3 px-4 py-2.5 text-[14px] {{ ($activePage ?? '') === 'pemantauan' ? 'font-semibold text-[#0a192f] bg-slate-50' : 'text-[#0a192f]/70' }} hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('bawahan.idp.pemantauan') }}">Pemantauan IDP</a>
              <a class="flex items-center gap-3 px-4 py-2.5 text-[14px] {{ ($activePage ?? '') === 'coaching' ? 'font-semibold text-[#0a192f] bg-slate-50' : 'text-[#0a192f]/70' }} hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('bawahan.coaching.index') }}">Coaching Saya</a>
              <a class="flex items-center gap-3 px-4 py-2.5 text-[14px] {{ ($activePage ?? '') === 'evaluasi' ? 'font-semibold text-[#0a192f] bg-slate-50' : 'text-[#0a192f]/70' }} hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('bawahan.idp.evaluasi') }}">Evaluasi IDP</a>
            </nav>
          </details>
        @endif
      </nav>
     </aside>
    <main class="flex min-w-0 flex-1 flex-col overflow-hidden bg-[#f8fafc]">
      <header class="w-full sticky top-0 bg-white border-b border-slate-200 z-50">
        <div class="flex h-[64px] items-center justify-between px-4 py-3 sm:h-[76px] sm:px-6 lg:px-8">
          <div class="flex min-w-0 items-center gap-2 sm:gap-4">
            <button data-mobile-nav-toggle class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 md:hidden" type="button" aria-label="Buka navigasi" aria-expanded="false">
              <span class="material-symbols-outlined">menu</span>
            </button>
            <img class="h-4 max-w-[120px] object-contain sm:h-5 sm:max-w-none" src="{{ asset('logo/main-danantara-indonesia-horizontal-logo.webp') }}" alt="Danantara Indonesia logo" />
            <div class="h-6 w-px flex-shrink-0 bg-slate-200 sm:h-7"></div>
            <img class="h-5 max-w-[70px] object-contain sm:h-6 sm:max-w-none" src="{{ asset('images/logo_pln_horizontal.svg') }}" alt="PLN logo" />
          </div>
          <div class="flex flex-shrink-0 items-center gap-1 sm:gap-4">
            <details class="relative">
              <summary class="relative flex list-none items-center rounded-full p-2 text-slate-500 transition-colors hover:bg-slate-100" aria-label="Notifikasi">
                <span class="material-symbols-outlined">notifications</span>
                @if (($notifBelumDibaca ?? 0) > 0)
                  <span class="absolute -top-0.5 -right-0.5 flex h-4 min-w-4 items-center justify-center rounded-full border-2 border-white bg-red-500 px-1 text-[9px] font-bold text-white">{{ $notifBelumDibaca > 99 ? '99+' : $notifBelumDibaca }}</span>
                @endif
              </summary>
              <div class="absolute right-0 z-[100] mt-2 max-h-[70vh] w-[min(360px,calc(100vw-2rem))] overflow-y-auto rounded-xl border border-slate-200 bg-white py-2 shadow-lg">
                <div class="flex items-center justify-between px-4 pb-2">
                  <form method="POST" action="{{ route('notifikasi.baca-semua') }}">
                    @csrf
                    <button type="submit" class="text-[12px] font-medium text-[#31599b] hover:underline">Tandai semua dibaca</button>
                  </form>
                </div>
                @forelse (($notifTerbaru ?? collect()) as $item)
                  <form method="POST" action="{{ route('notifikasi.baca', $item) }}">
                    @csrf
                    <button type="submit" class="flex w-full items-start gap-2 px-4 py-2.5 text-left hover:bg-slate-50">
                      <span class="material-symbols-outlined mt-0.5 text-[18px] {{ $item->dibaca_pada ? 'text-slate-300' : 'text-[#31599b]' }}">notifications</span>
                      <span class="min-w-0 flex-1">
                        <span class="block truncate text-[13px] {{ $item->dibaca_pada ? 'font-medium text-slate-500' : 'font-semibold text-[#0a192f]' }}">{{ $item->judul }}</span>
                        <span class="mt-0.5 line-clamp-2 block text-[12px] text-slate-500">{{ $item->pesan }}</span>
                        <span class="mt-0.5 block text-[10px] text-slate-400">{{ $item->created_at?->diffForHumans() }}</span>
                      </span>
                    </button>
                  </form>
                @empty
                  <p class="px-4 py-6 text-center text-[13px] text-slate-400">Belum ada notifikasi.</p>
                @endforelse
                <a href="{{ route('notifikasi.index') }}" class="mt-1 block border-t border-slate-100 px-4 pt-2 text-center text-[12px] font-medium text-[#31599b] hover:underline">Lihat semua</a>
              </div>
            </details>
            <details class="relative">
              <summary class="flex items-center gap-3 cursor-pointer hover:bg-slate-50 p-1 pr-3 rounded-full transition-colors list-none">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex items-center justify-center text-sm font-semibold">{{ strtoupper(substr(auth()->user()->nama, 0, 2)) }}</div>
                <div class="hidden md:flex flex-col">
                  <span class="text-[13px] font-semibold text-[#0a192f] leading-tight">Halo, {{ auth()->user()->nama }}</span>
                  <span class="text-[11px] text-slate-400 leading-tight">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</span>
                </div>
                <span class="material-symbols-outlined text-slate-400 text-[18px] ml-1">expand_more</span>
              </summary>
              <div class="absolute right-0 mt-2 w-44 bg-white rounded-xl shadow-lg border border-slate-200 py-2 z-[100]">
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button type="submit" class="flex items-center gap-3 px-4 py-2.5 w-full text-sm text-red-600 hover:bg-red-50 rounded-xl transition-colors">
                    <span class="material-symbols-outlined text-[18px]">logout</span>
                    <span>Logout</span>
                  </button>
                </form>
              </div>
            </details>
          </div>
        </div>
      </header>
<div class="relative min-h-0 flex-1 space-y-6 overflow-y-auto px-4 py-4 sm:px-6 sm:py-6 lg:px-8">
         @yield('content')
       </div>
    </main>
    <script>
      const mobileNav = document.querySelector('[data-mobile-nav]');
      const mobileNavBackdrop = document.querySelector('[data-mobile-nav-backdrop]');
      const mobileNavToggle = document.querySelector('[data-mobile-nav-toggle]');
      const closeMobileNav = () => {
        mobileNav.classList.add('hidden');
        mobileNavBackdrop.classList.add('hidden');
        mobileNavToggle?.setAttribute('aria-expanded', 'false');
      };
      const openMobileNav = () => {
        mobileNav.classList.remove('hidden');
        mobileNav.classList.add('flex');
        mobileNavBackdrop.classList.remove('hidden');
        mobileNavToggle?.setAttribute('aria-expanded', 'true');
      };
      mobileNavToggle?.addEventListener('click', openMobileNav);
      mobileNavBackdrop?.addEventListener('click', closeMobileNav);
      mobileNav?.querySelectorAll('a').forEach((link) => link.addEventListener('click', closeMobileNav));
      const modals = Array.from(document.querySelectorAll('[data-modal]'));
      const mountModal = (modal) => {
        if (modal.parentElement === document.body) return;
        document.body.appendChild(modal);
      };
      modals.forEach((modal) => {
        mountModal(modal);
        modal.addEventListener('click', (event) => {
          if (event.target === modal) modal.classList.replace('flex', 'hidden');
        });
      });
      document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;
        closeMobileNav();
        modals.forEach((modal) => modal.classList.replace('flex', 'hidden'));
      });
      document.addEventListener('submit', async (event) => {
        const form = event.target;
        if (!form.matches('form[data-async-upload]')) return;
        event.preventDefault();
        event.stopImmediatePropagation();
        if (!form.checkValidity()) {
          form.reportValidity();
          return;
        }
        const button = form.querySelector('button[type="submit"]');
        button.disabled = true;
        try {
          const response = await fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
              Accept: 'application/json',
              'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
          });
          const data = await response.json();
          if (!response.ok) {
            throw new Error(Object.values(data.errors || {})[0]?.[0] || data.message || 'Upload gagal.');
          }
          form.querySelector('input[type="file"]').value = '';
          Swal.fire({ icon: 'success', text: data.message });
        } catch (error) {
          Swal.fire({ icon: 'error', text: error.message || 'Upload gagal.' });
        } finally {
          button.disabled = false;
        }
      });
      document.addEventListener('submit', (event) => {
        const form = event.target;
        if (form.matches('form[data-async-upload], form[data-search]') || form.dataset.confirmed || form.action.endsWith('/logout')) return;
        event.preventDefault();
        if (!form.checkValidity()) {
          form.reportValidity();
          return;
        }
        Swal.fire({
          icon: 'question',
          text: form.querySelector('input[name="_method"][value="PUT"]') ? 'Simpan perubahan?' : 'Simpan data?',
          showCancelButton: true,
          confirmButtonText: 'Ya, simpan',
          cancelButtonText: 'Batal',
        }).then((result) => {
          if (result.isConfirmed) {
            form.dataset.confirmed = '1';
            form.submit();
          }
        });
      });
      // Tabel bertanda data-table-search="false" hanya dirapikan, tanpa paginasi.
      // Tabel di dalam .bawahan-section adalah form input, jadi dikecualikan.
      const isBawahan = document.querySelector('[data-role="bawahan"]');
      const tabelData = (table) => table.dataset.tableSearch !== 'false' && ! table.classList.contains('tbl-plain') && ! table.closest('.bawahan-section');

      document.querySelectorAll('table').forEach((table) => {
        if (! tabelData(table)) {
          table.querySelectorAll('th, td').forEach((cell) => cell.classList.add('whitespace-nowrap'));
          return;
        }

        table.parentElement.classList.add('overflow-x-auto');
        if (table.dataset.tableScroll !== 'false') {
          table.classList.add('min-w-full', 'w-max');
        }
        table.querySelectorAll('th, td').forEach((cell) => cell.classList.add('whitespace-nowrap'));
      });

      document.querySelectorAll('table').forEach((table) => {
        if (! tabelData(table) || table.dataset.tablePagination === 'false') return;

        const rows = Array.from(table.tBodies).flatMap((body) => Array.from(body.rows));
        if (rows.length <= 10) return;

        const container = table.parentElement;
        const pageSize = 10;
        let page = 1;
        const pagination = document.createElement('div');
        pagination.className = 'mt-5 mb-2 flex flex-col items-center justify-between gap-4 border-t border-slate-100 px-1 pt-4 text-sm text-slate-500 sm:flex-row';
        container.after(pagination);
        const tombol = (label, target, aktif) => `<button type="button" data-page="${target}" ${aktif ? '' : 'disabled'} class="rounded border border-slate-300 bg-white px-3 py-1 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50">${label}</button>`;
        const render = () => {
          const totalPages = Math.max(1, Math.ceil(rows.length / pageSize));
          page = Math.min(page, totalPages);
          rows.forEach((row) => row.hidden = true);
          rows.slice((page - 1) * pageSize, page * pageSize).forEach((row) => row.hidden = false);
          const opsi = Array.from({ length: totalPages }, (_, i) => `<option value="${i + 1}" ${page === i + 1 ? 'selected' : ''}>${i + 1}</option>`).join('');
          pagination.innerHTML = `<span>Menampilkan ${(page - 1) * pageSize + 1}–${Math.min(page * pageSize, rows.length)} dari ${rows.length} data</span><div class="flex flex-wrap items-center gap-2">${tombol('&lt;&lt;', 1, page !== 1)}${tombol('&lt;', page - 1, page !== 1)}<select aria-label="Pilih halaman" class="rounded border border-slate-300 bg-white py-1 text-sm transition focus:border-[#31599b] focus:ring-[#31599b]">${opsi}</select>${tombol('&gt;', page + 1, page !== totalPages)}${tombol('&gt;&gt;', totalPages, page !== totalPages)}</div>`;
          pagination.querySelectorAll('button:not([disabled])').forEach((btn) => btn.addEventListener('click', () => { page = Number(btn.dataset.page); render(); }));
          pagination.querySelector('select').addEventListener('change', (event) => { page = Number(event.target.value); render(); });
        };
        render();
      });
    </script>
  </body>
</html>
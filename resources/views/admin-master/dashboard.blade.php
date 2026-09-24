@extends('layouts.app', ['title' => 'HCBP Area 3 Apps - Dashboard'])

@section('content')
{{-- Hero Banner --}}
<div class="relative overflow-hidden rounded-2xl bg-white border border-slate-200 h-[200px] flex items-center">
  <div class="absolute inset-0 z-0">
    <div class="w-full h-full" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCjz6BR9t1xAm_4yTRtjUFCuOHqIkeE5EKFN9IU8hG8k_u3B7psxquSS6Suk591TY9q_Y27C-c3_8Z_HW-o9T4mcs-BSs8NcCHUUxWxaIBP5fVyRf74hO-H-p4ikb76omJMmeg7ue3VDxPp1T-0X1MDCB76X944-1OQyLGojPYj3yPCpw1wjdFvEbWlt83s8VQfKiYaIR8j8f1RfVnP8NMKIKcRo4783TuNbljKJLkY62hs9DzkeU81TZVA3E9SyXdF3Ck'); background-size: cover; background-position: center right;"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-white via-white/85 to-transparent"></div>
  </div>
  <div class="relative z-10 px-8 max-w-xl">
    <h1 class="text-[27px] font-bold text-[#0a192f] mb-2 leading-tight">Selamat Datang di HCBP AREA 3 APPS</h1>
    <p class="text-slate-500 text-[15px]">Kelola kompetensi, sertifikasi, dan pengembangan SDM secara terintegrasi.</p>
  </div>
</div>

{{-- KPI Cards (4 cards untuk isi grid 4-kolom) --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
  {{-- Card 1: IDP Aktif --}}
  <div class="bg-white border border-[#E9EDF3] rounded-2xl shadow-sm p-5 flex items-center gap-4">
    <div class="w-12 h-12 rounded-full bg-[#0a192f] flex items-center justify-center text-white shrink-0">
      <span class="material-symbols-outlined filled">groups</span>
    </div>
    <div class="flex-1 min-w-0">
      <p class="text-[12px] text-slate-400 leading-tight">IDP Aktif</p>
      <h3 class="text-[24px] font-bold text-[#0a192f] leading-tight">{{ number_format($idpAktif) }}</h3>
      <p class="text-[11px] text-slate-400">dari {{ number_format($totalIdp) }} total IDP</p>
    </div>
    <div class="text-right shrink-0">
      <span class="text-[12px] font-bold {{ ($totalIdp > 0 ? ($idpAktif / $totalIdp * 100) : 0) > 0 ? 'text-emerald-500' : 'text-slate-400' }}">
        {{ $totalIdp > 0 ? number_format($idpAktif / $totalIdp * 100, 1) : '0' }}%
      </span>
      <p class="text-[10px] text-slate-400 whitespace-nowrap">aktif berjalan</p>
    </div>
  </div>

  {{-- Card 2: Rata-rata Progres IDP --}}
  <div class="bg-white border border-[#E9EDF3] rounded-2xl shadow-sm p-5 flex items-center gap-4">
    <div class="w-12 h-12 rounded-full bg-indigo-500 flex items-center justify-center text-white shrink-0">
      <span class="material-symbols-outlined filled">trending_up</span>
    </div>
    <div class="flex-1 min-w-0">
      <p class="text-[12px] text-slate-400 leading-tight">Rata-rata Progres IDP</p>
      <h3 class="text-[24px] font-bold text-[#0a192f] leading-tight">{{ number_format($rataProgres, 1) }}%</h3>
      <p class="text-[11px] text-slate-400">kemajuan pelaksanaan</p>
    </div>
    <div class="text-right shrink-0">
      <span class="text-[12px] font-bold {{ $rataProgres >= 50 ? 'text-emerald-500' : 'text-amber-500' }}">
        @if($rataProgres >= 75) Baik @elseif($rataProgres >= 50) Cukup @else Perlu Perhatian @endif
      </span>
      <p class="text-[10px] text-slate-400 whitespace-nowrap">status progres</p>
    </div>
  </div>

  {{-- Card 3: Coaching Selesai --}}
  <div class="bg-white border border-[#E9EDF3] rounded-2xl shadow-sm p-5 flex items-center gap-4">
    <div class="w-12 h-12 rounded-full bg-emerald-500 flex items-center justify-center text-white shrink-0">
      <span class="material-symbols-outlined filled">verified</span>
    </div>
    <div class="flex-1 min-w-0">
      <p class="text-[12px] text-slate-400 leading-tight">Coaching Selesai</p>
      <h3 class="text-[24px] font-bold text-[#0a192f] leading-tight">{{ number_format($coachingSelesai) }}</h3>
      <p class="text-[11px] text-slate-400">dari {{ number_format($totalCoaching) }} sesi</p>
    </div>
    <div class="text-right shrink-0">
      <span class="text-[12px] font-bold {{ ($totalCoaching > 0 ? ($coachingSelesai / $totalCoaching * 100) : 0) > 0 ? 'text-emerald-500' : 'text-slate-400' }}">
        {{ $totalCoaching > 0 ? number_format($coachingSelesai / $totalCoaching * 100, 1) : '0' }}%
      </span>
      <p class="text-[10px] text-slate-400 whitespace-nowrap">tingkat selesai</p>
    </div>
  </div>

  {{-- Card 4: Total Pegawai Aktif --}}
  <div class="bg-white border border-[#E9EDF3] rounded-2xl shadow-sm p-5 flex items-center gap-4">
    <div class="w-12 h-12 rounded-full bg-teal-500 flex items-center justify-center text-white shrink-0">
      <span class="material-symbols-outlined filled">badge</span>
    </div>
    <div class="flex-1 min-w-0">
      <p class="text-[12px] text-slate-400 leading-tight">Total Pegawai Aktif</p>
      <h3 class="text-[24px] font-bold text-[#0a192f] leading-tight">{{ number_format($totalPegawai) }}</h3>
      <p class="text-[11px] text-slate-400">dari {{ number_format($totalPegawaiAll) }} terdaftar</p>
    </div>
    <div class="text-right shrink-0">
      <span class="text-[12px] font-bold text-emerald-500">{{ $totalPegawaiAll > 0 ? number_format($totalPegawai / $totalPegawaiAll * 100, 1) : '0' }}%</span>
      <p class="text-[10px] text-slate-400 whitespace-nowrap">peg. aktif</p>
    </div>
  </div>
</div>

{{-- Row: Quick Action / Todo + Distribusi Pegawai per Unit --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
  {{-- Quick Action / Todo --}}
  <div class="bg-white border border-[#E9EDF3] rounded-2xl shadow-sm p-5">
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center gap-2">
        <span class="material-symbols-outlined text-[#0a192f] text-[22px]">task_alt</span>
        <h2 class="text-[15px] font-bold text-[#0a192f]">Quick Action</h2>
      </div>
      <span class="text-[11px] px-2.5 py-1 rounded-full bg-amber-50 text-amber-600 font-semibold">{{ $todoRencanaReview + $todoCoachingReview + $todoCoachingMenunggu }} Perlu Tindakan</span>
    </div>
    <div class="space-y-3">
      <a href="{{ route('admin-master.idp.penetapan') }}" class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:border-indigo-200 hover:bg-indigo-50/40 transition-colors group">
        <div class="w-9 h-9 rounded-full {{ $todoRencanaReview > 0 ? 'bg-amber-100 text-amber-600' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center shrink-0">
          <span class="material-symbols-outlined text-[20px]">edit_document</span>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-[13px] font-semibold text-[#0a192f] leading-tight">Review Rencana IDP</p>
          <p class="text-[11px] text-slate-400 leading-tight">Diajukan / perlu revisi</p>
        </div>
        <span class="text-[13px] font-bold {{ $todoRencanaReview > 0 ? 'text-amber-600' : 'text-slate-400' }}">{{ $todoRencanaReview }}</span>
        <span class="material-symbols-outlined text-slate-300 text-[18px] group-hover:text-indigo-400 group-hover:translate-x-0.5 transition-all">chevron_right</span>
      </a>
      <a href="{{ route('admin-master.coaching.pemantauan') }}" class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:border-indigo-200 hover:bg-indigo-50/40 transition-colors group">
        <div class="w-9 h-9 rounded-full {{ $todoCoachingReview > 0 ? 'bg-amber-100 text-amber-600' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center shrink-0">
          <span class="material-symbols-outlined text-[20px]">upload_file</span>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-[13px] font-semibold text-[#0a192f] leading-tight">Review Bukti Coaching</p>
          <p class="text-[11px] text-slate-400 leading-tight">Menunggu persetujuan atasan</p>
        </div>
        <span class="text-[13px] font-bold {{ $todoCoachingReview > 0 ? 'text-amber-600' : 'text-slate-400' }}">{{ $todoCoachingReview }}</span>
        <span class="material-symbols-outlined text-slate-300 text-[18px] group-hover:text-indigo-400 group-hover:translate-x-0.5 transition-all">chevron_right</span>
      </a>
      <a href="{{ route('admin-master.coaching.pemantauan') }}" class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:border-indigo-200 hover:bg-indigo-50/40 transition-colors group">
        <div class="w-9 h-9 rounded-full {{ $todoCoachingMenunggu > 0 ? 'bg-amber-100 text-amber-600' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center shrink-0">
          <span class="material-symbols-outlined text-[20px]">pending_actions</span>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-[13px] font-semibold text-[#0a192f] leading-tight">Persetujuan Coaching</p>
          <p class="text-[11px] text-slate-400 leading-tight">Sesi coaching menunggu</p>
        </div>
        <span class="text-[13px] font-bold {{ $todoCoachingMenunggu > 0 ? 'text-amber-600' : 'text-slate-400' }}">{{ $todoCoachingMenunggu }}</span>
        <span class="material-symbols-outlined text-slate-300 text-[18px] group-hover:text-indigo-400 group-hover:translate-x-0.5 transition-all">chevron_right</span>
      </a>
    </div>
    @if(($todoRencanaReview + $todoCoachingReview + $todoCoachingMenunggu) === 0)
      <div class="mt-3 flex items-center gap-2 p-3 rounded-xl bg-emerald-50/60 text-emerald-700">
        <span class="material-symbols-outlined text-[18px]">check_circle</span>
        <p class="text-[12px] font-medium">Semua tindakan sudah selesai. Mantap!</p>
      </div>
    @endif
  </div>

  {{-- Distribusi Pegawai per Unit Induk --}}
  <div class="bg-white border border-[#E9EDF3] rounded-2xl shadow-sm p-5 lg:col-span-2">
    <div class="flex items-center justify-between mb-5">
      <div class="flex items-center gap-2">
        <span class="material-symbols-outlined text-[#0a192f] text-[22px]">donut_large</span>
        <h2 class="text-[15px] font-bold text-[#0a192f]">Distribusi Pegawai per Unit Induk</h2>
      </div>
      <span class="text-[11px] text-slate-400">{{ number_format($totalPegawai) }} pegawai aktif</span>
    </div>
    @if($distribusiUnit->count() > 0)
      <div class="space-y-4">
        @php
          $maxUnit = $distribusiUnit->max('total') ?: 1;
          $warnaUnit = ['bg-[#0a192f]', 'bg-indigo-500', 'bg-emerald-500', 'bg-teal-500', 'bg-amber-500', 'bg-rose-500'];
        @endphp
        @foreach($distribusiUnit as $i => $unit)
          <div>
            <div class="flex items-center justify-between mb-1.5">
              <span class="text-[13px] font-semibold text-[#0a192f]">{{ $unit->unit_induk }}</span>
              <span class="text-[12px] text-slate-500">{{ $unit->total }} pegawai ({{ $unit->persentase }}%)</span>
            </div>
            <div class="w-full h-2.5 rounded-full bg-slate-100 overflow-hidden">
              <div class="h-full rounded-full {{ $warnaUnit[$i % count($warnaUnit)] }} transition-all duration-500" style="width: {{ ($unit->total / $maxUnit) * 100 }}%"></div>
            </div>
          </div>
        @endforeach
      </div>
    @else
      <div class="flex flex-col items-center justify-center py-10 text-center">
        <span class="material-symbols-outlined text-[40px] text-slate-300 mb-2">inbox</span>
        <p class="text-[13px] text-slate-400">Belum ada data pegawai</p>
      </div>
    @endif
  </div>
</div>

{{-- Row: Status IDP Breakdown + Aktivitas Terbaru --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
  {{-- Status IDP Breakdown --}}
  <div class="bg-white border border-[#E9EDF3] rounded-2xl shadow-sm p-5">
    <div class="flex items-center justify-between mb-5">
      <div class="flex items-center gap-2">
        <span class="material-symbols-outlined text-[#0a192f] text-[22px]">bar_chart</span>
        <h2 class="text-[15px] font-bold text-[#0a192f]">Status IDP</h2>
      </div>
      <a href="{{ route('admin-master.idp.pemantauan') }}" class="text-[12px] font-semibold text-indigo-600 hover:text-indigo-700 flex items-center gap-1">
        Lihat detail
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
      </a>
    </div>
    @php
      $warnaStatus = [
        'Draft' => 'bg-slate-400',
        'Diajukan' => 'bg-amber-400',
        'Revisi' => 'bg-rose-400',
        'Disetujui' => 'bg-indigo-500',
        'Berjalan' => 'bg-emerald-500',
        'Selesai' => 'bg-teal-500',
      ];
      $totalStatusIdp = $statusIdp->sum('total') ?: 1;
    @endphp
    @if($statusIdp->count() > 0)
      <div class="flex items-end gap-2 h-40 mb-4 px-2">
        @foreach($statusIdp as $status)
          @php $tinggi = ($status->total / $totalStatusIdp) * 100; @endphp
          <div class="flex-1 flex flex-col items-center justify-end gap-1.5 group">
            <span class="text-[11px] font-bold text-[#0a192f] opacity-0 group-hover:opacity-100 transition-opacity">{{ $status->total }}</span>
            <div class="w-full rounded-t-md {{ $warnaStatus[$status->status_perencanaan] ?? 'bg-slate-300' }} transition-all duration-500 hover:opacity-80" style="height: {{ max($tinggi, 4) }}%"></div>
          </div>
        @endforeach
      </div>
      <div class="flex flex-wrap gap-x-4 gap-y-2 px-2">
        @foreach($statusIdp as $status)
          <div class="flex items-center gap-1.5">
            <span class="w-2.5 h-2.5 rounded-full {{ $warnaStatus[$status->status_perencanaan] ?? 'bg-slate-300' }}"></span>
            <span class="text-[11px] text-slate-500">{{ $status->status_perencanaan }} ({{ $status->total }})</span>
          </div>
        @endforeach
      </div>
    @else
      <div class="flex flex-col items-center justify-center py-10 text-center">
        <span class="material-symbols-outlined text-[40px] text-slate-300 mb-2">inbox</span>
        <p class="text-[13px] text-slate-400">Belum ada data IDP</p>
      </div>
    @endif
  </div>

  {{-- Aktivitas Terbaru --}}
  <div class="bg-white border border-[#E9EDF3] rounded-2xl shadow-sm p-5">
    <div class="flex items-center justify-between mb-5">
      <div class="flex items-center gap-2">
        <span class="material-symbols-outlined text-[#0a192f] text-[22px]">history</span>
        <h2 class="text-[15px] font-bold text-[#0a192f]">Aktivitas Terbaru</h2>
      </div>
    </div>
    @if($aktivitas->count() > 0)
      <div class="space-y-3 max-h-72 overflow-y-auto pr-1">
        @foreach($aktivitas as $item)
          @php
            $ikonAktivitas = [
              'Rencana IDP' => ['edit_document', 'bg-amber-100 text-amber-600'],
              'Bukti Coaching' => ['upload_file', 'bg-indigo-100 text-indigo-600'],
              'Coaching' => ['verified', 'bg-emerald-100 text-emerald-600'],
            ];
            [$ikon, $warna] = $ikonAktivitas[$item['tipe']] ?? ['circle', 'bg-slate-100 text-slate-500'];
            $warnaStatusAktivitas = [
              'Diajukan' => 'text-amber-600 bg-amber-50',
              'Revisi' => 'text-rose-600 bg-rose-50',
              'Menunggu Review' => 'text-amber-600 bg-amber-50',
              'Disetujui' => 'text-emerald-600 bg-emerald-50',
            ];
          @endphp
          <a href="{{ $item['url'] }}" class="flex items-start gap-3 p-3 rounded-xl border border-slate-100 hover:border-indigo-200 hover:bg-indigo-50/40 transition-colors group">
            <div class="w-9 h-9 rounded-full {{ $warna }} flex items-center justify-center shrink-0">
              <span class="material-symbols-outlined text-[20px]">{{ $ikon }}</span>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-[13px] text-[#0a192f] leading-snug">{{ $item['deskripsi'] }}</p>
              <div class="flex items-center gap-2 mt-1">
                <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold {{ $warnaStatusAktivitas[$item['status']] ?? 'text-slate-500 bg-slate-50' }}">{{ $item['status'] }}</span>
                <span class="text-[11px] text-slate-400">{{ $item['waktu']?->diffForHumans() }}</span>
              </div>
            </div>
            <span class="material-symbols-outlined text-slate-300 text-[18px] group-hover:text-indigo-400 group-hover:translate-x-0.5 transition-all shrink-0">chevron_right</span>
          </a>
        @endforeach
      </div>
    @else
      <div class="flex flex-col items-center justify-center py-10 text-center">
        <span class="material-symbols-outlined text-[40px] text-slate-300 mb-2">inbox</span>
        <p class="text-[13px] text-slate-400">Belum ada aktivitas terbaru</p>
      </div>
    @endif
  </div>
</div>
@endsection

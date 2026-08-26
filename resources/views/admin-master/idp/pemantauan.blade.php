@php($pageTitle = 'Pemantauan IDP')
@php($activeSection = 'idp')
@php($activePage = 'pemantauan')

@extends('layouts.app', ['title' => $pageTitle])

@section('content')
<div class="relative overflow-hidden rounded-2xl bg-white border border-slate-200 h-[220px] flex items-center">
  <div class="absolute inset-0 z-0">
    <div class="w-full h-full" style="background-image:url('https://lh3.googleusercontent.com/aida-public/AB6AXuCjz6BR9t1xAm_4yTRtjUFCuOHqIkeE5EKFN9IU8hG8k_u3B7psxquSS6Suk591TY9q_Y27C-c3_8Z_HW-o9T4mcs-BSs8NcCHUUxWxaIBP5fVyRf74hO-H-p4ikb76omJMmeg7ue3VDxPp1T-0X1MDCB76X944-1OQyLGojPYj3yPCpw1wjdFvEbWlt83s8VQfKiYaIR8j8f1RfVnP8NMKIKcRo4783TuNbljKJLkY62hs9DzkeU81TZVA3E9SyXdF3Ck'); background-size:cover; background-position:center right;"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-white via-white/85 to-transparent"></div>
  </div>
  <div class="relative z-10 px-8 max-w-xl">
    <h1 class="text-[27px] font-bold text-[#0a192f] mb-2 leading-tight">Pemantauan IDP</h1>
    <p class="text-slate-500 text-[15px]">Pantau progres rencana pengembangan pegawai.</p>
  </div>
</div>

<div class="bg-white border border-slate-200 rounded-2xl p-6 mb-6">
  <div class="flex items-center justify-between mb-6">
    <div>
      <h2 class="text-lg font-bold">Status Pemantauan IDP per Unit Area</h2>
      <p class="text-sm text-slate-500 mt-1">Contoh tampilan diagram. Data akan disambungkan kemudian.</p>
    </div>
    <span class="text-xs text-slate-400">Contoh Data</span>
  </div>
  @php($statusColors = [
    'belum' => '#94a3b8',
    'menunggu' => '#f59e0b',
    'disetujui' => '#22c55e',
    'berjalan' => '#31599b',
  ])
  @php($chartMax = 15)
  @php($chartData = [
    ['unit' => 'UID S2JB', 'belum' => 8, 'menunggu' => 5, 'disetujui' => 12, 'berjalan' => 10],
    ['unit' => 'UID Lampung', 'belum' => 6, 'menunggu' => 4, 'disetujui' => 8, 'berjalan' => 7],
    ['unit' => 'UID Sumbagsel', 'belum' => 5, 'menunggu' => 6, 'disetujui' => 7, 'berjalan' => 5],
    ['unit' => 'UID Babel', 'belum' => 5, 'menunggu' => 3, 'disetujui' => 5, 'berjalan' => 4],
  ])
  @php($grandTotal = array_sum(array_map(fn($row) => $row['belum'] + $row['menunggu'] + $row['disetujui'] + $row['berjalan'], $chartData)))
  <div class="grid grid-cols-1 xl:grid-cols-12 gap-8 items-stretch">
    <div class="xl:col-span-6 space-y-6 flex flex-col justify-between">
      <div class="flex flex-wrap gap-4 text-xs bg-slate-50 p-3 rounded-xl border border-slate-100">
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded" style="background-color: {{ $statusColors['belum'] }};"></span> Belum Direncanakan</div>
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded" style="background-color: {{ $statusColors['menunggu'] }};"></span> Menunggu Persetujuan</div>
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded" style="background-color: {{ $statusColors['disetujui'] }};"></span> Disetujui</div>
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded" style="background-color: {{ $statusColors['berjalan'] }};"></span> Berjalan</div>
      </div>
      <div class="space-y-5 flex-grow flex flex-col justify-around pt-2">
        @foreach($chartData as $row)
        <div>
          <div class="text-sm font-semibold text-slate-800 mb-2.5">{{ $row['unit'] }}</div>
          <div class="space-y-2">
            @foreach(['belum' => 'Belum', 'menunggu' => 'Menunggu', 'disetujui' => 'Disetujui', 'berjalan' => 'Berjalan'] as $key => $label)
            @php($val = $row[$key])
            @php($pct = $grandTotal > 0 ? round(($val / $grandTotal) * 100, 1) : 0)
            @php($trackColor = match($key) { 'menunggu' => 'bg-amber-50', 'disetujui' => 'bg-emerald-50', 'berjalan' => 'bg-blue-50', default => 'bg-slate-100' })
            <div class="grid grid-cols-[100px_1fr_50px] items-center gap-3">
              <span class="text-xs text-slate-500 font-medium">{{ $label }}</span>
              <div class="h-5 rounded-md {{ $trackColor }} overflow-hidden relative flex items-center">
                <div class="h-full rounded-md flex items-center justify-end pr-2 font-bold text-[10px] text-white transition-all" style="width: {{ max(($val / $chartMax) * 100, 10) }}%; background-color: {{ $statusColors[$key] }};">
                  {{ $val }}
                </div>
              </div>
              <span class="text-xs font-bold text-slate-700 text-right">{{ $pct }}%</span>
            </div>
            @endforeach
          </div>
        </div>
        @endforeach
      </div>
    </div>
    <div class="xl:col-span-6 flex flex-col justify-between space-y-6">
      <div class="overflow-x-auto border border-slate-200 rounded-xl shadow-sm">
        <table class="w-full text-xs border-collapse">
          <thead class="bg-slate-50 border-b border-slate-200 text-slate-700">
            <tr>
              <th class="px-4 py-3 text-left font-bold">Unit Area</th>
              <th class="px-3 py-3 text-center font-semibold text-slate-500">Belum</th>
              <th class="px-3 py-3 text-center font-semibold text-amber-600">Menunggu</th>
              <th class="px-3 py-3 text-center font-semibold text-emerald-600">Disetujui</th>
              <th class="px-3 py-3 text-center font-semibold text-blue-600">Berjalan</th>
              <th class="px-4 py-3 text-center font-bold bg-slate-100 text-slate-800 border-l border-slate-200">Total</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 bg-white">
            @foreach($chartData as $row)
            @php($rowTotal = $row['belum'] + $row['menunggu'] + $row['disetujui'] + $row['berjalan'])
            <tr class="hover:bg-slate-50/80 transition-colors">
              <td class="px-4 py-3 font-semibold text-slate-800">{{ $row['unit'] }}</td>
              <td class="px-3 py-3 text-center text-slate-600">{{ $row['belum'] }}</td>
              <td class="px-3 py-3 text-center text-amber-600 font-semibold">{{ $row['menunggu'] }}</td>
              <td class="px-3 py-3 text-center text-emerald-600 font-semibold">{{ $row['disetujui'] }}</td>
              <td class="px-3 py-3 text-center text-blue-600 font-semibold">{{ $row['berjalan'] }}</td>
              <td class="px-4 py-3 text-center font-bold text-slate-900 bg-slate-50 border-l border-slate-200">{{ $rowTotal }}</td>
            </tr>
            @endforeach
          </tbody>
          <tfoot class="bg-slate-100/80 border-t-2 border-slate-200 font-bold">
            <tr>
              <td class="px-4 py-3 font-bold text-slate-800">Total All</td>
              <td class="px-3 py-3 text-center text-slate-700">{{ array_sum(array_column($chartData, 'belum')) }}</td>
              <td class="px-3 py-3 text-center text-amber-700">{{ array_sum(array_column($chartData, 'menunggu')) }}</td>
              <td class="px-3 py-3 text-center text-emerald-700">{{ array_sum(array_column($chartData, 'disetujui')) }}</td>
              <td class="px-3 py-3 text-center text-blue-700">{{ array_sum(array_column($chartData, 'berjalan')) }}</td>
              <td class="px-4 py-3 text-center font-extrabold text-slate-900 bg-slate-200/60 border-l border-slate-200">{{ $grandTotal }}</td>
            </tr>
          </tfoot>
        </table>
      </div>

      @php($totBelum = array_sum(array_column($chartData, 'belum')))
      @php($totMenunggu = array_sum(array_column($chartData, 'menunggu')))
      @php($totDisetujui = array_sum(array_column($chartData, 'disetujui')))
      @php($totBerjalan = array_sum(array_column($chartData, 'berjalan')))
      @php($pctBelum = $grandTotal > 0 ? ($totBelum / $grandTotal) * 100 : 0)
      @php($pctMenunggu = $grandTotal > 0 ? ($totMenunggu / $grandTotal) * 100 : 0)
      @php($pctDisetujui = $grandTotal > 0 ? ($totDisetujui / $grandTotal) * 100 : 0)
      @php($pctBerjalan = $grandTotal > 0 ? ($totBerjalan / $grandTotal) * 100 : 0)

      @php($ang1 = ($pctBelum / 100) * 360)
      @php($ang2 = $ang1 + (($pctMenunggu / 100) * 360))
      @php($ang3 = $ang2 + (($pctDisetujui / 100) * 360))

      <div class="border border-slate-200 rounded-xl p-5 bg-slate-50/50 flex-grow flex flex-col justify-center">
        <h3 class="text-xs font-bold text-slate-700 mb-12 text-center uppercase tracking-wider">Proporsi Overall Status IDP</h3>
        <div class="flex items-center justify-around gap-5">
          <div class="relative w-60 h-60 flex items-center justify-center">
            <div class="w-full h-full rounded-full" style="background: conic-gradient(
              {{ $statusColors['belum'] }} 0deg {{ $ang1 }}deg,
              {{ $statusColors['menunggu'] }} {{ $ang1 }}deg {{ $ang2 }}deg,
              {{ $statusColors['disetujui'] }} {{ $ang2 }}deg {{ $ang3 }}deg,
              {{ $statusColors['berjalan'] }} {{ $ang3 }}deg 360deg
            );"></div>
            <div class="absolute w-24 h-24 bg-white rounded-full flex flex-col items-center justify-center shadow-inner">
              <span class="text-[10px] text-slate-400 font-medium">Total</span>
              <span class="text-xs font-extrabold text-slate-800">{{ $grandTotal }}</span>
            </div>
          </div>
          <div class="space-y-2 text-sm">
            <div class="flex items-center gap-2">
              <span class="w-3 h-3 rounded-sm" style="background-color: {{ $statusColors['belum'] }};"></span>
              <span class="text-slate-600">Belum: <strong class="text-slate-800">{{ round($pctBelum, 1) }}%</strong> ({{ $totBelum }})</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="w-3 h-3 rounded-sm" style="background-color: {{ $statusColors['menunggu'] }};"></span>
              <span class="text-slate-600">Menunggu: <strong class="text-slate-800">{{ round($pctMenunggu, 1) }}%</strong> ({{ $totMenunggu }})</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="w-3 h-3 rounded-sm" style="background-color: {{ $statusColors['disetujui'] }};"></span>
              <span class="text-slate-600">Disetujui: <strong class="text-slate-800">{{ round($pctDisetujui, 1) }}%</strong> ({{ $totDisetujui }})</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="w-3 h-3 rounded-sm" style="background-color: {{ $statusColors['berjalan'] }};"></span>
              <span class="text-slate-600">Berjalan: <strong class="text-slate-800">{{ round($pctBerjalan, 1) }}%</strong> ({{ $totBerjalan }})</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
  <div class="px-6 py-5 border-b border-slate-200 flex items-center justify-between">
    <h2 class="text-lg font-bold">Progres IDP</h2>
    <span class="text-xs text-slate-400">View Only - Tidak bisa aksi</span>
  </div>
  <div class="overflow-x-auto">
    <table class="min-w-[1200px] w-full text-left text-xs">
      <thead class="bg-[#31599b] text-white">
        <tr>
          <th class="px-4 py-4 font-semibold">No.</th>
          <th class="px-4 py-4 font-semibold">Nama Bawahan</th>
          <th class="px-4 py-4 font-semibold">Jabatan</th>
          <th class="px-4 py-4 font-semibold">Nama Atasan</th>
          <th class="px-4 py-4 font-semibold">Jabatan Atasan</th>
          <th class="px-4 py-4 font-semibold">Unit Induk</th>
          <th class="px-4 py-4 font-semibold">Status</th>
          <th class="px-4 py-4 font-semibold">Kompetensi</th>
          <th class="px-4 py-4 font-semibold">10% Pembelajaran</th>
          <th class="px-4 py-4 font-semibold">20% Social</th>
          <th class="px-4 py-4 font-semibold">70% Action Learning</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        @forelse ($rows as $index => $row)
          @php($penetapan = $row->rencanaPengembangan->where('status', 'Disetujui'))
          @if($penetapan->isNotEmpty())
            @foreach($penetapan as $rencana)
            <tr class="hover:bg-slate-50">
              @if($loop->first)
              <td class="px-4 py-4 align-top" rowspan="{{ $penetapan->count() }}">{{ $index + 1 }}</td>
              <td class="px-4 py-4 font-medium align-top" rowspan="{{ $penetapan->count() }}">{{ $row->bawahan->nama ?? '-' }}</td>
              <td class="px-4 py-4 align-top" rowspan="{{ $penetapan->count() }}">{{ $row->bawahan->jabatan->sebutan_jabatan ?? '-' }}</td>
              <td class="px-4 py-4 align-top" rowspan="{{ $penetapan->count() }}">{{ $row->atasan->nama ?? '-' }}</td>
              <td class="px-4 py-4 align-top" rowspan="{{ $penetapan->count() }}">{{ $row->atasan->jabatan->sebutan_jabatan ?? '-' }}</td>
              <td class="px-4 py-4 align-top" rowspan="{{ $penetapan->count() }}">{{ $row->bawahan->unit_induk ?? '-' }}</td>
              <td class="px-4 py-4 align-top" rowspan="{{ $penetapan->count() }}">
                <span class="px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-700">Disetujui</span>
              </td>
              @endif
              <td class="px-4 py-4">
                <div class="font-medium">{{ $rencana->kompetensi->kode_kompetensi ?? '-' }}</div>
                <div class="text-slate-500">{{ $rencana->kompetensi->nama_kompetensi ?? '-' }}</div>
              </td>
              <td class="px-4 py-4">{{ $rencana->pembelajaran_10_persen ?? '-' }}</td>
              <td class="px-4 py-4">{{ $rencana->social_learning_20_persen ?? '-' }}</td>
              <td class="px-4 py-4">{{ $rencana->action_learning_70_persen ?? '-' }}</td>
            </tr>
            @endforeach
          @else
            <tr class="hover:bg-slate-50">
              <td class="px-4 py-4">{{ $index + 1 }}</td>
              <td class="px-4 py-4 font-medium">{{ $row->bawahan->nama ?? '-' }}</td>
              <td class="px-4 py-4">{{ $row->bawahan->jabatan->sebutan_jabatan ?? '-' }}</td>
              <td class="px-4 py-4">{{ $row->atasan->nama ?? '-' }}</td>
              <td class="px-4 py-4">{{ $row->atasan->jabatan->sebutan_jabatan ?? '-' }}</td>
              <td class="px-4 py-4">{{ $row->bawahan->unit_induk ?? '-' }}</td>
              <td class="px-4 py-4">
                <span class="px-2 py-1 rounded text-xs font-medium bg-yellow-100 text-yellow-700">Belum Disetujui</span>
              </td>
              <td class="px-4 py-4 text-slate-400" colspan="4">Belum ada penetapan IDP</td>
            </tr>
          @endif
        @empty
        <tr>
          <td colspan="11" class="px-4 py-8 text-center text-slate-500">Belum ada data IDP.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection

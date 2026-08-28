@php($pageTitle = 'Pemantauan Coaching IDP')
@php($activeSection = 'coaching')
@php($activePage = 'pemantauan')

@extends('layouts.app', ['title' => $pageTitle])

@section('content')
@php($units = ['UID S2JB', 'UID LAMPUNG', 'UID SUMBAGSEL', 'UIW BABEL', 'UIP SBS'])
@php($summary = [
  ['label' => 'Belum Mengisi Sesi Coaching', 'values' => [4, 2, 2, 0]],
  ['label' => 'Menunggu Persetujuan', 'values' => [1, 0, 1, 0]],
  ['label' => 'Disetujui', 'values' => [2, 2, 2, 0]],
])
@php($statuses = ['belum direncanakan', 'menunggu persetujuan', 'disetujui'])
@php($statusClasses = ['bg-red-400 text-red-950', 'bg-amber-300 text-amber-950', 'bg-cyan-400 text-cyan-950'])

<div class="relative mb-6 flex h-[220px] items-center overflow-hidden rounded-2xl border border-slate-200 bg-white">
  <div class="absolute inset-0 z-0"><div class="h-full w-full" style="background-image:url('https://lh3.googleusercontent.com/aida-public/AB6AXuCjz6BR9t1xAm_4yTRtjUFCuOHqIkeE5EKFN9IU8hG8k_u3B7psxquSS6Suk591TY9q_Y27C-c3_8Z_HW-o9T4mcs-BSs8NcCHUUxWxaIBP5fVyRf74hO-H-p4ikb76omJMmeg7ue3VDxPp1T-0X1MDCB76X944-1OQyLGojPYj3yPCpw1wjdFvEbWlt83s8VQfKiYaIR8j8f1RfVnP8NMKIKcRo4783TuNbljKJLkY62hs9DzkeU81TZVA3E9SyXdF3Ck'); background-size:cover; background-position:center right;"></div><div class="absolute inset-0 bg-gradient-to-r from-white via-white/85 to-transparent"></div></div>
  <div class="relative z-10 max-w-xl px-8"><h1 class="mb-2 text-[27px] font-bold leading-tight text-[#0a192f]">Pemantauan Coaching IDP</h1><p class="text-[15px] text-slate-500">Pantau progres coaching pegawai pada area Anda.</p></div>
</div>

<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
  <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-6 py-5">
    <h2 class="text-lg font-bold">Progres Coaching IDP</h2>
    <button type="button" class="rounded-lg bg-[#31599b] px-4 py-2 text-xs font-semibold text-white">Export (.xlsx)</button>
  </div>
  <div class="p-6">
  <div class="mb-7 grid gap-8 lg:grid-cols-[minmax(280px,1fr)_minmax(480px,2fr)] lg:items-center">
    <div class="border-[3px] border-violet-600 p-5">
      <div class="space-y-4 text-[8px] text-slate-600">
        @foreach(array_slice($units, 0, 4) as $index => $unit)
          @php($widths = [[28, 18, 30], [28, 12, 25], [42, 10, 25], [28, 15, 25]][$index])
          <div class="grid grid-cols-[50px_1fr] items-center gap-2">
            <span>{{ $unit }}</span>
            <div class="flex h-5 overflow-hidden rounded-r-sm">
              <span class="bg-cyan-800" style="width: {{ $widths[0] }}%"></span><span class="bg-orange-400" style="width: {{ $widths[1] }}%"></span><span class="bg-green-800" style="width: {{ $widths[2] }}%"></span>
            </div>
          </div>
        @endforeach
      </div>
      <div class="mt-5 flex justify-end gap-7 pr-5 text-[7px] text-slate-500"><span>0</span><span>1</span><span>2</span><span>3</span><span>4</span><span>5</span><span>6</span><span>7</span></div>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full min-w-[480px] border-collapse text-center text-xs">
        <thead><tr><th class="border border-slate-500 px-3 py-2"></th>@foreach($units as $unit)<th class="border border-slate-500 px-3 py-2 font-medium">{{ $unit }}</th>@endforeach<th class="border border-slate-500 px-3 py-2 font-medium">TOTAL</th></tr></thead>
        <tbody>
          @foreach($summary as $row)
          <tr><td class="border border-slate-500 px-3 py-2 text-center">{{ $row['label'] }}</td>@foreach($row['values'] as $value)<td class="border border-slate-500 px-3 py-2">{{ $value }}</td>@endforeach<td class="border border-slate-500 px-3 py-2">{{ array_sum($row['values']) }}</td></tr>
          @endforeach
        </tbody>
        <tfoot><tr><td class="border border-slate-500 px-3 py-2"></td>@foreach(range(0, 3) as $column)<td class="border border-slate-500 px-3 py-2">{{ array_sum(array_column(array_column($summary, 'values'), $column)) }}</td>@endforeach<td class="border border-slate-500 px-3 py-2">{{ array_sum(array_map(fn ($row) => array_sum($row['values']), $summary)) }}</td></tr></tfoot>
      </table>
    </div>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-[1300px] w-full border-collapse text-center text-xs">
      <thead class="bg-[#31599b] font-semibold text-white"><tr>
        <th class="border border-slate-500 px-2 py-3">No.</th><th class="border border-slate-500 px-3 py-3">Nama<br>Bawahan /<br>Mentee</th><th class="border border-slate-500 px-3 py-3">Jabatan<br>Bawahan /<br>Mentee</th><th class="border border-slate-500 px-3 py-3">Nama Atasan /<br>Mentor</th><th class="border border-slate-500 px-3 py-3">Jabatan Atasan /<br>Mentor</th><th class="border border-slate-500 px-3 py-3">Unit Induk</th><th class="border border-slate-500 px-3 py-3">Status<br>Perencanaan</th><th class="border border-slate-500 px-3 py-3">10%<br>PEMBELAJARAN<br>(PERENCANAAN)</th><th class="border border-slate-500 px-3 py-3">10 %<br>PEMBELAJARAN<br>(REALISASI)</th><th class="border border-slate-500 px-3 py-3">20%<br>SOCIAL<br>LEARNING</th><th class="border border-slate-500 px-3 py-3">20 % SOCIAL<br>LEARNING<br>(REALISASI)</th><th class="border border-slate-500 px-3 py-3">70% ACTION<br>LEARNING<br>(PERENCANAAN)</th><th class="border border-slate-500 px-3 py-3">70 % ACTION<br>LEARNING<br>(REALISASI)</th>
      </tr></thead>
      <tbody>
        @foreach(range(1, 7) as $index)
        <tr class="h-10"><td class="border border-slate-500 px-2">{{ $index }}</td><td class="border border-slate-500"></td><td class="border border-slate-500"></td><td class="border border-slate-500"></td><td class="border border-slate-500"></td><td class="border border-slate-500 px-2">{{ $units[$index - 1] ?? '' }}</td><td class="border border-slate-500 px-2 {{ $statusClasses[$index - 1] ?? '' }}">{{ $statuses[$index - 1] ?? '' }}</td><td class="border border-slate-500"></td><td class="border border-slate-500"></td><td class="border border-slate-500"></td><td class="border border-slate-500"></td><td class="border border-slate-500"></td><td class="border border-slate-500"></td></tr>
        @endforeach
      </tbody>
    </table>
  </div>
  </div>
</div>
@endsection

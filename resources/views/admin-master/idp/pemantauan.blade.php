@php
$pageTitle = 'Pemantauan IDP';
$activeSection = 'idp';
$activePage = 'pemantauan';
$statuses = ['Draft', 'Diajukan', 'Revisi', 'Disetujui', 'Berjalan', 'Selesai'];
$labels = ['Draft' => 'Belum Direncanakan', 'Diajukan' => 'Menunggu Persetujuan', 'Revisi' => 'Perlu Revisi', 'Disetujui' => 'Disetujui', 'Berjalan' => 'Berjalan', 'Selesai' => 'Selesai'];
$colors = ['Draft' => '#94a3b8', 'Diajukan' => '#f59e0b', 'Revisi' => '#ef4444', 'Disetujui' => '#22c55e', 'Berjalan' => '#31599b', 'Selesai' => '#8b5cf6'];
$classes = ['Draft' => 'bg-slate-100 text-slate-700', 'Diajukan' => 'bg-amber-100 text-amber-700', 'Revisi' => 'bg-red-100 text-red-700', 'Disetujui' => 'bg-green-100 text-green-700', 'Berjalan' => 'bg-blue-100 text-blue-700', 'Selesai' => 'bg-violet-100 text-violet-700'];
$units = $summaryRows->pluck('bawahan.unit_induk')->filter()->unique()->values();
$counts = collect($statuses)->mapWithKeys(fn ($status) => [$status => $summaryRows->filter(fn ($row) => ($row->monitoring?->status_perencanaan ?? 'Draft') === $status)->count()]);
$total = $counts->sum();
$chartMax = max($counts->max(), 1);
@endphp

@extends('layouts.app', ['title' => $pageTitle])

@section('content')
<div class="relative flex h-[220px] items-center overflow-hidden rounded-2xl border border-slate-200 bg-white">
  <div class="absolute inset-0 z-0"><div class="h-full w-full" style="background-image:url('https://lh3.googleusercontent.com/aida-public/AB6AXuCjz6BR9t1xAm_4yTRtjUFCuOHqIkeE5EKFN9IU8hG8k_u3B7psxquSS6Suk591TY9q_Y27C-c3_8Z_HW-o9T4mcs-BSs8NcCHUUxWxaIBP5fVyRf74hO-H-p4ikb76omJMmeg7ue3VDxPp1T-0X1MDCB76X944-1OQyLGojPYj3yPCpw1wjdFvEbWlt83s8VQfKiYaIR8j8f1RfVnP8NMKIKcRo4783TuNbljKJLkY62hs9DzkeU81TZVA3E9SyXdF3Ck'); background-size:cover; background-position:center right;"></div><div class="absolute inset-0 bg-gradient-to-r from-white via-white/85 to-transparent"></div></div>
  <div class="relative z-10 max-w-xl px-8"><h1 class="mb-2 text-[27px] font-bold leading-tight text-[#0a192f]">Pemantauan IDP</h1><p class="text-[15px] text-slate-500">Pantau status rencana pengembangan pegawai.</p></div>
</div>

<div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white">
  <div class="border-b border-slate-200 px-6 py-5"><h2 class="text-lg font-bold">Status Pemantauan IDP per Unit Area</h2><p class="mt-1 text-sm text-slate-500">Data aktual berdasarkan status pemantauan IDP.</p></div>
  <div class="grid grid-cols-12 gap-8 p-6">
    <div class="col-span-6 space-y-5">
      <div class="flex flex-wrap gap-3 rounded-xl border border-slate-100 bg-slate-50 p-3 text-xs">@foreach ($statuses as $status)<span class="flex items-center gap-2"><i class="h-3 w-3 rounded" style="background-color: {{ $colors[$status] }};"></i>{{ $labels[$status] }}</span>@endforeach</div>
      <h3 class="text-sm font-semibold text-slate-800">Total Pemantauan IDP</h3>
      @foreach ($statuses as $status)
        @php($jumlah = $counts[$status])
        <div class="grid grid-cols-[170px_1fr_55px] items-center gap-3"><span class="text-sm font-medium text-slate-600">{{ $labels[$status] }}</span><div class="h-8 overflow-hidden rounded-md bg-slate-100"><div class="flex h-full items-center justify-end rounded-md pr-3 text-sm font-bold text-white" style="width: {{ $jumlah ? max(($jumlah / $chartMax) * 100, 10) : 0 }}%; background-color: {{ $colors[$status] }};">{{ $jumlah }}</div></div><span class="text-right text-sm font-bold text-slate-700">{{ $total ? round(($jumlah / $total) * 100, 1) : 0 }}%</span></div>
      @endforeach
    </div>
    <div class="col-span-6 overflow-x-auto rounded-xl border border-slate-200">
      <table class="w-full text-xs"><thead class="bg-slate-50 text-slate-700"><tr><th class="px-4 py-3 text-left">Unit Area</th>@foreach ($statuses as $status)<th class="px-3 py-3 text-center">{{ $labels[$status] }}</th>@endforeach<th class="bg-slate-100 px-4 py-3 text-center">Total</th></tr></thead><tbody class="divide-y divide-slate-100">
      @forelse ($units as $unit)
        @php($unitCounts = collect($statuses)->mapWithKeys(fn ($status) => [$status => $summaryRows->filter(fn ($row) => $row->bawahan?->unit_induk === $unit && ($row->monitoring?->status_perencanaan ?? 'Draft') === $status)->count()]))
        <tr><td class="px-4 py-3 font-semibold">{{ $unit }}</td>@foreach ($statuses as $status)<td class="px-3 py-3 text-center" style="color: {{ $colors[$status] }};">{{ $unitCounts[$status] }}</td>@endforeach<td class="bg-slate-50 px-4 py-3 text-center font-bold">{{ $unitCounts->sum() }}</td></tr>
      @empty
        <tr><td colspan="8" class="px-4 py-6 text-center text-slate-500">Belum ada data unit.</td></tr>
      @endforelse
      </tbody><tfoot class="bg-slate-100 font-bold"><tr><td class="px-4 py-3">Total</td>@foreach ($statuses as $status)<td class="px-3 py-3 text-center">{{ $counts[$status] }}</td>@endforeach<td class="px-4 py-3 text-center">{{ $total }}</td></tr></tfoot></table>
    </div>
  </div>
</div>

<div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white">
  <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5"><h2 class="text-lg font-bold">Data Pemantauan IDP</h2><span class="text-xs text-slate-400">View Only - Tidak bisa aksi</span></div>
  <div class="overflow-x-auto"><table class="min-w-[1550px] w-full text-left text-xs"><thead class="bg-[#31599b] text-white"><tr><th class="px-4 py-4">No.</th><th class="px-4 py-4">Nama Pegawai</th><th class="px-4 py-4">Jabatan</th><th class="px-4 py-4">Atasan</th><th class="px-4 py-4">Unit</th><th class="px-4 py-4">Periode</th><th class="px-4 py-4">Status</th><th class="px-4 py-4">Kompetensi</th><th class="px-4 py-4">10% Pembelajaran</th><th class="px-4 py-4">20% Social</th><th class="px-4 py-4">70% Action Learning</th><th class="px-4 py-4">Terakhir Diperbarui</th></tr></thead><tbody class="divide-y divide-slate-100">
  @forelse ($rows as $index => $row)
    @php($plans = $row->rencanaPengembangan)
    @php($status = $row->monitoring?->status_perencanaan ?? 'Draft')
    @if ($plans->isNotEmpty())
      @foreach ($plans as $plan)
      <tr class="hover:bg-slate-50">
        @if ($loop->first)
        <td class="px-4 py-4 align-top" rowspan="{{ $plans->count() }}">{{ $rows->firstItem() + $index }}</td><td class="px-4 py-4 align-top font-medium" rowspan="{{ $plans->count() }}">{{ $row->bawahan?->nama ?? '-' }}</td><td class="px-4 py-4 align-top" rowspan="{{ $plans->count() }}">{{ $row->bawahan?->jabatan?->sebutan_jabatan ?? '-' }}</td><td class="px-4 py-4 align-top" rowspan="{{ $plans->count() }}">{{ $row->atasan?->nama ?? '-' }}</td><td class="px-4 py-4 align-top" rowspan="{{ $plans->count() }}">{{ $row->bawahan?->unit_induk ?? '-' }}</td><td class="px-4 py-4 align-top" rowspan="{{ $plans->count() }}">{{ $row->periode_idp ?? '-' }}</td><td class="px-4 py-4 align-top" rowspan="{{ $plans->count() }}"><span class="rounded px-2 py-1 font-medium {{ $classes[$status] }}">{{ $labels[$status] }}</span></td>
        @endif
        <td class="px-4 py-4"><div class="font-medium">{{ $plan->kompetensi?->kode_kompetensi ?? '-' }}</div><div class="text-slate-500">{{ $plan->kompetensi?->nama_kompetensi ?? '-' }}</div></td><td class="px-4 py-4">{{ $plan->pembelajaran_10_persen ?? '-' }}</td><td class="px-4 py-4">{{ $plan->social_learning_20_persen ?? '-' }}</td><td class="px-4 py-4">{{ $plan->action_learning_70_persen ?? '-' }}</td><td class="px-4 py-4">{{ $row->monitoring?->updated_at?->format('d/m/Y H:i') ?? '-' }}</td>
      </tr>
      @endforeach
    @else
      <tr><td class="px-4 py-4">{{ $rows->firstItem() + $index }}</td><td class="px-4 py-4 font-medium">{{ $row->bawahan?->nama ?? '-' }}</td><td class="px-4 py-4">{{ $row->bawahan?->jabatan?->sebutan_jabatan ?? '-' }}</td><td class="px-4 py-4">{{ $row->atasan?->nama ?? '-' }}</td><td class="px-4 py-4">{{ $row->bawahan?->unit_induk ?? '-' }}</td><td class="px-4 py-4">{{ $row->periode_idp ?? '-' }}</td><td class="px-4 py-4"><span class="rounded px-2 py-1 font-medium {{ $classes[$status] }}">{{ $labels[$status] }}</span></td><td colspan="5" class="px-4 py-4 text-slate-400">Belum ada penetapan IDP</td></tr>
    @endif
  @empty
    <tr><td colspan="12" class="px-4 py-8 text-center text-slate-500">Belum ada data IDP.</td></tr>
  @endforelse
  </tbody></table></div>
  @if ($rows->hasPages())<div class="flex items-center justify-between gap-4 border-t border-slate-200 px-6 py-4 text-sm text-slate-500"><span>Menampilkan {{ $rows->firstItem() }}–{{ $rows->lastItem() }} dari {{ $rows->total() }} data</span><div class="flex gap-2"><a href="{{ $rows->previousPageUrl() ?? '#' }}" class="rounded border border-slate-300 px-3 py-1">&lt;</a><span class="rounded border border-slate-300 px-3 py-1">{{ $rows->currentPage() }}/{{ $rows->lastPage() }}</span><a href="{{ $rows->nextPageUrl() ?? '#' }}" class="rounded border border-slate-300 px-3 py-1">&gt;</a></div></div>@endif
</div>
@endsection

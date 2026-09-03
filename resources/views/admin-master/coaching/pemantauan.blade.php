@php($pageTitle = 'Pemantauan Coaching IDP')
@php($activeSection = 'coaching')
@php($activePage = 'pemantauan')
@php($statuses = ['Belum Mengisi Sesi Coaching', 'Menunggu Persetujuan', 'Disetujui'])
@php($statusClasses = ['bg-red-100 text-red-700', 'bg-yellow-100 text-yellow-700', 'bg-green-100 text-green-700'])
@php($units = $summaryRows->pluck('bawahan.unit_induk')->filter()->unique()->values())

@extends('layouts.app', ['title' => $pageTitle])

@section('content')
<div class="relative mb-6 flex h-[220px] items-center overflow-hidden rounded-2xl border border-slate-200 bg-white">
  <div class="absolute inset-0 z-0">
    <div class="h-full w-full" style="background-image:url('https://lh3.googleusercontent.com/aida-public/AB6AXuCjz6BR9t1xAm_4yTRtjUFCuOHqIkeE5EKFN9IU8hG8k_u3B7psxquSS6Suk591TY9q_Y27C-c3_8Z_HW-o9T4mcs-BSs8NcCHUUxWxaIBP5fVyRf74hO-H-p4ikb76omJMmeg7ue3VDxPp1T-0X1MDCB76X944-1OQyLGojPYj3yPCpw1wjdFvEbWlt83s8VQfKiYaIR8j8f1RfVnP8NMKIKcRo4783TuNbljKJLkY62hs9DzkeU81TZVA3E9SyXdF3Ck'); background-size:cover; background-position:center right;"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-white via-white/85 to-transparent"></div>
  </div>
  <div class="relative z-10 max-w-xl px-8"><h1 class="mb-2 text-[27px] font-bold leading-tight text-[#0a192f]">Pemantauan Coaching IDP</h1><p class="text-[15px] text-slate-500">Pantau progres coaching pegawai.</p></div>
</div>

<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
  <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-6 py-5"><h2 class="text-lg font-bold">Progres Coaching IDP</h2><span class="text-xs text-slate-400">View Only - Tidak bisa aksi</span></div>
  @php($statusColors = ['Belum Mengisi Sesi Coaching' => '#94a3b8', 'Menunggu Persetujuan' => '#f59e0b', 'Disetujui' => '#22c55e'])
  @php($statusCounts = collect($statuses)->mapWithKeys(fn ($status) => [$status => $summaryRows->filter(fn ($row) => $row->monitoring?->status_perencanaan === $status || (!$row->monitoring && $status === $statuses[0]))->count()]))
  @php($grandTotal = $statusCounts->sum())
  @php($chartMax = max($statusCounts->max(), 1))
  <div class="grid grid-cols-1 gap-8 border-b border-slate-200 p-6 xl:grid-cols-12 xl:items-stretch">
    <div class="flex flex-col justify-between space-y-6 xl:col-span-6">
      <div class="flex flex-wrap gap-4 rounded-xl border border-slate-100 bg-slate-50 p-3 text-xs">@foreach($statuses as $status)<div class="flex items-center gap-2"><span class="h-3 w-3 rounded" style="background-color: {{ $statusColors[$status] }};"></span>{{ $status }}</div>@endforeach</div>
      <div class="flex flex-grow flex-col justify-center space-y-5 pt-2"><h3 class="text-sm font-semibold text-slate-800">Total Pemantauan Coaching</h3><div class="space-y-4">@foreach($statuses as $status)@php($count = $statusCounts[$status])@php($percent = $grandTotal ? round(($count / $grandTotal) * 100, 1) : 0)<div class="grid grid-cols-[180px_1fr_55px] items-center gap-3"><span class="text-sm font-medium text-slate-600">{{ $status }}</span><div class="relative flex h-8 items-center overflow-hidden rounded-md bg-slate-100"><div class="flex h-full items-center justify-end rounded-md pr-3 text-sm font-bold text-white" style="width: {{ $count ? max(($count / $chartMax) * 100, 10) : 0 }}%; background-color: {{ $statusColors[$status] }};">{{ $count }}</div></div><span class="text-right text-sm font-bold text-slate-700">{{ $percent }}%</span></div>@endforeach</div></div>
    </div>
    <div class="h-full xl:col-span-6"><div class="h-full overflow-x-auto rounded-xl border border-slate-200 shadow-sm"><table data-table-search="false" data-table-scroll="false" class="h-full w-full border-collapse text-xs"><thead class="border-b border-slate-200 bg-slate-50 text-slate-700"><tr><th class="px-4 py-3 text-left font-bold">Unit Area</th>@foreach($statuses as $status)<th class="px-3 py-3 text-center font-semibold">{{ $status }}</th>@endforeach<th class="border-l border-slate-200 bg-slate-100 px-4 py-3 text-center font-bold text-slate-800">Total</th></tr></thead><tbody class="divide-y divide-slate-100 bg-white">@foreach($units as $unit)@php($unitCounts = collect($statuses)->mapWithKeys(fn ($status) => [$status => $summaryRows->filter(fn ($row) => $row->bawahan?->unit_induk === $unit && ($row->monitoring?->status_perencanaan === $status || (!$row->monitoring && $status === $statuses[0])))->count()]))<tr class="transition-colors hover:bg-slate-50/80"><td class="px-4 py-3 font-semibold text-slate-800">{{ $unit }}</td>@foreach($statuses as $status)<td class="px-3 py-3 text-center font-semibold" style="color: {{ $statusColors[$status] }};">{{ $unitCounts[$status] }}</td>@endforeach<td class="border-l border-slate-200 bg-slate-50 px-4 py-3 text-center font-bold text-slate-900">{{ $unitCounts->sum() }}</td></tr>@endforeach</tbody><tfoot class="border-t-2 border-slate-200 bg-slate-100/80 font-bold"><tr><td class="px-4 py-3 font-bold text-slate-800">Total All</td>@foreach($statuses as $status)<td class="px-3 py-3 text-center text-slate-700">{{ $statusCounts[$status] }}</td>@endforeach<td class="border-l border-slate-200 bg-slate-200/60 px-4 py-3 text-center font-extrabold text-slate-900">{{ $grandTotal }}</td></tr></tfoot></table></div></div>
  </div>
  <div class="overflow-x-auto">
    <table data-table-pagination="false" class="min-w-[1850px] w-full text-left text-xs">
      <colgroup><col class="w-14"><col class="w-44"><col class="w-48"><col class="w-44"><col class="w-48"><col class="w-28"><col class="w-44"><col class="w-52"><col class="w-52"><col class="w-52"><col class="w-52"><col class="w-52"><col class="w-52"><col class="w-52"></colgroup>
      <thead class="bg-[#31599b] text-white">
        <tr><th class="px-4 py-4 font-semibold">No.</th><th class="px-4 py-4 font-semibold">Nama Mentee</th><th class="px-4 py-4 font-semibold">Jabatan Mentee</th><th class="px-4 py-4 font-semibold">Nama Mentor</th><th class="px-4 py-4 font-semibold">Jabatan Mentor</th><th class="px-4 py-4 font-semibold">Unit Induk</th><th class="px-4 py-4 font-semibold">Status</th><th class="px-4 py-4 font-semibold">Kompetensi</th><th class="px-4 py-4 font-semibold">10% Perencanaan</th><th class="px-4 py-4 font-semibold">10% Realisasi</th><th class="px-4 py-4 font-semibold">20% Perencanaan</th><th class="px-4 py-4 font-semibold">20% Realisasi</th><th class="px-4 py-4 font-semibold">70% Perencanaan</th><th class="px-4 py-4 font-semibold">70% Realisasi</th></tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($rows as $index => $row)
          @php($plans = $row->rencanaPengembangan)
          @php($status = $row->monitoring?->status_perencanaan ?? 'Belum Mengisi Sesi Coaching')
          @if($plans->isNotEmpty())
            @foreach($plans as $plan)
            <tr class="hover:bg-slate-50">
              @if($loop->first)
              <td class="px-4 py-4 align-top" rowspan="{{ $plans->count() }}">{{ $rows->firstItem() + $index }}</td><td class="px-4 py-4 font-medium align-top" rowspan="{{ $plans->count() }}">{{ $row->bawahan?->nama ?? '-' }}</td><td class="px-4 py-4 align-top" rowspan="{{ $plans->count() }}">{{ $row->bawahan?->jabatan?->sebutan_jabatan ?? '-' }}</td><td class="px-4 py-4 align-top" rowspan="{{ $plans->count() }}">{{ $row->atasan?->nama ?? '-' }}</td><td class="px-4 py-4 align-top" rowspan="{{ $plans->count() }}">{{ $row->atasan?->jabatan?->sebutan_jabatan ?? '-' }}</td><td class="px-4 py-4 align-top" rowspan="{{ $plans->count() }}">{{ $row->bawahan?->unit_induk ?? '-' }}</td><td class="px-4 py-4 align-top" rowspan="{{ $plans->count() }}"><span class="rounded px-2 py-1 font-medium {{ $statusClasses[array_search($status, $statuses, true)] ?? 'bg-slate-100 text-slate-700' }}">{{ $status }}</span></td>
              @endif
              <td class="px-4 py-4"><div class="font-medium">{{ $plan->kompetensi?->kode_kompetensi ?? '-' }}</div><div class="text-slate-500">{{ $plan->kompetensi?->nama_kompetensi ?? '-' }}</div></td><td class="px-4 py-4">{{ $plan->pembelajaran_10_persen ?? '-' }}</td><td class="px-4 py-4">@php($bukti10 = $plan->coachingBukti->firstWhere('jenis', 10)) @if($bukti10)<a class="font-semibold text-blue-600 hover:underline" href="{{ route(auth()->user()->role === 'admin_area' ? 'admin-area.coaching.download' : 'admin-master.coaching.download', ['idp' => $row, 'type' => 10, 'idRencana' => $plan]) }}">PDF</a>@else-@endif</td><td class="px-4 py-4">{{ $plan->social_learning_20_persen ?? '-' }}</td><td class="px-4 py-4">@php($bukti20 = $plan->coachingBukti->firstWhere('jenis', 20)) @if($bukti20)<a class="font-semibold text-blue-600 hover:underline" href="{{ route(auth()->user()->role === 'admin_area' ? 'admin-area.coaching.download' : 'admin-master.coaching.download', ['idp' => $row, 'type' => 20, 'idRencana' => $plan]) }}">PDF</a>@else-@endif</td><td class="px-4 py-4">{{ $plan->action_learning_70_persen ?? '-' }}</td><td class="px-4 py-4">@php($bukti70 = $plan->coachingBukti->firstWhere('jenis', 70)) @if($bukti70)<a class="font-semibold text-blue-600 hover:underline" href="{{ route(auth()->user()->role === 'admin_area' ? 'admin-area.coaching.download' : 'admin-master.coaching.download', ['idp' => $row, 'type' => 70, 'idRencana' => $plan]) }}">PDF</a>@else-@endif</td>
            </tr>
            @endforeach
          @else
            <tr class="hover:bg-slate-50"><td class="px-4 py-4">{{ $rows->firstItem() + $index }}</td><td class="px-4 py-4 font-medium">{{ $row->bawahan?->nama ?? '-' }}</td><td class="px-4 py-4">{{ $row->bawahan?->jabatan?->sebutan_jabatan ?? '-' }}</td><td class="px-4 py-4">{{ $row->atasan?->nama ?? '-' }}</td><td class="px-4 py-4">{{ $row->atasan?->jabatan?->sebutan_jabatan ?? '-' }}</td><td class="px-4 py-4">{{ $row->bawahan?->unit_induk ?? '-' }}</td><td class="px-4 py-4"><span class="rounded px-2 py-1 font-medium {{ $statusClasses[array_search($status, $statuses, true)] ?? 'bg-slate-100 text-slate-700' }}">{{ $status }}</span></td><td colspan="7" class="px-4 py-4 text-slate-400">Belum ada penetapan IDP</td></tr>
          @endif
        @empty
          <tr><td colspan="14" class="px-4 py-8 text-center text-slate-500">Belum ada data coaching.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($rows->hasPages())
  <div class="mt-4 flex items-center justify-between gap-4 px-6 text-sm text-slate-500">
    <span>Menampilkan {{ $rows->firstItem() }}–{{ $rows->lastItem() }} dari {{ $rows->total() }} data</span>
    <div class="flex flex-wrap items-center gap-2"><a href="{{ $rows->url(1) }}" @class(['rounded border border-slate-300 px-3 py-1', 'pointer-events-none opacity-50' => $rows->onFirstPage()])>&lt;&lt;</a><a href="{{ $rows->previousPageUrl() ?? '#' }}" @class(['rounded border border-slate-300 px-3 py-1', 'pointer-events-none opacity-50' => $rows->onFirstPage()])>&lt;</a><select aria-label="Pilih halaman" class="rounded border-slate-300 py-1 text-sm focus:border-[#31599b] focus:ring-[#31599b]" onchange="window.location.href=this.value">@foreach(range(1, $rows->lastPage()) as $page)<option value="{{ $rows->url($page) }}" @selected($rows->currentPage() === $page)>{{ $page }}</option>@endforeach</select><a href="{{ $rows->nextPageUrl() ?? '#' }}" @class(['rounded border border-slate-300 px-3 py-1', 'pointer-events-none opacity-50' => !$rows->hasMorePages()])>&gt;</a><a href="{{ $rows->url($rows->lastPage()) }}" @class(['rounded border border-slate-300 px-3 py-1', 'pointer-events-none opacity-50' => !$rows->hasMorePages()])>&gt;&gt;</a></div>
  </div>
  @endif
</div>
@endsection

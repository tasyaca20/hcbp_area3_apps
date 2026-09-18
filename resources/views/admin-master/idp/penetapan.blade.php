@php($pageTitle = 'Penetapan IDP')
@php($heroTitle = 'Penetapan IDP')
@php($heroSubtitle = 'Pantau data rencana pengembangan pegawai yang telah ditetapkan.')
@php($cardTitle = 'Data Penetapan IDP')
@php($activeSection = 'idp')
@php($activePage = 'penetapan')
@php($totalIdp = $rows->count())
@php($idpDisetujui = $rows->filter(fn ($row) => $row->rencanaPengembangan->isNotEmpty())->count())
@php($idpBelumDisetujui = $totalIdp - $idpDisetujui)
@php($chartMax = max($totalIdp, 1))

@extends('layouts.app', ['title' => $pageTitle])

@section('content')
<div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white h-[220px] flex items-center">
  <div class="absolute inset-0 z-0"><div class="w-full h-full" style="background-image: url(&quot;https://lh3.googleusercontent.com/aida-public/AB6AXuCjz6BR9t1xAm_4yTRtjUFCuOHqIkeE5EKFN9IU8hG8k_u3B7psxquSS6Suk591TY9q_Y27C-c3_8Z_HW-o9T4mcs-BSs8NcCHUUxWxaIBP5fVyRf74hO-H-p4ikb76omJMmeg7ue3VDxPp1T-0X1MDCB76X944-1OQyLGojPYj3yPCpw1wjdFvEbWlt83s8VQfKiYaIR8j8f1RfVnP8NMKIKcRo4783TuNbljKJLkY62hs9DzkeU81TZVA3E9SyXdF3Ck&quot;); background-size: cover; background-position: center right;"></div><div class="absolute inset-0 bg-gradient-to-r from-white via-white/85 to-transparent"></div></div>
  <div class="relative z-10 px-8 max-w-xl"><h1 class="text-[27px] font-bold text-[#0a192f] mb-2 leading-tight">{{ $heroTitle }}</h1><p class="text-slate-500 text-[15px]">{{ $heroSubtitle }}</p></div>
</div>

<div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white p-6">
  <div class="mb-6"><h2 class="text-lg font-bold">Ringkasan Penetapan IDP</h2><p class="mt-1 text-sm text-slate-500">Data berdasarkan rencana kompetensi berstatus Disetujui.</p></div>
  <div class="grid grid-cols-2 gap-6">
    <div class="space-y-5"><h3 class="text-sm font-semibold text-slate-800">Status Penetapan</h3>@foreach(['Disetujui' => [$idpDisetujui, '#22c55e'], 'Belum Disetujui' => [$idpBelumDisetujui, '#f59e0b']] as $status => [$jumlah, $warna])<div class="grid grid-cols-[150px_1fr_45px] items-center gap-3"><span class="text-sm font-medium text-slate-600">{{ $status }}</span><div class="h-8 overflow-hidden rounded-md bg-slate-100"><div class="flex h-full items-center justify-end rounded-md pr-3 text-sm font-bold text-white" style="width: {{ $jumlah ? max(($jumlah / $chartMax) * 100, 10) : 0 }}%; background-color: {{ $warna }};">{{ $jumlah }}</div></div><span class="text-right text-sm font-bold text-slate-700">{{ $totalIdp ? round(($jumlah / $totalIdp) * 100, 1) : 0 }}%</span></div>@endforeach</div>
    <div class="overflow-hidden rounded-xl border border-slate-200"><table class="w-full text-sm"><thead class="bg-slate-50 text-slate-700"><tr><th class="px-4 py-3 text-left">Status</th><th class="px-4 py-3 text-center">Jumlah IDP</th></tr></thead><tbody class="divide-y divide-slate-100"><tr><td class="px-4 py-3 font-medium">Disetujui</td><td class="px-4 py-3 text-center font-semibold text-green-700">{{ $idpDisetujui }}</td></tr><tr><td class="px-4 py-3 font-medium">Belum Disetujui</td><td class="px-4 py-3 text-center font-semibold text-amber-700">{{ $idpBelumDisetujui }}</td></tr></tbody><tfoot class="bg-slate-100 font-bold"><tr><td class="px-4 py-3">Total</td><td class="px-4 py-3 text-center">{{ $totalIdp }}</td></tr></tfoot></table></div>
  </div>
</div>

<div class="mt-6 bg-white border border-slate-200 rounded-2xl overflow-hidden">
  <div class="flex items-center justify-between gap-4 px-6 py-5 border-b border-slate-200"><h2 class="text-lg font-bold">{{ $cardTitle }}</h2><span class="text-xs text-slate-400">View Only - Tidak bisa aksi</span></div>
  <div class="overflow-x-auto"><table class="min-w-[1200px] w-full text-left text-xs"><thead class="bg-[#31599b] text-white"><tr><th class="px-4 py-4 font-semibold">No.</th><th class="px-4 py-4 font-semibold">Nama Pegawai</th><th class="px-4 py-4 font-semibold">Jabatan</th><th class="px-4 py-4 font-semibold">Nama Atasan</th><th class="px-4 py-4 font-semibold">NIP Atasan</th><th class="px-4 py-4 font-semibold">Jabatan Atasan</th><th class="px-4 py-4 font-semibold">Unit Induk</th><th class="px-4 py-4 font-semibold">Status</th><th class="px-4 py-4 font-semibold">Kompetensi</th><th class="px-4 py-4 font-semibold">10% Pembelajaran</th><th class="px-4 py-4 font-semibold">20% Social</th><th class="px-4 py-4 font-semibold">70% Action Learning</th></tr></thead><tbody class="divide-y divide-slate-100">
  @forelse ($rows as $index => $row)
    @php($penetapan = $row->rencanaPengembangan)
    @if($penetapan->isNotEmpty())
      @foreach($penetapan as $rencana)<tr class="hover:bg-slate-50">@if($loop->first)<td class="px-4 py-4 align-top" rowspan="{{ $penetapan->count() }}">{{ $index + 1 }}</td><td class="px-4 py-4 font-medium align-top" rowspan="{{ $penetapan->count() }}">{{ $row->bawahan?->nama ?? '-' }}</td><td class="px-4 py-4 align-top" rowspan="{{ $penetapan->count() }}">{{ $row->bawahan?->jabatan?->sebutan_jabatan ?? '-' }}</td><td class="px-4 py-4 align-top" rowspan="{{ $penetapan->count() }}">{{ $row->atasan?->nama ?? '-' }}</td><td class="px-4 py-4 align-top" rowspan="{{ $penetapan->count() }}">{{ $row->atasan?->nip ?? '-' }}</td><td class="px-4 py-4 align-top" rowspan="{{ $penetapan->count() }}">{{ $row->atasan?->jabatan?->sebutan_jabatan ?? '-' }}</td><td class="px-4 py-4 align-top" rowspan="{{ $penetapan->count() }}">{{ $row->bawahan?->unit_induk ?? '-' }}</td><td class="px-4 py-4 align-top" rowspan="{{ $penetapan->count() }}"><span class="px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-700">Disetujui</span></td>@endif<td class="px-4 py-4"><div class="font-medium">{{ $rencana->kompetensi?->kode_kompetensi ?? '-' }}</div><div class="text-slate-500">{{ $rencana->kompetensi?->nama_kompetensi ?? '-' }}</div></td><td class="px-4 py-4">{{ $rencana->pembelajaran_10_persen ?? '-' }}</td><td class="px-4 py-4">{{ $rencana->social_learning_20_persen ?? '-' }}</td><td class="px-4 py-4">{{ $rencana->action_learning_70_persen ?? '-' }}</td></tr>@endforeach
    @else
      <tr class="hover:bg-slate-50"><td class="px-4 py-4">{{ $index + 1 }}</td><td class="px-4 py-4 font-medium">{{ $row->bawahan?->nama ?? '-' }}</td><td class="px-4 py-4">{{ $row->bawahan?->jabatan?->sebutan_jabatan ?? '-' }}</td><td class="px-4 py-4">{{ $row->atasan?->nama ?? '-' }}</td><td class="px-4 py-4">{{ $row->atasan?->nip ?? '-' }}</td><td class="px-4 py-4">{{ $row->atasan?->jabatan?->sebutan_jabatan ?? '-' }}</td><td class="px-4 py-4">{{ $row->bawahan?->unit_induk ?? '-' }}</td><td class="px-4 py-4"><span class="px-2 py-1 rounded text-xs font-medium bg-yellow-100 text-yellow-700">Belum Disetujui</span></td><td class="px-4 py-4 text-slate-400" colspan="4">Belum ada penetapan IDP</td></tr>
    @endif
  @empty<tr><td colspan="12" class="px-4 py-8 text-center text-slate-500">Belum ada data IDP.</td></tr>@endforelse
  </tbody></table></div>
  <div class="border-t border-slate-200 px-6 py-4 text-xs text-slate-500">Data penetapan Individual Development Plan.</div>
</div>
@endsection

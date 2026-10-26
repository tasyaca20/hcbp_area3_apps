@php($pageTitle = 'Coaching Bawahan')
@php($activeSection = 'coaching')
@php($activePage = 'coaching')
@php($hasSearch = request()->filled('search'))

@extends('layouts.app', ['title' => $pageTitle])

@section('content')
<div class="relative flex h-[220px] items-center overflow-hidden rounded-2xl border border-slate-200 bg-white">
  <div class="absolute inset-0 z-0"><div class="h-full w-full" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCjz6BR9t1xAm_4yTRtjUFCuOHqIkeE5EKFN9IU8hG8k_u3B7psxquSS6Suk591TY9q_Y27C-c3_8Z_HW-o9T4mcs-BSs8NcCHUUxWxaIBP5fVyRf74hO-H-p4ikb76omJMmeg7ue3VDxPp1T-0X1MDCB76X944-1OQyLGojPYj3yPCpw1wjdFvEbWlt83s8VQfKiYaIR8j8f1RfVnP8NMKIKcRo4783TuNbljKJLkY62hs9DzkeU81TZVA3E9SyXdF3Ck'); background-size: cover; background-position: center right;"></div><div class="absolute inset-0 bg-gradient-to-r from-white via-white/85 to-transparent"></div></div>
  <div class="relative z-10 max-w-xl px-8"><h1 class="mb-2 text-[27px] font-bold leading-tight text-[#0a192f]">Coaching Bawahan</h1><p class="text-[15px] text-slate-500">Pantau dan tinjau realisasi coaching bawahan.</p></div>
</div>

<div class="mt-6 rounded-2xl border border-slate-200 bg-white p-6">
  <div class="mb-5"><h2 class="text-lg font-bold text-slate-900">Pencarian Data Coaching</h2><p class="mt-1 text-sm text-slate-500">Cari kompetensi, pembelajaran, realisasi, atau data coaching bawahan.</p></div>
  <form method="GET" data-search class="flex flex-col gap-3 sm:flex-row sm:items-center">
    <div class="relative flex-1"><svg aria-hidden="true" class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m1.35-5.65a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" /></svg><input name="search" value="{{ request('search') }}" placeholder="Cari kompetensi, pembelajaran, atau data coaching..." class="w-full rounded-lg border-slate-300 py-2.5 pl-10 pr-3 text-sm focus:border-[#31599b] focus:ring-[#31599b]"></div>
    <div class="flex gap-2"><button class="rounded-lg bg-[#31599b] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#27487d]">Cari</button>@if($hasSearch)<a href="{{ route('atasan.coaching.index') }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Reset</a>@endif</div>
  </form>
</div>

@if($rows->total())<p class="mt-5 text-sm text-slate-500">Menampilkan {{ $rows->count() }} dari {{ $rows->total() }} data coaching.</p>@endif

@forelse($rows as $row)
<div class="mt-5 rounded-2xl border border-slate-200 bg-white p-6">
  <div class="border-b border-slate-200 pb-5"><h2 class="text-lg font-bold text-slate-900">Individual Development Program (IDP)</h2><p class="mt-1 text-sm text-slate-500">Coaching - <i>Talent Home Coming</i></p></div>
  <div class="grid gap-x-8 gap-y-3 py-5 text-sm sm:grid-cols-2"><div class="space-y-2"><p><span class="font-semibold text-slate-700">Nama Bawahan:</span> {{ $row->bawahan?->nama ?? '-' }}</p><p><span class="font-semibold text-slate-700">NIP:</span> {{ $row->bawahan?->nip ?? '-' }}</p><p><span class="font-semibold text-slate-700">Jabatan:</span> {{ $row->bawahan?->jabatan?->sebutan_jabatan ?? '-' }}</p></div><div class="space-y-2"><p><span class="font-semibold text-slate-700">Nama Atasan:</span> {{ $row->atasan?->nama ?? '-' }}</p><p><span class="font-semibold text-slate-700">NIP Atasan:</span> {{ $row->atasan?->nip ?? '-' }}</p><p><span class="font-semibold text-slate-700">Jabatan Atasan:</span> {{ $row->atasan?->jabatan?->sebutan_jabatan ?? '-' }}</p></div></div>
  <div class="overflow-x-auto rounded-xl border border-slate-200"><table data-table-pagination="false" class="min-w-[1400px] w-full text-left text-sm tbl"><thead><tr><th class="w-14">No</th><th class="min-w-56">Kompetensi Teknis</th><th class="min-w-52">10% Pembelajaran<br><span class="font-normal">Perencanaan</span></th><th class="min-w-44">10% Pembelajaran<br><span class="font-normal">Realisasi</span></th><th class="min-w-52">20% Social Learning<br><span class="font-normal">Perencanaan</span></th><th class="min-w-44">20% Social Learning<br><span class="font-normal">Realisasi</span></th><th class="min-w-52">70% Action Learning<br><span class="font-normal">Perencanaan</span></th><th class="min-w-44">70% Action Learning<br><span class="font-normal">Realisasi</span></th><th class="min-w-48">Status Atasan</th></tr></thead><tbody class="divide-y divide-slate-100">
    @forelse($row->rencanaPengembangan as $index => $plan)
    <tr class="align-top transition-colors odd:bg-white even:bg-slate-50/60 hover:bg-blue-50/50"><td>{{ $index + 1 }}</td><td><div class="font-semibold text-slate-800">{{ $plan->kompetensi?->kode_kompetensi ?? '-' }}</div><div class="mt-1 leading-5 text-slate-600">{{ $plan->kompetensi?->nama_kompetensi ?? '-' }}</div></td><td class="whitespace-pre-line leading-5 text-slate-700">{{ $plan->pembelajaran_10_persen ?? '-' }}</td><td>@include('atasan.coaching.realisasi', ['bukti' => $plan->coachingBukti->where('jenis', 10)->first(), 'jenis' => 10])</td><td class="whitespace-pre-line leading-5 text-slate-700">{{ $plan->social_learning_20_persen ?? '-' }}</td><td>@include('atasan.coaching.realisasi', ['bukti' => $plan->coachingBukti->where('jenis', 20)->first(), 'jenis' => 20])</td><td class="whitespace-pre-line leading-5 text-slate-700">{{ $plan->action_learning_70_persen ?? '-' }}</td><td>@include('atasan.coaching.realisasi', ['bukti' => $plan->coachingBukti->where('jenis', 70)->first(), 'jenis' => 70])</td><td>@foreach([10, 20, 70] as $jenis) @php($bukti = $plan->coachingBukti->where('jenis', $jenis)->first()) <div class="mb-3 last:mb-0"><span class="font-semibold">{{ $jenis }}%:</span> @if($bukti) @include('atasan.coaching.review', ['bukti' => $bukti]) @else <span class="text-slate-500">Belum ada bukti</span> @endif</div> @endforeach</td></tr>
    @empty
    <tr><td colspan="9" class="py-10 text-center text-slate-500">Tidak ada data yang cocok dengan pencarian Anda.</td></tr>
    @endforelse
  </tbody></table></div>
</div>
@empty
<div class="mt-6 rounded-2xl border border-slate-200 bg-white px-6 py-12 text-center"><p class="font-semibold text-slate-700">{{ $hasSearch ? 'Tidak ada data yang cocok dengan pencarian Anda.' : 'Belum ada rencana coaching.' }}</p>@if($hasSearch)<a href="{{ route('atasan.coaching.index') }}" class="mt-3 inline-block text-sm font-semibold text-[#31599b] hover:underline">Reset Pencarian</a>@endif</div>
@endforelse

@if($rows->hasPages())<div class="mt-6">@include('components.pagination', ['paginator' => $rows])</div>@endif
@endsection

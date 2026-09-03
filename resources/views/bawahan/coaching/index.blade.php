@php($pageTitle = 'Coaching Saya')
@php($activeSection = 'coaching')
@php($activePage = 'coaching')

@extends('layouts.app', ['title' => $pageTitle])

@section('content')
<div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white h-[220px] flex items-center">
  <div class="absolute inset-0 z-0"><div class="w-full h-full" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCjz6BR9t1xAm_4yTRtjUFCuOHqIkeE5EKFN9IU8hG8k_u3B7psxquSS6Suk591TY9q_Y27C-c3_8Z_HW-o9T4mcs-BSs8NcCHUUxWxaIBP5fVyRf74hO-H-p4ikb76omJMmeg7ue3VDxPp1T-0X1MDCB76X944-1OQyLGojPYj3yPCpw1wjdFvEbWlt83s8VQfKiYaIR8j8f1RfVnP8NMKIKcRo4783TuNbljKJLkY62hs9DzkeU81TZVA3E9SyXdF3Ck'); background-size: cover; background-position: center right;"></div><div class="absolute inset-0 bg-gradient-to-r from-white via-white/85 to-transparent"></div></div>
  <div class="relative z-10 px-8 max-w-xl"><h1 class="text-[27px] font-bold text-[#0a192f] mb-2 leading-tight">Coaching Saya</h1><p class="text-slate-500 text-[15px]">Pantau progres coaching dan unggah bukti kegiatan.</p></div>
</div>

@forelse($rows as $row)
<div class="mt-6 overflow-hidden rounded-[2rem] border border-slate-200 bg-white p-8">
  <h1 class="text-lg font-bold text-slate-900">Individual Development Program (IDP) - <i>Talent Home Coming</i></h1>
  <p class="mt-1 text-sm text-slate-600">Coaching</p>
  <div class="mt-4 grid gap-4 text-base font-semibold sm:grid-cols-2">
    <div class="space-y-2"><p>Nama Bawahan: <span class="font-normal">{{ $row->bawahan?->nama ?? '-' }}</span></p><p>NIP: <span class="font-normal">{{ $row->bawahan?->nip ?? '-' }}</span></p><p>Jabatan: <span class="font-normal">{{ $row->bawahan?->jabatan?->sebutan_jabatan ?? '-' }}</span></p></div>
    <div class="space-y-2"><p>Nama Atasan: <span class="font-normal">{{ $row->atasan?->nama ?? '-' }}</span></p><p>NIP: <span class="font-normal">{{ $row->atasan?->nip ?? '-' }}</span></p><p>Jabatan Atasan: <span class="font-normal">{{ $row->atasan?->jabatan?->sebutan_jabatan ?? '-' }}</span></p></div>
  </div>
  <div class="mt-6 overflow-x-auto"><table class="min-w-[1200px] w-full text-left text-sm"><thead class="bg-[#31599b] text-white"><tr><th class="px-4 py-4">No</th><th class="px-4 py-4">Kompetensi Teknis</th><th class="px-4 py-4">10% Pembelajaran<br>(Perencanaan)</th><th class="px-4 py-4">10% Pembelajaran<br>(Realisasi)</th><th class="px-4 py-4">20% Social Learning<br>(Perencanaan)</th><th class="px-4 py-4">20% Social Learning<br>(Realisasi)</th><th class="px-4 py-4">70% Action Learning<br>(Perencanaan)</th><th class="px-4 py-4">70% Action Learning<br>(Realisasi)</th></tr></thead><tbody class="divide-y divide-slate-100">
  @forelse($row->rencanaPengembangan->where('status', 'Disetujui') as $index => $plan)
  <tr class="align-top"><td class="border border-slate-700 px-3 py-2 text-center">{{ $index + 1 }}</td><td class="border border-slate-700 px-3 py-2"><div class="font-semibold">{{ $plan->kompetensi?->kode_kompetensi ?? '-' }}</div><div>{{ $plan->kompetensi?->nama_kompetensi ?? '-' }}</div></td><td class="border border-slate-700 px-3 py-2">{{ $plan->pembelajaran_10_persen ?? 'Belum dibuat' }}</td><td class="border border-slate-700 px-3 py-2">@include('bawahan.coaching.download-status', ['bukti' => $plan->coachingBukti->where('jenis', 10)->first(), 'jenis' => 10])</td><td class="border border-slate-700 px-3 py-2">{{ $plan->social_learning_20_persen ?? 'Belum dibuat' }}</td><td class="border border-slate-700 px-3 py-2">@include('bawahan.coaching.download-status', ['bukti' => $plan->coachingBukti->where('jenis', 20)->first(), 'jenis' => 20])</td><td class="border border-slate-700 px-3 py-2">{{ $plan->action_learning_70_persen ?? 'Belum dibuat' }}</td><td class="border border-slate-700 px-3 py-2">@include('bawahan.coaching.download-status', ['bukti' => $plan->coachingBukti->where('jenis', 70)->first(), 'jenis' => 70])</td></tr>
  @empty
  <tr><td colspan="8" class="px-4 py-8 text-center text-slate-500">Belum ada rencana yang disetujui.</td></tr>
  @endforelse
  </tbody></table></div>
</div>
@empty
<div class="mt-6 rounded-2xl border border-slate-200 bg-white px-6 py-12 text-center text-slate-500">Belum ada data coaching.</div>
@endforelse
@endsection

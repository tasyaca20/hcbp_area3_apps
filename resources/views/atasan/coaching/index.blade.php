@php($pageTitle = 'Coaching Bawahan')
@php($activeSection = 'coaching')
@php($activePage = 'coaching')

@extends('layouts.app', ['title' => $pageTitle])

@section('content')
<div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white h-[220px] flex items-center">
  <div class="absolute inset-0 z-0"><div class="w-full h-full" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCjz6BR9t1xAm_4yTRtjUFCuOHqIkeE5EKFN9IU8hG8k_u3B7psxquSS6Suk591TY9q_Y27C-c3_8Z_HW-o9T4mcs-BSs8NcCHUUxWxaIBP5fVyRf74hO-H-p4ikb76omJMmeg7ue3VDxPp1T-0X1MDCB76X944-1OQyLGojPYj3yPCpw1wjdFvEbWlt83s8VQfKiYaIR8j8f1RfVnP8NMKIKcRo4783TuNbljKJLkY62hs9DzkeU81TZVA3E9SyXdF3Ck'); background-size: cover; background-position: center right;"></div><div class="absolute inset-0 bg-gradient-to-r from-white via-white/85 to-transparent"></div></div>
  <div class="relative z-10 px-8 max-w-xl"><h1 class="text-[27px] font-bold text-[#0a192f] mb-2 leading-tight">Coaching Bawahan</h1><p class="text-slate-500 text-[15px]">Pantau dan kelola coaching bawahan Anda.</p></div>
</div>

@forelse($rows as $row)
<div class="mt-6 overflow-hidden rounded-[2rem] border border-slate-200 bg-white p-8">
  <h1 class="text-lg font-bold text-slate-900">Individual Development Program (IDP) - <i>Talent Home Coming</i></h1>
  <p class="mt-1 text-sm text-slate-600">Coaching</p>
  <div class="mt-4 grid gap-4 text-base font-semibold sm:grid-cols-2">
    <div class="space-y-2"><p>Nama Bawahan: <span class="font-normal">{{ $row->bawahan?->nama ?? '-' }}</span></p><p>NIP: <span class="font-normal">{{ $row->bawahan?->nip ?? '-' }}</span></p><p>Jabatan: <span class="font-normal">{{ $row->bawahan?->jabatan?->sebutan_jabatan ?? '-' }}</span></p><p>Unit Induk: <span class="font-normal">{{ $row->bawahan?->unit_induk ?? '-' }}</span></p></div>
    <div class="space-y-2"><p>Nama Atasan: <span class="font-normal">{{ $row->atasan?->nama ?? '-' }}</span></p><p>NIP: <span class="font-normal">{{ $row->atasan?->nip ?? '-' }}</span></p><p>Jabatan Atasan: <span class="font-normal">{{ $row->atasan?->jabatan?->sebutan_jabatan ?? '-' }}</span></p></div>
  </div>
  <div class="mt-6 overflow-x-auto"><table class="min-w-[1200px] w-full text-left text-sm"><thead class="bg-[#31599b] text-white"><tr><th class="px-4 py-4">No</th><th class="px-4 py-4">Kompetensi Teknis</th><th class="px-4 py-4">10% Pembelajaran<br>(Perencanaan)</th><th class="px-4 py-4">10% Pembelajaran<br>(Realisasi)</th><th class="px-4 py-4">20% Social Learning<br>(Perencanaan)</th><th class="px-4 py-4">20% Social Learning<br>(Realisasi)</th><th class="px-4 py-4">70% Action Learning<br>(Perencanaan)</th><th class="px-4 py-4">70% Action Learning<br>(Realisasi)</th></tr></thead><tbody class="divide-y divide-slate-100">
  @forelse($row->rencanaPengembangan as $index => $plan)
  <tr class="align-top"><td class="border border-slate-700 px-3 py-2 text-center">{{ $index + 1 }}</td><td class="border border-slate-700 px-3 py-2"><div class="font-semibold">{{ $plan->kompetensi?->kode_kompetensi ?? '-' }}</div><div>{{ $plan->kompetensi?->nama_kompetensi ?? '-' }}</div></td><td class="border border-slate-700 px-3 py-2">{{ $plan->pembelajaran_10_persen ?? 'Belum dibuat' }}</td><td class="border border-slate-700 px-3 py-2">@php($bukti10 = $plan->coachingBukti->where('jenis', 10)->first()) @if($bukti10)<span class="rounded bg-green-100 px-2 py-1 text-xs font-semibold text-green-700">{{ $bukti10->original_name ?? 'File uploaded' }}</span> <a class="ml-2 text-xs font-semibold text-blue-600 hover:text-blue-800" href="{{ route('atasan.coaching.download', ['idp' => $row->id_daftar_idp, 'type' => '10']) }}">Download</a>@else<span class="text-slate-400 italic">Belum dibuat</span>@endif</td><td class="border border-slate-700 px-3 py-2">{{ $plan->social_learning_20_persen ?? 'Belum dibuat' }}</td><td class="border border-slate-700 px-3 py-2">@php($bukti20 = $plan->coachingBukti->where('jenis', 20)->first()) @if($bukti20)<span class="rounded bg-green-100 px-2 py-1 text-xs font-semibold text-green-700">{{ $bukti20->original_name ?? 'File uploaded' }}</span> <a class="ml-2 text-xs font-semibold text-blue-600 hover:text-blue-800" href="{{ route('atasan.coaching.download', ['idp' => $row->id_daftar_idp, 'type' => '20']) }}">Download</a>@else<span class="text-slate-400 italic">Belum dibuat</span>@endif</td><td class="border border-slate-700 px-3 py-2">{{ $plan->action_learning_70_persen ?? 'Belum dibuat' }}</td><td class="border border-slate-700 px-3 py-2">@php($bukti70 = $plan->coachingBukti->where('jenis', 70)->first()) @if($bukti70)<span class="rounded bg-green-100 px-2 py-1 text-xs font-semibold text-green-700">{{ $bukti70->original_name ?? 'File uploaded' }}</span> <a class="ml-2 text-xs font-semibold text-blue-600 hover:text-blue-800" href="{{ route('atasan.coaching.download', ['idp' => $row->id_daftar_idp, 'type' => '70']) }}">Download</a>@else<span class="text-slate-400 italic">Belum dibuat</span>@endif</td></tr>
  @empty
  <tr><td colspan="8" class="px-4 py-8 text-center text-slate-500">Belum ada rencana coaching.</td></tr>
  @endforelse
  </tbody></table></div>
</div>
@empty
<div class="mt-6 rounded-2xl border border-slate-200 bg-white px-6 py-12 text-center text-slate-500">Belum ada data coaching bawahan.</div>
@endforelse
@endsection

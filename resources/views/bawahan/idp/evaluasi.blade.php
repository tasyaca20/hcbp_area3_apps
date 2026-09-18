@php($pageTitle = 'Evaluasi IDP')
@php($activeSection = 'idp')
@php($activePage = 'evaluasi')

@extends('layouts.app', ['title' => $pageTitle])

@section('content')
@if(session('success'))
<script>Swal.fire({icon:'success',text:@json(session('success'))});</script>
@endif
<div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white h-[220px] flex items-center">
  <div class="absolute inset-0 z-0"><div class="w-full h-full" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCjz6BR9t1xAm_4yTRtjUFCuOHqIkeE5EKFN9IU8hG8k_u3B7psxquSS6Suk591TY9q_Y27C-c3_8Z_HW-o9T4mcs-BSs8NcCHUUxWxaIBP5fVyRf74hO-H-p4ikb76omJMmeg7ue3VDxPp1T-0X1MDCB76X944-1OQyLGojPYj3yPCpw1wjdFvEbWlt83s8VQfKiYaIR8j8f1RfVnP8NMKIKcRo4783TuNbljKJLkY62hs9DzkeU81TZVA3E9SyXdF3Ck'); background-size: cover; background-position: center right;"></div><div class="absolute inset-0 bg-gradient-to-r from-white via-white/85 to-transparent"></div></div>
  <div class="relative z-10 px-8 max-w-xl"><h1 class="text-[27px] font-bold text-[#0a192f] mb-2 leading-tight">Evaluasi IDP</h1><p class="text-slate-500 text-[15px]">Unggah bukti realisasi evaluasi pembelajaran.</p></div>
</div>

@forelse($rows as $row)
<div class="mt-6 overflow-hidden rounded-[2rem] border border-slate-200 bg-white p-8">
  <h1 class="text-lg font-bold text-slate-900">Individual Development Program (IDP) - <i>Talent Home Coming</i></h1>
  <p class="mt-1 text-sm text-slate-600">Evaluasi</p>
  <div class="mt-4 grid gap-4 text-base font-semibold sm:grid-cols-2">
    <div class="space-y-2"><p>Nama Bawahan: <span class="font-normal">{{ $row->bawahan?->nama ?? '-' }}</span></p><p>NIP: <span class="font-normal">{{ $row->bawahan?->nip ?? '-' }}</span></p><p>Jabatan: <span class="font-normal">{{ $row->bawahan?->jabatan?->sebutan_jabatan ?? '-' }}</span></p></div>
    <div class="space-y-2"><p>Nama Atasan: <span class="font-normal">{{ $row->atasan?->nama ?? '-' }}</span></p><p>NIP: <span class="font-normal">{{ $row->atasan?->nip ?? '-' }}</span></p><p>Jabatan Atasan: <span class="font-normal">{{ $row->atasan?->jabatan?->sebutan_jabatan ?? '-' }}</span></p></div>
  </div>
   @if(! $row->coaching_disetujui)
   <p class="mt-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">Evaluasi belum tersedia. Menunggu persetujuan seluruh bukti coaching 10%, 20%, dan 70% untuk setiap kompetensi.</p>
   @else
   <div class="mt-6 overflow-x-auto"><table class="min-w-[1400px] w-full text-left text-sm"><thead class="bg-[#31599b] text-white"><tr><th class="px-4 py-4">No</th><th class="px-4 py-4">Kompetensi Teknis</th><th class="px-4 py-4">10% Pembelajaran<br>(Perencanaan)</th><th class="px-4 py-4">10% Pembelajaran<br>(Realisasi)</th><th class="px-4 py-4">20% Social Learning<br>(Perencanaan)</th><th class="px-4 py-4">20% Social Learning<br>(Realisasi)</th><th class="px-4 py-4">70% Action Learning<br>(Perencanaan)</th><th class="px-4 py-4">70% Action Learning<br>(Realisasi)</th><th class="px-4 py-4">Hasil Review Atasan</th></tr></thead><tbody class="divide-y divide-slate-100">
  @forelse($row->evaluasi as $index => $evaluasi)
  <tr class="align-top"><td class="border border-slate-700 px-3 py-2 text-center">{{ $index + 1 }}</td><td class="border border-slate-700 px-3 py-2"><div class="font-semibold">{{ $evaluasi->kompetensi?->kode_kompetensi ?? '-' }}</div><div>{{ $evaluasi->kompetensi?->nama_kompetensi ?? '-' }}</div></td><td class="border border-slate-700 px-3 py-2">{{ $evaluasi->pembelajaran_10_persen ?? 'Belum dibuat' }}</td><td class="border border-slate-700 px-3 py-2">@include('bawahan.idp.evaluasi-download-status', ['bukti' => $evaluasi->bukti->where('jenis', 10)->first(), 'jenis' => 10])</td><td class="border border-slate-700 px-3 py-2">{{ $evaluasi->social_learning_20_persen ?? 'Belum dibuat' }}</td><td class="border border-slate-700 px-3 py-2">@include('bawahan.idp.evaluasi-download-status', ['bukti' => $evaluasi->bukti->where('jenis', 20)->first(), 'jenis' => 20])</td><td class="border border-slate-700 px-3 py-2">{{ $evaluasi->action_learning_70_persen ?? 'Belum dibuat' }}</td><td class="border border-slate-700 px-3 py-2">@include('bawahan.idp.evaluasi-download-status', ['bukti' => $evaluasi->bukti->where('jenis', 70)->first(), 'jenis' => 70])</td><td class="border border-slate-700 px-3 py-2">@include('bawahan.idp.evaluasi-review-result')</td></tr>
  @empty
  <tr><td colspan="9" class="px-4 py-8 text-center text-slate-500">Belum ada evaluasi.</td></tr>
  @endforelse
   </tbody></table></div>
   @php($hasilEvaluasi = $row->evaluasi->firstWhere('skor', '!==', null))
   @if($hasilEvaluasi)
   <div class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-4">
     <h2 class="text-sm font-bold text-slate-800">Hasil Evaluasi Atasan</h2>
     <div class="mt-3 flex flex-wrap items-center gap-3">
       <span class="rounded bg-[#31599b] px-3 py-1.5 text-sm font-semibold text-white">{{ ['0 - Belum bisa', '1 - Bisa, butuh bimbingan', '2 - Mandiri'][$hasilEvaluasi->skor] ?? '-' }}</span>
       @if($hasilEvaluasi->tanggal_penilaian)<span class="text-xs text-slate-400">Dinilai: {{ $hasilEvaluasi->tanggal_penilaian->format('d/m/Y') }}</span>@endif
     </div>
     @if($hasilEvaluasi->feedback_evaluasi)<p class="mt-2 whitespace-pre-line text-sm text-slate-600">{{ $hasilEvaluasi->feedback_evaluasi }}</p>@endif
   </div>
    @endif
    @endif
</div>
@empty
<div class="mt-6 rounded-2xl border border-slate-200 bg-white px-6 py-12 text-center text-slate-500">Belum ada data evaluasi.</div>
@endforelse
@endsection

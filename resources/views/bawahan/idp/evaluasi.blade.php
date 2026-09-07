@extends('layouts.app', ['title' => 'Evaluasi IDP - Bawahan'])

@section('content')
<div class="relative overflow-hidden rounded-2xl bg-white border border-slate-200 h-[220px] flex items-center">
  <div class="absolute inset-0 z-0">
    <div class="w-full h-full" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCjz6BR9t1xAm_4yTRtjUFCuOHqIkeE5EKFN9IU8hG8k_u3B7psxquSS6Suk591TY9q_Y27C-c3_8Z_HW-o9T4mcs-BSs8NcCHUUxWxaIBP5fVyRf74hO-H-p4ikb76omJMmeg7ue3VDxPp1T-0X1MDCB76X944-1OQyLGojPYj3yPCpw1wjdFvEbWlt83s8VQfKiYaIR8j8f1RfVnP8NMKIKcRo4783TuNbljKJLkY62hs9DzkeU81TZVA3E9SyXdF3Ck'); background-size: cover; background-position: center right;"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-white via-white/85 to-transparent"></div>
  </div>
  <div class="relative z-10 px-8 max-w-xl">
    <h1 class="text-[27px] font-bold text-[#0a192f] mb-2 leading-tight">Evaluasi IDP</h1>
    <p class="text-slate-500 text-[15px]">Lihat hasil evaluasi dan feedback dari atasan.</p>
  </div>
</div>

<div class="bawahan-section mt-6 overflow-hidden rounded-[2rem] border border-slate-200 bg-white p-8">
  <h1 class="text-lg font-bold text-slate-900">Hasil Evaluasi</h1>
  <div class="mt-6 overflow-x-auto">
    <table class="min-w-[900px] w-full text-left text-sm">
      <thead class="bg-[#31599b] text-white"><tr><th class="px-4 py-4">No</th><th class="px-4 py-4">Nama Atasan</th><th class="px-4 py-4">Periode IDP</th><th class="px-4 py-4">Nilai Akhir</th><th class="px-4 py-4">Feedback</th><th class="px-4 py-4">Tanggal</th></tr></thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($evaluasi as $index => $item)
        <tr class="align-top"><td class="border border-slate-700 px-3 py-2 text-center">{{ $index + 1 }}</td><td class="border border-slate-700 px-3 py-2">{{ $item->daftarIdp->atasan->nama ?? '-' }}</td><td class="border border-slate-700 px-3 py-2">{{ $item->daftarIdp->periode ?? '-' }}</td><td class="border border-slate-700 px-3 py-2">{{ [0 => '0 - Belum bisa', 1 => '1 - Bisa, butuh pendamping', 2 => '2 - Sudah bisa/mandiri'][$item->skor] ?? '-' }}</td><td class="border border-slate-700 px-3 py-2">{{ $item->feedback }}</td><td class="border border-slate-700 px-3 py-2">{{ $item->tanggal_evaluasi?->format('d/m/Y') }}</td></tr>
        @empty
        <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">Belum ada evaluasi.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection

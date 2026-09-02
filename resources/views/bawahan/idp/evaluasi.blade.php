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

<div class="bawahan-section bg-white border border-slate-200 rounded-2xl p-6">
  <h1 class="text-xl font-bold mb-4">Hasil Evaluasi</h1>
  <div class="space-y-4">
    @forelse($evaluasi as $item)
    <div class="border border-slate-200 rounded-xl p-4">
      <div class="flex items-center justify-between">
        <h2 class="font-semibold">{{ $item->daftarIdp->atasan->nama ?? '-' }}</h2>
        <span class="text-sm text-slate-500">{{ $item->tanggal_evaluasi?->format('d/m/Y') }}</span>
      </div>
      <p class="mt-2 text-sm"><strong>Skor:</strong> {{ $item->skor }}</p>
      <p class="mt-2 text-sm"><strong>Feedback:</strong> {{ $item->feedback }}</p>
    </div>
    @empty
    <p class="text-slate-400">Belum ada feedback.</p>
    @endforelse
  </div>
</div>
@endsection

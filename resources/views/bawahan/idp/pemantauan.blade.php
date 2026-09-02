@extends('layouts.app', ['title' => 'Pemantauan IDP - Bawahan'])

@section('content')
<div class="relative overflow-hidden rounded-2xl bg-white border border-slate-200 h-[220px] flex items-center">
  <div class="absolute inset-0 z-0">
    <div class="w-full h-full" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCjz6BR9t1xAm_4yTRtjUFCuOHqIkeE5EKFN9IU8hG8k_u3B7psxquSS6Suk591TY9q_Y27C-c3_8Z_HW-o9T4mcs-BSs8NcCHUUxWxaIBP5fVyRf74hO-H-p4ikb76omJMmeg7ue3VDxPp1T-0X1MDCB76X944-1OQyLGojPYj3yPCpw1wjdFvEbWlt83s8VQfKiYaIR8j8f1RfVnP8NMKIKcRo4783TuNbljKJLkY62hs9DzkeU81TZVA3E9SyXdF3Ck'); background-size: cover; background-position: center right;"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-white via-white/85 to-transparent"></div>
  </div>
  <div class="relative z-10 px-8 max-w-xl">
    <h1 class="text-[27px] font-bold text-[#0a192f] mb-2 leading-tight">Pemantauan IDP</h1>
    <p class="text-slate-500 text-[15px]">Pantau progres pengembangan kompetensi Anda.</p>
  </div>
</div>

<div class="bawahan-section bg-white border border-slate-200 rounded-2xl p-6">
  <h1 class="text-xl font-bold">Pemantauan IDP Saya</h1>
  <p class="text-slate-500 mt-2 mb-6">Pantau progres setiap rencana pengembangan yang disetujui.</p>
  @forelse($rows as $row)
    @if($row->rencanaPengembangan->isNotEmpty())
    <div class="border border-slate-200 rounded-xl p-5 mb-5">
      <h2 class="font-semibold mb-3">{{ $row->periode_idp }}</h2>
      <div class="overflow-x-auto">
        <table class="min-w-[900px] w-full text-sm">
          <thead class="bg-slate-50"><tr><th class="px-3 py-3 text-left w-12">NO</th><th class="px-3 py-3 text-left w-[220px]">KOMPETENSI TEKNIS</th><th class="px-3 py-3 text-left">10% PEMBELAJARAN</th><th class="px-3 py-3 text-left">20% SOCIAL LEARNING</th><th class="px-3 py-3 text-left">70% ACTION LEARNING</th><th class="px-3 py-3 text-left">STATUS</th></tr></thead>
          <tbody>
            @foreach($row->rencanaPengembangan as $rencana)
            <tr class="border-t border-slate-200">
              <td class="px-3 py-2">{{ $loop->iteration }}</td>
              <td class="px-3 py-2"><strong>{{ $rencana->kompetensi->kode_kompetensi }}</strong><br><span class="text-xs text-slate-500">{{ $rencana->kompetensi->nama_kompetensi }}</span></td>
              <td class="px-3 py-2 whitespace-pre-line">{{ $rencana->pembelajaran_10_persen ?: '-' }}</td>
              <td class="px-3 py-2 whitespace-pre-line">{{ $rencana->social_learning_20_persen ?: '-' }}</td>
              <td class="px-3 py-2 whitespace-pre-line">{{ $rencana->action_learning_70_persen ?: '-' }}</td>
              <td class="px-3 py-2"><span class="rounded bg-green-100 px-2 py-1 text-xs font-medium text-green-700">{{ $rencana->status }}</span></td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    @endif
  @empty
    <p class="text-slate-400">Belum ada IDP.</p>
  @endforelse
</div>
@endsection

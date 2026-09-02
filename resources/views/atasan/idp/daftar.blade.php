@extends('layouts.app', ['title' => 'Daftar IDP - Atasan'])

@section('content')
@if(session('success') || session('warning') || session('error'))
<script>Swal.fire({icon:@json(session('success') ? 'success' : (session('warning') ? 'warning' : 'error')),text:@json(session('success') ?? session('warning') ?? session('error'))});</script>
@endif
<div class="relative overflow-hidden rounded-2xl bg-white border border-slate-200 h-[220px] flex items-center">
  <div class="absolute inset-0 z-0">
    <div class="w-full h-full" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCjz6BR9t1xAm_4yTRtjUFCuOHqIkeE5EKFN9IU8hG8k_u3B7psxquSS6Suk591TY9q_Y27C-c3_8Z_HW-o9T4mcs-BSs8NcCHUUxWxaIBP5fVyRf74hO-H-p4ikb76omJMmeg7ue3VDxPp1T-0X1MDCB76X944-1OQyLGojPYj3yPCpw1wjdFvEbWlt83s8VQfKiYaIR8j8f1RfVnP8NMKIKcRo4783TuNbljKJLkY62hs9DzkeU81TZVA3E9SyXdF3Ck'); background-size: cover; background-position: center right;"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-white via-white/85 to-transparent"></div>
  </div>
  <div class="relative z-10 px-8 max-w-xl">
    <h1 class="text-[27px] font-bold text-[#0a192f] mb-2 leading-tight">Daftar IDP</h1>
    <p class="text-slate-500 text-[15px]">Lihat daftar bawahan dan IDP mereka.</p>
  </div>
</div>

<div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
  <div class="overflow-x-auto rounded-xl border border-slate-200">
    <table data-table-search="false" data-table-pagination="false" class="w-full text-sm">
      <thead class="bg-slate-50 text-slate-600">
        <tr>
          <th class="px-4 py-3 text-left font-semibold">No</th>
          <th class="px-4 py-3 text-left font-semibold">Nama Bawahan</th>
          <th class="px-4 py-3 text-left font-semibold">NIP</th>
          <th class="px-4 py-3 text-left font-semibold">Jabatan</th>
          <th class="px-4 py-3 text-left font-semibold">Periode</th>
          <th class="px-4 py-3 text-left font-semibold">Business Area</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($rows as $i => $row)
        <tr class="hover:bg-slate-50/60">
          <td class="px-4 py-3 text-slate-500">{{ $i + 1 }}</td>
          <td class="px-4 py-3 font-medium text-slate-800">{{ $row->bawahan->nama ?? '-' }}</td>
          <td class="px-4 py-3 text-slate-600">{{ $row->bawahan->nip ?? '-' }}</td>
          <td class="px-4 py-3 text-slate-600">{{ $row->bawahan->jabatan->sebutan_jabatan ?? '-' }}</td>
          <td class="px-4 py-3"><span class="rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-[#31599b]">{{ $row->periode_idp }}</span></td>
          <td class="px-4 py-3 text-slate-600">{{ $row->business_area ?? '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="px-4 py-10 text-center text-slate-400">Tidak ada data</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection

@extends('layouts.app', ['title' => 'Daftar IDP - Admin Area'])

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
    <h1 class="text-[27px] font-bold text-[#0a192f] mb-2 leading-tight">Daftar IDP - Pegawai Area {{ auth()->user()->unit_induk }}</h1>
    <p class="text-slate-500 text-[15px]">Lihat daftar IDP pegawai di area Anda.</p>
  </div>
</div>

<div class="bg-white border border-slate-200 rounded-2xl p-6">
  <div class="mb-4 flex items-center justify-between">
    <h2 class="text-[15px] font-bold text-[#0a192f]">Data IDP Pegawai</h2>
    <span class="text-[12px] text-slate-400">{{ $rows->count() }} pegawai</span>
  </div>
  <div class="max-h-[600px] overflow-auto rounded-xl border border-slate-200">
    <table class="w-full text-sm tbl">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Bawahan</th>
          <th>NIP</th>
          <th>Jabatan</th>
          <th>Nama Atasan</th>
          <th>NIP Atasan</th>
          <th>Periode</th>
          <th>Business Area</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($rows as $i => $row)
        <tr class="hover:bg-slate-50/60">
          <td class="text-slate-500">{{ $rows->firstItem() + $i }}</td>
          <td class="font-medium text-slate-800">{{ $row->bawahan->nama ?? '-' }}</td>
          <td class="text-slate-600">{{ $row->bawahan->nip ?? '-' }}</td>
          <td class="text-slate-600">{{ $row->bawahan->jabatan->sebutan_jabatan ?? '-' }}</td>
          <td class="text-slate-600">{{ $row->atasan->nama ?? '-' }}</td>
          <td class="text-slate-600">{{ $row->atasan->nip ?? '-' }}</td>
          <td><span class="rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-[#31599b]">{{ $row->periode_idp }}</span></td>
          <td class="text-slate-600">{{ $row->business_area ?? '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="8" class="py-10 text-slate-400">Tidak ada data</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="mt-1">
    @include('components.pagination', ['paginator' => $rows])
  </div>
</div>
@endsection

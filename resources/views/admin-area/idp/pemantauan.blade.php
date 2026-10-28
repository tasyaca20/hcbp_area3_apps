@php($pageTitle = 'Pemantauan IDP')
@php($activeSection = 'idp')
@php($activePage = 'pemantauan')

@extends('layouts.app', ['title' => $pageTitle])

@section('content')
    <div class="relative overflow-hidden rounded-2xl bg-white border border-slate-200 h-[220px] flex items-center">
        <div class="absolute inset-0 z-0">
            <div class="w-full h-full"
                style="background-image:url('https://lh3.googleusercontent.com/aida-public/AB6AXuCjz6BR9t1xAm_4yTRtjUFCuOHqIkeE5EKFN9IU8hG8k_u3B7psxquSS6Suk591TY9q_Y27C-c3_8Z_HW-o9T4mcs-BSs8NcCHUUxWxaIBP5fVyRf74hO-H-p4ikb76omJMmeg7ue3VDxPp1T-0X1MDCB76X944-1OQyLGojPYj3yPCpw1wjdFvEbWlt83s8VQfKiYaIR8j8f1RfVnP8NMKIKcRo4783TuNbljKJLkY62hs9DzkeU81TZVA3E9SyXdF3Ck'); background-size:cover; background-position:center right;">
            </div>
            <div class="absolute inset-0 bg-gradient-to-r from-white via-white/85 to-transparent"></div>
        </div>
        <div class="relative z-10 px-8 max-w-xl">
            <h1 class="text-[27px] font-bold text-[#0a192f] mb-2 leading-tight">Penetapan dan Pemantauan IDP</h1>
            <p class="text-slate-500 text-[15px]">Pantau progres rencana pengembangan pegawai pada area Anda.</p>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-6">
        <div class="px-6 py-5 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-lg font-bold">Progres IDP</h2>
            <span class="text-xs text-slate-400">View Only - Tidak bisa aksi</span>
        </div>
        <div class="mb-4 flex justify-end">
            <form method="GET" data-search class="flex items-center gap-2"><input name="search"
                    value="{{ request('search') }}" placeholder="Cari semua data..."
                    class="w-52 rounded-lg border-slate-300 text-sm"><button
                    class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cari</button>
            </form>
        </div>
        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="min-w-[1200px] w-full text-left text-xs tbl">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Bawahan</th>
                        <th>Jabatan</th>
                        <th>Nama Atasan</th>
                        <th>NIP Atasan</th>
                        <th>Jabatan Atasan</th>
                        <th>Unit Induk</th>
                        <th>Status</th>
                        <th>Kompetensi</th>
                        <th>10% Pembelajaran</th>
                        <th>20% Social</th>
                        <th>70% Action Learning</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($rows as $index => $row)
                        @php($penetapan = $row->rencanaPengembangan->where('status', 'Disetujui'))
                        @if ($penetapan->isNotEmpty())
                            @foreach ($penetapan as $rencana)
                                <tr class="hover:bg-slate-50">
                                    @if ($loop->first)
                                        <td rowspan="{{ $penetapan->count() }}">{{ $rows->firstItem() + $index }}</td>
                                        <td class="font-medium" rowspan="{{ $penetapan->count() }}">
                                            {{ $row->bawahan->nama ?? '-' }}</td>
                                        <td rowspan="{{ $penetapan->count() }}">
                                            {{ $row->bawahan->jabatan->sebutan_jabatan ?? '-' }}</td>
                                        <td rowspan="{{ $penetapan->count() }}">{{ $row->atasan->nama ?? '-' }}</td>
                                        <td rowspan="{{ $penetapan->count() }}">{{ $row->atasan->nip ?? '-' }}</td>
                                        <td rowspan="{{ $penetapan->count() }}">
                                            {{ $row->atasan->jabatan->sebutan_jabatan ?? '-' }}</td>
                                        <td rowspan="{{ $penetapan->count() }}">{{ $row->bawahan->unit_induk ?? '-' }}
                                        </td>
                                        <td rowspan="{{ $penetapan->count() }}"><span
                                                class="px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-700">Disetujui</span>
                                        </td>
                                    @endif
                                    <td>
                                        <div class="font-medium">{{ $rencana->kompetensi->kode_kompetensi ?? '-' }}</div>
                                        <div class="text-slate-500">{{ $rencana->kompetensi->nama_kompetensi ?? '-' }}
                                        </div>
                                    </td>
                                    <td>{{ $rencana->pembelajaran_10_persen ?? '-' }}</td>
                                    <td>{{ $rencana->social_learning_20_persen ?? '-' }}</td>
                                    <td>{{ $rencana->action_learning_70_persen ?? '-' }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr class="hover:bg-slate-50">
                                <td>{{ $rows->firstItem() + $index }}</td>
                                <td class="font-medium">{{ $row->bawahan->nama ?? '-' }}</td>
                                <td>{{ $row->bawahan->jabatan->sebutan_jabatan ?? '-' }}</td>
                                <td>{{ $row->atasan->nama ?? '-' }}</td>
                                <td>{{ $row->atasan->nip ?? '-' }}</td>
                                <td>{{ $row->atasan->jabatan->sebutan_jabatan ?? '-' }}</td>
                                <td>{{ $row->bawahan->unit_induk ?? '-' }}</td>
                                <td><span class="px-2 py-1 rounded text-xs font-medium bg-yellow-100 text-yellow-700">Belum
                                        Disetujui</span></td>
                                <td class="text-slate-400" colspan="4">Belum ada penetapan IDP</td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="12" class="py-8 text-slate-500">Belum ada data IDP.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-1">
            @include('components.pagination', ['paginator' => $rows])
        </div>
    </div>
@endsection

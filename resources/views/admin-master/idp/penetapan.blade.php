@php($pageTitle = 'Penetapan IDP')
@php($heroTitle = 'Penetapan IDP')
@php($heroSubtitle = 'Pantau data rencana pengembangan pegawai yang telah ditetapkan.')
@php($cardTitle = 'Data Penetapan IDP')
@php($activeSection = 'idp')
@php($activePage = 'penetapan')
@php($statuses = ['Draft', 'Diajukan', 'Disetujui'])
@php($labels = ['Draft' => 'Belum Direncanakan', 'Diajukan' => 'Diajukan', 'Disetujui' => 'Disetujui'])
@php($colors = ['Draft' => '#94a3b8', 'Diajukan' => '#f59e0b', 'Disetujui' => '#22c55e'])
@php($statusOf = fn($row) => $row->rencanaPengembangan->contains('status', 'Disetujui') ? 'Disetujui' : ($row->rencanaPengembangan->contains('status', 'Diajukan') ? 'Diajukan' : 'Draft'))
@php($counts = collect($statuses)->mapWithKeys(fn($status) => [$status => $summaryRows->filter(fn($row) => $statusOf($row) === $status)->count()]))
@php($totalIdp = $counts->sum())
@php($chartMax = max($counts->max(), 1))

@extends('layouts.app', ['title' => $pageTitle])

@section('content')
    <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white h-[220px] flex items-center">
        <div class="absolute inset-0 z-0">
            <div class="w-full h-full"
                style="background-image: url('{{ asset('images/hero-dashboard.webp') }}'); background-size: cover; background-position: center right;">
            </div>
            <div class="absolute inset-0 bg-gradient-to-r from-white via-white/85 to-transparent"></div>
        </div>
        <div class="relative z-10 px-8 max-w-xl">
            <h1 class="text-[27px] font-bold text-[#0a192f] mb-2 leading-tight">{{ $heroTitle }}</h1>
            <p class="text-slate-500 text-[15px]">{{ $heroSubtitle }}</p>
        </div>
    </div>

    <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-6">
        <div class="mb-4">
            <h2 class="text-lg font-bold">Ringkasan Penetapan IDP</h2>
            <p class="mt-1 text-sm text-slate-500">Status rencana kompetensi yang diajukan bawahan.</p>
        </div>
        <div class="grid grid-cols-1 gap-8 p-6 xl:grid-cols-12 xl:items-stretch">
            <div class="flex flex-col justify-between space-y-6 xl:col-span-6">
                <div class="flex flex-wrap gap-4 rounded-xl border border-slate-100 bg-slate-50 p-3 text-xs">
                    @foreach ($statuses as $status)
                        <div class="flex items-center gap-2"><span class="h-3 w-3 rounded"
                                style="background-color: {{ $colors[$status] }};"></span>{{ $labels[$status] }}</div>
                    @endforeach
                </div>
                <div class="flex flex-grow flex-col justify-center space-y-5 pt-2">
                    <h3 class="text-sm font-semibold text-slate-800">Total Penetapan IDP</h3>
                    <div class="space-y-4">
                        @foreach ($statuses as $status)
                            @php($jumlah = $counts[$status])
                            <div class="grid grid-cols-[180px_1fr_55px] items-center gap-3"><span
                                    class="text-sm font-medium text-slate-600">{{ $labels[$status] }}</span>
                                <div class="relative flex h-8 items-center overflow-hidden rounded-md bg-slate-100">
                                    <div class="flex h-full items-center justify-end rounded-md pr-3 text-sm font-bold text-white"
                                        style="width: {{ $jumlah ? max(($jumlah / $chartMax) * 100, 10) : 0 }}%; background-color: {{ $colors[$status] }};">
                                        {{ $jumlah }}</div>
                                </div><span
                                    class="text-right text-sm font-bold text-slate-700">{{ $totalIdp ? round(($jumlah / $totalIdp) * 100, 1) : 0 }}%</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="h-full xl:col-span-6">
                <div class="h-full overflow-x-auto rounded-xl border border-slate-200 shadow-sm">
                    <table data-table-search="false" data-table-scroll="false"
                        class="h-full w-full border-collapse text-xs tbl">
                        <thead>
                            <tr>
                                @foreach ($statuses as $status)
                                    <th>{{ $labels[$status] }}</th>
                                @endforeach
                                <th>
                                    Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr>
                                @foreach ($statuses as $status)
                                    <td class="font-semibold" style="color: {{ $colors[$status] }};">
                                        {{ $counts[$status] }}</td>
                                @endforeach
                                <td class="font-bold">
                                    {{ $totalIdp }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 bg-white border border-slate-200 rounded-2xl p-6">
        <div class="mb-4 flex items-center justify-between gap-4">
            <h2 class="text-lg font-bold">{{ $cardTitle }}</h2><span class="text-xs text-slate-400">View Only - Tidak
                bisa aksi</span>
        </div>
        <div class="mb-4 flex justify-end">
            <form method="GET" data-search class="flex items-center gap-2"><input name="search"
                    value="{{ request('search') }}" placeholder="Cari semua data..."
                    class="w-52 rounded-lg border-slate-300 text-sm"><button
                    class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cari</button>
            </form>
        </div>
        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table data-table-pagination="false" class="min-w-[1200px] w-full text-left text-xs tbl">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Pegawai</th>
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
                        @php($penetapan = $row->rencanaPengembangan)
                        @if ($penetapan->isNotEmpty())
                            @foreach ($penetapan as $rencana)
                                <tr class="hover:bg-slate-50">
                                    @if ($loop->first)
                                        <td rowspan="{{ $penetapan->count() }}">{{ $rows->firstItem() + $index }}</td>
                                        <td class="font-medium" rowspan="{{ $penetapan->count() }}">
                                            {{ $row->bawahan?->nama ?? '-' }}</td>
                                        <td rowspan="{{ $penetapan->count() }}">
                                            {{ $row->bawahan?->jabatan?->sebutan_jabatan ?? '-' }}</td>
                                        <td rowspan="{{ $penetapan->count() }}">{{ $row->atasan?->nama ?? '-' }}</td>
                                        <td rowspan="{{ $penetapan->count() }}">{{ $row->atasan?->nip ?? '-' }}</td>
                                        <td rowspan="{{ $penetapan->count() }}">
                                            {{ $row->atasan?->jabatan?->sebutan_jabatan ?? '-' }}</td>
                                        <td rowspan="{{ $penetapan->count() }}">{{ $row->bawahan?->unit_induk ?? '-' }}
                                        </td>
                                        <td rowspan="{{ $penetapan->count() }}"><span
                                                class="px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-700">Disetujui</span>
                                        </td>
                                    @endif
                                    <td>
                                        <div class="font-medium">{{ $rencana->kompetensi?->kode_kompetensi ?? '-' }}</div>
                                        <div class="text-slate-500">{{ $rencana->kompetensi?->nama_kompetensi ?? '-' }}
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
                                <td class="font-medium">{{ $row->bawahan?->nama ?? '-' }}</td>
                                <td>{{ $row->bawahan?->jabatan?->sebutan_jabatan ?? '-' }}</td>
                                <td>{{ $row->atasan?->nama ?? '-' }}</td>
                                <td>{{ $row->atasan?->nip ?? '-' }}</td>
                                <td>{{ $row->atasan?->jabatan?->sebutan_jabatan ?? '-' }}</td>
                                <td>{{ $row->bawahan?->unit_induk ?? '-' }}</td>
                                <td><span class="px-2 py-1 rounded text-xs font-medium bg-yellow-100 text-yellow-700">Belum
                                        Disetujui</span></td>
                                <td class="text-slate-400" colspan="4">Belum ada penetapan IDP</td>
                            </tr>
                        @endif
                    @empty<tr>
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

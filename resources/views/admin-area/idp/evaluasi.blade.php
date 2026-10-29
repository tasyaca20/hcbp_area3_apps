@php($pageTitle = 'Evaluasi IDP')
@php($activeSection = 'idp')
@php($activePage = 'evaluasi')
@php($statuses = ['Belum Mengisi Evaluasi', 'Menunggu Persetujuan', 'Disetujui'])
@php($statusClasses = ['bg-red-100 text-red-700', 'bg-yellow-100 text-yellow-700', 'bg-green-100 text-green-700'])
@php($statusColors = ['Belum Mengisi Evaluasi' => '#94a3b8', 'Menunggu Persetujuan' => '#f59e0b', 'Disetujui' => '#22c55e'])
@php(
    $evaluationStatus = function ($row) {
        $evaluations = $row->evaluasi;
        $evidence = $evaluations->flatMap->bukti;
        if ($evidence->isEmpty()) {
            return 'Belum Mengisi Evaluasi';
        }
        $isApproved = $evaluations->isNotEmpty() && $evaluations->every(fn($evaluasi) => collect([10, 20, 70])->every(fn($jenis) => $evaluasi->bukti->contains(fn($bukti) => (int) $bukti->jenis === $jenis && $bukti->status_atasan === 'setuju')));

        return $isApproved ? 'Disetujui' : 'Menunggu Persetujuan';
    }
)
@php($units = $summaryRows->pluck('bawahan.unit_induk')->filter()->unique()->values())
@php($skorLabels = [0 => 'Belum bisa', 1 => 'Bisa, butuh bimbingan', 2 => 'Mandiri'])
@php($skorClasses = [0 => 'bg-red-100 text-red-700', 1 => 'bg-yellow-100 text-yellow-700', 2 => 'bg-green-100 text-green-700'])
@php($statusCounts = collect($statuses)->mapWithKeys(fn($status) => [$status => $summaryRows->filter(fn($row) => $evaluationStatus($row) === $status)->count()]))
@php($grandTotal = $statusCounts->sum())
@php($chartMax = max($statusCounts->max(), 1))

@extends('layouts.app', ['title' => $pageTitle])

@section('content')
    <div class="relative mb-6 flex h-[220px] items-center overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <div class="absolute inset-0 z-0">
            <div class="h-full w-full"
                style="background-image:url('{{ asset('images/hero-dashboard.webp') }}'); background-size:cover; background-position:center right;">
            </div>
            <div class="absolute inset-0 bg-gradient-to-r from-white via-white/85 to-transparent"></div>
        </div>
        <div class="relative z-10 max-w-xl px-8">
            <h1 class="mb-2 text-[27px] font-bold leading-tight text-[#0a192f]">Evaluasi IDP</h1>
            <p class="text-[15px] text-slate-500">Pantau hasil evaluasi pengembangan pegawai area Anda.</p>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6">
        <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-6 py-5">
            <h2 class="text-lg font-bold">Progres Evaluasi IDP</h2><span class="text-xs text-slate-400">View Only - Tidak
                bisa aksi</span>
        </div>
        <div class="grid grid-cols-1 gap-8 border-b border-slate-200 p-6 xl:grid-cols-12 xl:items-stretch">
            <div class="flex flex-col justify-between space-y-6 xl:col-span-6">
                <div class="flex flex-wrap gap-4 rounded-xl border border-slate-100 bg-slate-50 p-3 text-xs">
                    @foreach ($statuses as $status)
                        <div class="flex items-center gap-2"><span class="h-3 w-3 rounded"
                                style="background-color: {{ $statusColors[$status] }};"></span>{{ $status }}</div>
                    @endforeach
                </div>
                <div class="flex flex-grow flex-col justify-center space-y-5 pt-2">
                    <h3 class="text-sm font-semibold text-slate-800">Total Pemantauan Evaluasi</h3>
                    <div class="space-y-4">
                        @foreach ($statuses as $status)
                            @php($count = $statusCounts[$status])@php($percent = $grandTotal ? round(($count / $grandTotal) * 100, 1) : 0)
                            <div class="grid grid-cols-[180px_1fr_55px] items-center gap-3"><span
                                    class="text-sm font-medium text-slate-600">{{ $status }}</span>
                                <div class="relative flex h-8 items-center overflow-hidden rounded-md bg-slate-100">
                                    <div class="flex h-full items-center justify-end rounded-md pr-3 text-sm font-bold text-white"
                                        style="width: {{ $count ? max(($count / $chartMax) * 100, 10) : 0 }}%; background-color: {{ $statusColors[$status] }};">
                                        {{ $count }}</div>
                                </div><span class="text-right text-sm font-bold text-slate-700">{{ $percent }}%</span>
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
                                <th>Unit Area</th>
                                @foreach ($statuses as $status)
                                    <th>{{ $status }}</th>
                                @endforeach
                                <th>
                                    Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($units as $unit)
                                @php($unitCounts = collect($statuses)->mapWithKeys(fn($status) => [$status => $summaryRows->filter(fn($row) => $row->bawahan?->unit_induk === $unit && $evaluationStatus($row) === $status)->count()]))
                                <tr class="transition-colors hover:bg-slate-50/80">
                                    <td class="font-semibold text-slate-800">{{ $unit }}</td>
                                    @foreach ($statuses as $status)
                                        <td class="font-semibold" style="color: {{ $statusColors[$status] }};">
                                            {{ $unitCounts[$status] }}</td>
                                    @endforeach
                                    <td class="font-bold">
                                        {{ $unitCounts->sum() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="border-t-2 border-slate-200 bg-slate-100/80 font-bold">
                            <tr>
                                <td class="font-bold text-slate-800">Total All</td>
                                @foreach ($statuses as $status)
                                    <td class="text-slate-700">{{ $statusCounts[$status] }}</td>
                                @endforeach
                                <td class="font-bold">
                                    {{ $grandTotal }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-6">
        <div class="mb-4 flex justify-end">
            <form method="GET" data-search class="flex items-center gap-2"><input name="search"
                    value="{{ request('search') }}" placeholder="Cari semua data..."
                    class="w-52 rounded-lg border-slate-300 text-sm"><button
                    class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cari</button>
            </form>
        </div>
        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table data-table-pagination="false" class="min-w-[1850px] w-full text-left text-xs tbl">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Pegawai</th>
                        <th>Jabatan Pegawai</th>
                        <th>Nama Atasan</th>
                        <th>Jabatan Atasan</th>
                        <th>Unit Induk</th>
                        <th>Status</th>
                        <th>Kompetensi</th>
                        <th>10% Perencanaan</th>
                        <th>10% Realisasi</th>
                        <th>20% Perencanaan</th>
                        <th>20% Realisasi</th>
                        <th>70% Perencanaan</th>
                        <th>70% Realisasi</th>
                        <th>Nilai</th>
                        <th>Feedback</th>
                        <th>Tanggal Penilaian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rows as $index => $row)
                        @php($evaluations = $row->evaluasi)@php($status = $evaluationStatus($row))@foreach ($evaluations as $evaluasi)
                            <tr class="hover:bg-slate-50">
                                @if ($loop->first)
                                    <td rowspan="{{ $evaluations->count() }}">{{ $rows->firstItem() + $index }}</td>
                                    <td class="font-medium" rowspan="{{ $evaluations->count() }}">
                                        {{ $row->bawahan?->nama ?? '-' }}</td>
                                    <td rowspan="{{ $evaluations->count() }}">
                                        {{ $row->bawahan?->jabatan?->sebutan_jabatan ?? '-' }}</td>
                                    <td rowspan="{{ $evaluations->count() }}">{{ $row->atasan?->nama ?? '-' }}</td>
                                    <td rowspan="{{ $evaluations->count() }}">
                                        {{ $row->atasan?->jabatan?->sebutan_jabatan ?? '-' }}</td>
                                    <td rowspan="{{ $evaluations->count() }}">{{ $row->bawahan?->unit_induk ?? '-' }}</td>
                                    <td rowspan="{{ $evaluations->count() }}"><span
                                            class="rounded px-2 py-1 font-medium {{ $statusClasses[array_search($status, $statuses, true)] }}">{{ $status }}</span>
                                    </td>
                                @endif
                                <td>
                                    <div class="font-medium">{{ $evaluasi->kompetensi?->kode_kompetensi ?? '-' }}</div>
                                    <div class="text-slate-500">{{ $evaluasi->kompetensi?->nama_kompetensi ?? '-' }}</div>
                                </td>
                                <td>{{ $evaluasi->pembelajaran_10_persen ?? '-' }}</td>
                                <td>@php($bukti10 = $evaluasi->bukti->firstWhere('jenis', 10)) @if ($bukti10)
                                        <a class="font-semibold text-blue-600 hover:underline"
                                        href="{{ route('admin-area.idp.evaluasi.download', ['idp' => $row, 'type' => 10, 'evaluasi' => $evaluasi]) }}">{{ $bukti10->original_name }}</a>@else-
                                    @endif
                                </td>
                                <td>{{ $evaluasi->social_learning_20_persen ?? '-' }}</td>
                                <td>@php($bukti20 = $evaluasi->bukti->firstWhere('jenis', 20)) @if ($bukti20)
                                        <a class="font-semibold text-blue-600 hover:underline"
                                        href="{{ route('admin-area.idp.evaluasi.download', ['idp' => $row, 'type' => 20, 'evaluasi' => $evaluasi]) }}">{{ $bukti20->original_name }}</a>@else-
                                    @endif
                                </td>
                                <td>{{ $evaluasi->action_learning_70_persen ?? '-' }}</td>
                                <td>@php($bukti70 = $evaluasi->bukti->firstWhere('jenis', 70)) @if ($bukti70)
                                        <a class="font-semibold text-blue-600 hover:underline"
                                        href="{{ route('admin-area.idp.evaluasi.download', ['idp' => $row, 'type' => 70, 'evaluasi' => $evaluasi]) }}">{{ $bukti70->original_name }}</a>@else-
                                    @endif
                                </td>
                                <td>
                                    @if (isset($skorLabels[$evaluasi->skor]))
                                        <span
                                        class="inline-block rounded px-2 py-1 font-medium {{ $skorClasses[$evaluasi->skor] }}">{{ $skorLabels[$evaluasi->skor] }}</span>@else-
                                    @endif
                                </td>
                                <td>{{ $evaluasi->feedback_evaluasi ?? '-' }}</td>
                                <td>{{ $evaluasi->tanggal_penilaian?->format('d/m/Y') ?? '-' }}</td>
                            </tr>
                        @endforeach
                        @empty<tr>
                                <td colspan="17" class="py-8 text-slate-500">Belum ada data evaluasi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @include('components.pagination', ['paginator' => $rows])
        </div>
    @endsection

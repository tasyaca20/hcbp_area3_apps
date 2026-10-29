@extends('layouts.app', ['title' => 'Pemantauan IDP - Bawahan'])

@section('content')
    <div class="relative overflow-hidden rounded-2xl bg-white border border-slate-200 h-[220px] flex items-center">
        <div class="absolute inset-0 z-0">
            <div class="w-full h-full"
                style="background-image: url('{{ asset('images/hero-dashboard.webp') }}'); background-size: cover; background-position: center right;">
            </div>
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
            @if ($row->rencanaPengembangan->isNotEmpty())
                @php($statusClasses = ['Draft' => 'bg-slate-100 text-slate-700', 'Diajukan' => 'bg-yellow-100 text-yellow-700', 'Revisi' => 'bg-red-100 text-red-700', 'Disetujui' => 'bg-green-100 text-green-700', 'Berjalan' => 'bg-blue-100 text-blue-700', 'Selesai' => 'bg-violet-100 text-violet-700'])
                <div class="border border-slate-200 rounded-xl p-5 mb-5">
                    <h2 class="font-semibold mb-3">{{ $row->periode_idp }}</h2>
                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="min-w-[900px] w-full text-sm tbl">
                            <thead>
                                <tr>
                                    <th>NO</th>
                                    <th class="w-[220px]">KOMPETENSI TEKNIS</th>
                                    <th>10% PEMBELAJARAN</th>
                                    <th>20% SOCIAL LEARNING</th>
                                    <th>70% ACTION LEARNING</th>
                                    <th>STATUS</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($row->rencanaPengembangan as $rencana)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $rencana->kompetensi->kode_kompetensi }}</strong><br><span
                                                class="text-xs text-slate-500">{{ $rencana->kompetensi->nama_kompetensi }}</span>
                                        </td>
                                        <td class="whitespace-pre-line">{{ $rencana->pembelajaran_10_persen ?: '-' }}</td>
                                        <td class="whitespace-pre-line">{{ $rencana->social_learning_20_persen ?: '-' }}
                                        </td>
                                        <td class="whitespace-pre-line">{{ $rencana->action_learning_70_persen ?: '-' }}
                                        </td>
                                        <td><span
                                                class="rounded px-2 py-1 text-xs font-medium {{ $statusClasses[$rencana->status] ?? 'bg-slate-100 text-slate-700' }}">{{ $rencana->status }}</span>
                                        </td>
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
        @include('components.pagination', ['paginator' => $rows])
    </div>
@endsection

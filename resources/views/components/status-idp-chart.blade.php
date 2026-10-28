@php
    $warnaStatus = [
        'Draft' => ['#94a3b8', 'bg-slate-400', 'text-slate-600'],
        'Diajukan' => ['#fbbf24', 'bg-amber-400', 'text-amber-600'],
        'Revisi' => ['#fb7185', 'bg-rose-400', 'text-rose-600'],
        'Disetujui' => ['#6366f1', 'bg-indigo-500', 'text-indigo-600'],
    ];
    $warnaDefault = ['#cbd5e1', 'bg-slate-300', 'text-slate-600'];
    $totalStatusIdp = $statusIdp->sum('total');
    $terisi = $statusIdp->where('total', '>', 0);

    $offset = 0;
    $segmen = $terisi
        ->map(function ($status) use ($warnaStatus, $warnaDefault, $totalStatusIdp, &$offset) {
            $warna = $warnaStatus[$status->status_perencanaan] ?? $warnaDefault;
            $porsi = $totalStatusIdp > 0 ? ($status->total / $totalStatusIdp) * 100 : 0;
            $segmen = ['warna' => $warna[0], 'porsi' => $porsi, 'offset' => 100 - $offset];
            $offset += $porsi;
            return $segmen;
        })
        ->values();

    $gradien = $segmen
        ->map(fn($s) => $s['warna'] . ' ' . ($s['offset'] - $s['porsi']) . '% ' . $s['offset'] . '%')
        ->implode(', ');
    $statusTerbesar = $terisi->sortByDesc('total')->first();
@endphp

@if ($totalStatusIdp > 0)
    <div class="flex flex-col items-center gap-6 sm:flex-row">
        <div class="relative h-36 w-36 shrink-0">
            <div class="h-full w-full rounded-full"
                style="background: conic-gradient({{ $gradien }}{{ $segmen->isNotEmpty() ? ', ' : '' }}#e2e8f0 0);">
            </div>
            <div class="absolute inset-5 flex flex-col items-center justify-center rounded-full bg-white">
                <span
                    class="text-[26px] font-extrabold leading-none text-[#0a192f]">{{ number_format($totalStatusIdp) }}</span>
                <span class="mt-1 text-[10px] font-medium uppercase tracking-wide text-slate-400">Total IDP</span>
            </div>
        </div>

        <div class="min-w-0 flex-1 space-y-2">
            @foreach ($statusIdp as $status)
                @php $warna = $warnaStatus[$status->status_perencanaan] ?? $warnaDefault; @endphp
                <div class="flex items-center gap-2.5 {{ $status->total > 0 ? '' : 'opacity-50' }}">
                    <span class="h-2.5 w-2.5 shrink-0 rounded-full {{ $warna[1] }}"></span>
                    <span
                        class="min-w-0 flex-1 truncate text-[12.5px] text-slate-600">{{ $status->status_perencanaan }}</span>
                    <span class="text-[12px] font-bold tabular-nums text-[#0a192f]">{{ $status->total }}</span>
                    <span
                        class="w-9 text-right text-[11px] tabular-nums text-slate-400">{{ $totalStatusIdp > 0 ? round(($status->total / $totalStatusIdp) * 100) : 0 }}%</span>
                </div>
            @endforeach
        </div>
    </div>

    @if ($statusTerbesar)
        <div class="mt-5 flex items-center gap-2 rounded-xl border border-slate-100 bg-slate-50/70 px-3 py-2.5">
            <span
                class="material-symbols-outlined text-[18px] {{ $warnaStatus[$statusTerbesar->status_perencanaan][2] ?? 'text-slate-500' }}">trending_up</span>
            <p class="text-[12px] text-slate-500">
                Terbanyak: <span class="font-semibold text-[#0a192f]">{{ $statusTerbesar->status_perencanaan }}</span>
                — {{ $statusTerbesar->total }} IDP ({{ round(($statusTerbesar->total / $totalStatusIdp) * 100) }}%
                dari total)
            </p>
        </div>
    @endif
@else
    <div class="flex flex-col items-center justify-center py-10 text-center">
        <span class="material-symbols-outlined text-[40px] text-slate-300 mb-2">inbox</span>
        <p class="text-[13px] text-slate-400">Belum ada data IDP</p>
    </div>
@endif

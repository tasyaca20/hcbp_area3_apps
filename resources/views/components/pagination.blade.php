@if ($paginator->hasPages())
    <div
        class="mt-5 mb-2 flex flex-col items-center justify-between gap-4 border-t border-slate-100 px-1 pt-4 text-sm text-slate-500 sm:flex-row">
        <span>Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }}
            data</span>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ $paginator->url(1) }}" @class([
                'rounded border border-slate-300 bg-white px-3 py-1 transition',
                'pointer-events-none opacity-50' => $paginator->onFirstPage(),
            ])>&lt;&lt;</a>
            <a href="{{ $paginator->previousPageUrl() ?? '#' }}" @class([
                'rounded border border-slate-300 bg-white px-3 py-1 transition',
                'pointer-events-none opacity-50' => $paginator->onFirstPage(),
            ])>&lt;</a>
            <select aria-label="Pilih halaman"
                class="rounded border border-slate-300 bg-white py-1 text-sm transition focus:border-[#31599b] focus:ring-[#31599b]"
                onchange="window.location.href=this.value">
                @foreach (range(1, $paginator->lastPage()) as $halaman)
                    <option value="{{ $paginator->url($halaman) }}" @selected($paginator->currentPage() === $halaman)>{{ $halaman }}
                    </option>
                @endforeach
            </select>
            <a href="{{ $paginator->nextPageUrl() ?? '#' }}" @class([
                'rounded border border-slate-300 bg-white px-3 py-1 transition',
                'pointer-events-none opacity-50' => !$paginator->hasMorePages(),
            ])>&gt;</a>
            <a href="{{ $paginator->url($paginator->lastPage()) }}" @class([
                'rounded border border-slate-300 bg-white px-3 py-1 transition',
                'pointer-events-none opacity-50' => !$paginator->hasMorePages(),
            ])>&gt;&gt;</a>
        </div>
    </div>
@endif

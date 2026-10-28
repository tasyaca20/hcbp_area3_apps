@extends('layouts.app')

@section('content')
    <div class="mx-auto w-full max-w-3xl">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-xl font-bold text-[#0a192f]">Notifikasi</h1>
            <form method="POST" action="{{ route('notifikasi.baca-semua') }}">
                @csrf
                <button type="submit"
                    class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Tandai
                    semua dibaca</button>
            </form>
        </div>

        <div class="divide-y divide-slate-100 overflow-hidden rounded-2xl border border-slate-200 bg-white">
            @forelse ($daftar as $item)
                <form method="POST" action="{{ route('notifikasi.baca', $item) }}">
                    @csrf
                    <button type="submit" class="flex w-full items-start gap-3 px-4 py-3 text-left hover:bg-slate-50">
                        <span
                            class="material-symbols-outlined mt-0.5 text-[20px] {{ $item->dibaca_pada ? 'text-slate-300' : 'text-[#31599b]' }}">notifications</span>
                        <span class="min-w-0 flex-1">
                            <span
                                class="block text-[14px] {{ $item->dibaca_pada ? 'font-medium text-slate-500' : 'font-semibold text-[#0a192f]' }}">{{ $item->judul }}</span>
                            <span class="mt-0.5 block text-[13px] text-slate-500">{{ $item->pesan }}</span>
                            <span
                                class="mt-1 block text-[11px] text-slate-400">{{ $item->created_at?->diffForHumans() }}</span>
                        </span>
                        @unless ($item->dibaca_pada)
                            <span class="mt-1.5 h-2 w-2 flex-shrink-0 rounded-full bg-red-500"></span>
                        @endunless
                    </button>
                </form>
            @empty
                <p class="px-4 py-10 text-center text-sm text-slate-400">Belum ada notifikasi.</p>
            @endforelse
        </div>

        <div class="mt-4">{{ $daftar->links() }}</div>
    </div>
@endsection

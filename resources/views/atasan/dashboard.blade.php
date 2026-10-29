@extends('layouts.app', ['title' => 'HCBP Area 3 Apps - Atasan'])

@section('content')
    <div class="relative overflow-hidden rounded-2xl bg-white border border-slate-200 h-[220px] flex items-center">
        <div class="absolute inset-0 z-0">
            <div class="w-full h-full"
                style="background-image: url('{{ asset('images/hero-dashboard.webp') }}'); background-size: cover; background-position: center right;">
            </div>
            <div class="absolute inset-0 bg-gradient-to-r from-white via-white/85 to-transparent"></div>
        </div>
        <div class="relative z-10 px-8 max-w-xl">
            <h1 class="text-[27px] font-bold text-[#0a192f] mb-2 leading-tight">Dashboard Atasan</h1>
            <p class="text-slate-500 text-[15px]">Kelola IDP dan coaching bawahan Anda.</p>
        </div>
    </div>
@endsection

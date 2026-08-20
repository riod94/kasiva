@extends('errors.layout')

@section('code', '404')
@section('title', 'Halaman Tidak Ditemukan')
@section('category', 'NAVIGASI OUTLET')
@section('glow_color', 'bg-[#3EDAD7]')
@section('glow_color_secondary', 'bg-[#4338CA]')
@section('accent_bar', 'bg-gradient-to-r from-[#3EDAD7] via-[#00AAA6] to-[#4338CA]')
@section('badge_indicator_color', 'bg-[#3EDAD7]')
@section('badge_style', 'bg-[#00AAA6]/20 text-[#3EDAD7] border border-[#00AAA6]/40')
@section('icon_bg', 'bg-[#00AAA6]/15 text-[#3EDAD7] border border-[#00AAA6]/30')

@section('icon')
    <x-icon name="question" class="w-10 h-10 text-[#3EDAD7]" />
@endsection

@section('message')
    {{ $exception->getMessage() ?: 'Tautan atau alamat URL yang Anda tuju tidak ditemukan atau mungkin telah dipindahkan ke menu lain. Periksa kembali alamat URL Anda atau kembali ke menu kasir.' }}
@endsection

@section('actions')
    <a 
        href="{{ route('pos.cashier') }}" 
        class="w-full sm:w-auto px-6 py-3.5 rounded-2xl font-black text-xs uppercase tracking-wider bg-[#4338CA] hover:bg-[#3730A3] text-white shadow-xl shadow-[#4338CA]/30 flex items-center justify-center gap-2 active:scale-95 transition"
    >
        <x-icon name="store" class="w-4 h-4" />
        <span>Buka Kasir POS</span>
    </a>

    <button 
        type="button" 
        onclick="window.history.back()"
        class="w-full sm:w-auto px-6 py-3.5 rounded-2xl font-bold text-xs uppercase tracking-wider bg-[#16192E] hover:bg-[#25215A] text-slate-200 border border-[#2E2A68] flex items-center justify-center gap-2 active:scale-95 transition"
    >
        <x-icon name="arrow-left" class="w-4 h-4" />
        <span>Halaman Sebelumnya</span>
    </button>
@endsection

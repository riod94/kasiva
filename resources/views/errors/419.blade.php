@extends('errors.layout')

@section('code', '419')
@section('title', 'Sesi Formulir Kedaluwarsa')
@section('category', 'TOKEN KEAMANAN')
@section('glow_color', 'bg-sky-600')
@section('glow_color_secondary', 'bg-[#4338CA]')
@section('accent_bar', 'bg-gradient-to-r from-sky-500 via-indigo-500 to-[#00AAA6]')
@section('badge_indicator_color', 'bg-sky-400')
@section('badge_style', 'bg-sky-500/20 text-sky-300 border border-sky-500/40')
@section('icon_bg', 'bg-sky-500/15 text-sky-400 border border-sky-500/30')

@section('icon')
    <x-icon name="refresh" class="w-10 h-10 text-sky-400" />
@endsection

@section('message')
    {{ $exception->getMessage() ?: 'Token keamanan (CSRF Token) formulir telah kedaluwarsa karena tidak ada aktivitas dalam waktu lama. Silakan segarkan halaman browser dan kirimkan kembali aksi Anda.' }}
@endsection

@section('actions')
    <button 
        type="button"
        onclick="window.location.reload()"
        class="w-full sm:w-auto px-6 py-3.5 rounded-2xl font-black text-xs uppercase tracking-wider bg-[#4338CA] hover:bg-[#3730A3] text-white shadow-xl shadow-[#4338CA]/30 flex items-center justify-center gap-2 active:scale-95 transition"
    >
        <x-icon name="refresh" class="w-4 h-4" />
        <span>Segarkan Halaman</span>
    </button>

    <a 
        href="{{ route('pos.cashier') }}" 
        class="w-full sm:w-auto px-6 py-3.5 rounded-2xl font-bold text-xs uppercase tracking-wider bg-[#16192E] hover:bg-[#25215A] text-slate-200 border border-[#2E2A68] flex items-center justify-center gap-2 active:scale-95 transition"
    >
        <x-icon name="store" class="w-4 h-4" />
        <span>Buka Kasir</span>
    </a>
@endsection

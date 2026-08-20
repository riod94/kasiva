@extends('errors.layout')

@section('code', '503')
@section('title', 'Layanan Sedang Dalam Pemeliharaan')
@section('category', 'PEMELIHARAAN SISTEM')
@section('glow_color', 'bg-[#00AAA6]')
@section('glow_color_secondary', 'bg-[#4338CA]')
@section('accent_bar', 'bg-gradient-to-r from-[#00AAA6] via-[#3EDAD7] to-[#8696ED]')
@section('badge_indicator_color', 'bg-[#00AAA6]')
@section('badge_style', 'bg-[#00AAA6]/20 text-[#3EDAD7] border border-[#00AAA6]/40')
@section('icon_bg', 'bg-[#00AAA6]/15 text-[#3EDAD7] border border-[#00AAA6]/30')

@section('icon')
    <x-icon name="wrench" class="w-10 h-10 text-[#3EDAD7]" />
@endsection

@section('message')
    {{ $exception->getMessage() ?: 'Sistem Kasiva POS sedang menjalani pemeliharaan rutin atau peningkatan performa server untuk memastikan keandalan transaksi outlet Anda. Layanan akan segera kembali aktif dalam beberapa saat.' }}
@endsection

@section('actions')
    <button 
        type="button" 
        onclick="window.location.reload()"
        class="w-full sm:w-auto px-6 py-3.5 rounded-2xl font-black text-xs uppercase tracking-wider bg-[#00AAA6] hover:bg-[#008f8c] text-white shadow-xl shadow-[#00AAA6]/30 flex items-center justify-center gap-2 active:scale-95 transition"
    >
        <x-icon name="refresh" class="w-4 h-4" />
        <span>Periksa Ketersediaan</span>
    </button>

    <a 
        href="{{ route('landing') }}" 
        class="w-full sm:w-auto px-6 py-3.5 rounded-2xl font-bold text-xs uppercase tracking-wider bg-[#16192E] hover:bg-[#25215A] text-slate-200 border border-[#2E2A68] flex items-center justify-center gap-2 active:scale-95 transition"
    >
        <x-icon name="home" class="w-4 h-4" />
        <span>Beranda Kasiva</span>
    </a>
@endsection

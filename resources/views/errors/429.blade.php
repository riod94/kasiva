@extends('errors.layout')

@section('code', '429')
@section('title', 'Terlalu Banyak Permintaan')
@section('category', 'PEMBATASAN AKSES')
@section('glow_color', 'bg-orange-600')
@section('glow_color_secondary', 'bg-rose-600')
@section('accent_bar', 'bg-gradient-to-r from-orange-500 via-amber-500 to-rose-500')
@section('badge_indicator_color', 'bg-orange-400')
@section('badge_style', 'bg-orange-500/20 text-orange-300 border border-orange-500/40')
@section('icon_bg', 'bg-orange-500/15 text-orange-400 border border-orange-500/30')

@section('icon')
    <x-icon name="clock" class="w-10 h-10 text-orange-400" />
@endsection

@section('message')
    {{ $exception->getMessage() ?: 'Sistem mendeteksi terlalu banyak pengiriman permintaan dari perangkat Anda dalam waktu singkat. Mohon tunggu beberapa saat sebelum mencoba kembali.' }}
@endsection

@section('actions')
    <button 
        type="button" 
        onclick="setTimeout(() => window.location.reload(), 1000)"
        class="w-full sm:w-auto px-6 py-3.5 rounded-2xl font-black text-xs uppercase tracking-wider bg-[#00AAA6] hover:bg-[#008F8C] text-white shadow-xl shadow-[#00AAA6]/30 flex items-center justify-center gap-2 active:scale-95 transition"
    >
        <x-icon name="refresh" class="w-4 h-4" />
        <span>Coba Lagi Sekarang</span>
    </button>

    <a 
        href="{{ route('pos.cashier') }}" 
        class="w-full sm:w-auto px-6 py-3.5 rounded-2xl font-bold text-xs uppercase tracking-wider bg-[#16192E] hover:bg-[#2A3155] text-slate-200 border border-[#2E2A68] flex items-center justify-center gap-2 active:scale-95 transition"
    >
        <x-icon name="store" class="w-4 h-4" />
        <span>Menu Kasir</span>
    </a>
@endsection

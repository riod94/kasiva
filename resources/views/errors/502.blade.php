@extends('errors.layout')

@section('code', '502')
@section('title', 'Gateway Bermasalah (Bad Gateway)')
@section('category', 'KONEKSI SERVER')
@section('glow_color', 'bg-violet-600')
@section('glow_color_secondary', 'bg-rose-600')
@section('accent_bar', 'bg-gradient-to-r from-violet-600 via-[#8696ED] to-rose-500')
@section('badge_indicator_color', 'bg-violet-400')
@section('badge_style', 'bg-violet-500/20 text-violet-300 border border-violet-500/40')
@section('icon_bg', 'bg-violet-500/15 text-violet-400 border border-violet-500/30')

@section('icon')
    <x-icon name="server" class="w-10 h-10 text-violet-400" />
@endsection

@section('message')
    {{ $exception->getMessage() ?: 'Server upstream atau reverse proxy gagal merespons dengan benar. Hal ini biasanya bersifat sementara saat server sedang memperbarui konfigurasi atau memproses beban puncak.' }}
@endsection

@section('actions')
    <button 
        type="button" 
        onclick="window.location.reload()"
        class="w-full sm:w-auto px-6 py-3.5 rounded-2xl font-black text-xs uppercase tracking-wider bg-[#00AAA6] hover:bg-[#008F8C] text-white shadow-xl shadow-[#00AAA6]/30 flex items-center justify-center gap-2 active:scale-95 transition"
    >
        <x-icon name="refresh" class="w-4 h-4" />
        <span>Coba Muat Ulang</span>
    </button>

    <a 
        href="{{ route('pos.cashier') }}" 
        class="w-full sm:w-auto px-6 py-3.5 rounded-2xl font-bold text-xs uppercase tracking-wider bg-[#16192E] hover:bg-[#2A3155] text-slate-200 border border-[#2E2A68] flex items-center justify-center gap-2 active:scale-95 transition"
    >
        <x-icon name="store" class="w-4 h-4" />
        <span>Menu Kasir</span>
    </a>
@endsection

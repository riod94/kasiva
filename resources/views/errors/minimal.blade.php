@extends('errors.layout')

@section('code', $exception->getStatusCode() ?? 'Error')
@section('title', $exception->getMessage() ?: 'Terjadi Kendala')
@section('category', 'SISTEM POS')
@section('glow_color', 'bg-[#8696ED]')
@section('glow_color_secondary', 'bg-[#00AAA6]')
@section('accent_bar', 'bg-gradient-to-r from-[#8696ED] via-[#00AAA6] to-[#00AAA6]')
@section('badge_indicator_color', 'bg-[#8696ED]')
@section('badge_style', 'bg-[#8696ED]/20 text-[#8696ED] border border-[#8696ED]/40')
@section('icon_bg', 'bg-[#8696ED]/15 text-[#8696ED] border border-[#8696ED]/30')

@section('icon')
    <x-icon name="alert-triangle" class="w-10 h-10 text-[#8696ED]" />
@endsection

@section('message')
    {{ $exception->getMessage() ?: 'Terjadi kendala saat memproses permintaan Anda. Silakan kembali ke menu kasir atau hubungi administrator outlet jika kendala berlanjut.' }}
@endsection

@section('actions')
    <a 
        href="{{ route('pos.cashier') }}" 
        class="w-full sm:w-auto px-6 py-3.5 rounded-2xl font-black text-xs uppercase tracking-wider bg-[#00AAA6] hover:bg-[#008F8C] text-white shadow-xl shadow-[#00AAA6]/30 flex items-center justify-center gap-2 active:scale-95 transition"
    >
        <x-icon name="store" class="w-4 h-4" />
        <span>Kembali ke Kasir POS</span>
    </a>

    <button 
        type="button" 
        onclick="window.history.back()"
        class="w-full sm:w-auto px-6 py-3.5 rounded-2xl font-bold text-xs uppercase tracking-wider bg-[#16192E] hover:bg-[#2A3155] text-slate-200 border border-[#2E2A68] flex items-center justify-center gap-2 active:scale-95 transition"
    >
        <x-icon name="arrow-left" class="w-4 h-4" />
        <span>Halaman Sebelumnya</span>
    </button>
@endsection

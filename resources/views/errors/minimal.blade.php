@extends('errors.layout')

@section('code', $exception->getStatusCode() ?? 'Error')
@section('title', $exception->getMessage() ?: 'Terjadi Kendala')
@section('category', 'SISTEM POS')
@section('glow_color', 'bg-indigo-600')
@section('glow_color_secondary', 'bg-[#00AAA6]')
@section('accent_bar', 'bg-gradient-to-r from-indigo-500 via-[#4338CA] to-[#00AAA6]')
@section('badge_indicator_color', 'bg-indigo-400')
@section('badge_style', 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/40')
@section('icon_bg', 'bg-indigo-500/15 text-indigo-400 border border-indigo-500/30')

@section('icon')
    <x-icon name="alert-triangle" class="w-10 h-10 text-indigo-400" />
@endsection

@section('message')
    {{ $exception->getMessage() ?: 'Terjadi kendala saat memproses permintaan Anda. Silakan kembali ke menu kasir atau hubungi administrator outlet jika kendala berlanjut.' }}
@endsection

@section('actions')
    <a 
        href="{{ route('pos.cashier') }}" 
        class="w-full sm:w-auto px-6 py-3.5 rounded-2xl font-black text-xs uppercase tracking-wider bg-[#4338CA] hover:bg-[#3730A3] text-white shadow-xl shadow-[#4338CA]/30 flex items-center justify-center gap-2 active:scale-95 transition"
    >
        <x-icon name="store" class="w-4 h-4" />
        <span>Kembali ke Kasir POS</span>
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

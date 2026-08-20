@extends('errors.layout')

@section('code', '400')
@section('title', 'Permintaan Tidak Valid')
@section('category', 'SINTAKS & INPUT')
@section('glow_color', 'bg-amber-600')
@section('glow_color_secondary', 'bg-rose-600')
@section('accent_bar', 'bg-gradient-to-r from-amber-500 via-orange-500 to-rose-500')
@section('badge_indicator_color', 'bg-amber-400')
@section('badge_style', 'bg-amber-500/20 text-amber-300 border border-amber-500/40')
@section('icon_bg', 'bg-amber-500/15 text-amber-400 border border-amber-500/30')

@section('icon')
    <x-icon name="alert-triangle" class="w-10 h-10 text-amber-400" />
@endsection

@section('message')
    {{ $exception->getMessage() ?: 'Server tidak dapat memproses permintaan ini karena format data atau parameter formulir tidak valid. Silakan coba ulangi operasi sebelumnya dengan data yang benar.' }}
@endsection

@section('actions')
    <a 
        href="{{ route('pos.cashier') }}" 
        class="w-full sm:w-auto px-6 py-3.5 rounded-2xl font-black text-xs uppercase tracking-wider bg-[#4338CA] hover:bg-[#3730A3] text-white shadow-xl shadow-[#4338CA]/30 flex items-center justify-center gap-2 active:scale-95 transition"
    >
        <x-icon name="store" class="w-4 h-4" />
        <span>Kembali ke Kasir</span>
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

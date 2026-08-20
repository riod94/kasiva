@extends('errors.layout')

@section('code', '500')
@section('title', 'Kendala Server Internal')
@section('category', 'SERVER & LOGIKA')
@section('glow_color', 'bg-rose-600')
@section('glow_color_secondary', 'bg-[#4338CA]')
@section('accent_bar', 'bg-gradient-to-r from-rose-600 via-purple-600 to-[#4338CA]')
@section('badge_indicator_color', 'bg-rose-500')
@section('badge_style', 'bg-rose-500/20 text-rose-300 border border-rose-500/40')
@section('icon_bg', 'bg-rose-500/15 text-rose-400 border border-rose-500/30')

@section('icon')
    <x-icon name="server" class="w-10 h-10 text-rose-400" />
@endsection

@section('message')
    {{ $exception->getMessage() ?: 'Terjadi kendala teknis internal pada server saat memproses permintaan Anda. Tim teknis kami telah mencatat peristiwa ini untuk penanganan segera. Silakan segarkan halaman atau kembali beberapa saat lagi.' }}
@endsection

@if(config('app.debug') && $exception->getMessage())
    @section('details')
        <div class="text-[11px] text-rose-400 font-bold mb-1">Rincian Pengecualian (Debug Mode):</div>
        <div class="text-[10px] text-slate-300">{{ $exception->getMessage() }}</div>
        @if(method_exists($exception, 'getFile'))
            <div class="text-[9px] text-slate-400 mt-1">Berkas: {{ basename($exception->getFile()) }}:{{ $exception->getLine() }}</div>
        @endif
    @endsection
@endif

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
        <span>Kembali ke Kasir</span>
    </a>
@endsection

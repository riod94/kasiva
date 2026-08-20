@extends('errors.layout')

@section('code', '403')
@section('title', 'Akses Modul Dibatasi')
@section('category', 'KEAMANAN & HAK AKSES')
@section('glow_color', 'bg-amber-600')
@section('glow_color_secondary', 'bg-[#4338CA]')
@section('accent_bar', 'bg-gradient-to-r from-amber-500 via-[#4338CA] to-[#00AAA6]')
@section('badge_indicator_color', 'bg-amber-400')
@section('badge_style', 'bg-amber-500/20 text-amber-300 border border-amber-500/40')
@section('icon_bg', 'bg-amber-500/15 text-amber-400 border border-amber-500/30')

@section('icon')
    <x-icon name="lock-closed" class="w-10 h-10 text-amber-400" />
@endsection

@section('message')
    {{ $exception->getMessage() ?: 'Akun Anda tidak memiliki izin otorisasi yang memadai untuk mengakses halaman atau fitur ini. Silakan hubungi Owner atau Administrator outlet untuk peningkatan hak akses peran Anda.' }}
@endsection

@section('actions')
    <a 
        href="{{ route('pos.cashier') }}" 
        class="w-full sm:w-auto px-6 py-3.5 rounded-2xl font-black text-xs uppercase tracking-wider bg-[#4338CA] hover:bg-[#3730A3] text-white shadow-xl shadow-[#4338CA]/30 flex items-center justify-center gap-2 active:scale-95 transition"
    >
        <x-icon name="store" class="w-4 h-4" />
        <span>Kembali ke Kasir POS</span>
    </a>

    <a 
        href="{{ route('history.index') }}" 
        class="w-full sm:w-auto px-6 py-3.5 rounded-2xl font-bold text-xs uppercase tracking-wider bg-[#16192E] hover:bg-[#25215A] text-slate-200 border border-[#2E2A68] flex items-center justify-center gap-2 active:scale-95 transition"
    >
        <x-icon name="receipt" class="w-4 h-4" />
        <span>Riwayat Transaksi</span>
    </a>
@endsection

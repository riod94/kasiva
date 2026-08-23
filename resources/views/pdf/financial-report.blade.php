<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Laporan Kasiva POS</title>
<style>
  body{font-family: ui-sans-serif, sans-serif; font-size:11px; color:#0F172A; margin:24px;}
  h1{font-size:18px; margin:0 0 4px;}
  .muted{color:#64748B;}
  table{width:100%; border-collapse:collapse; margin-top:12px;}
  th,td{border:1px solid #E2E8F0; padding:6px 8px; text-align:left;}
  th{background:#1E1B4B; color:#fff; font-size:10px; text-transform:uppercase; letter-spacing:0.05em;}
  .right{text-align:right;}
  .badge{font-size:9px; font-weight:800; padding:2px 6px; border-radius:9999px; border:1px solid #E2E8F0;}
  .grid{display:grid; grid-template-columns: repeat(4,1fr); gap:12px; margin:16px 0;}
  .card{border:1px solid #E2E8F0; border-radius:12px; padding:12px;}
  .card b{font-size:14px;}
  @media print { body{margin:12px;} }
</style>
</head>
<body>
  <h1>Kasiva POS — Laporan Keuangan</h1>
  <div class="muted">Periode: {{ $period ?? 'SEMUA' }} @if(!empty($startDate)) {{ $startDate }} s/d {{ $endDate }} @endif • Dicetak: {{ now()->format('d M Y H:i') }}</div>
  <div class="grid">
    <div class="card"><div class="muted">Omset</div><b>Rp {{ number_format($omset ?? 0,0,',','.') }}</b><div class="muted">{{ $txCount ?? 0 }} transaksi</div></div>
    <div class="card"><div class="muted">Total HPP</div><b>Rp {{ number_format($cogsTotal ?? 0,0,',','.') }}</b><div class="muted">Margin {{ $grossMarginPercent ?? 0 }}%</div></div>
    <div class="card"><div class="muted">Laba Kotor</div><b>Rp {{ number_format($grossProfit ?? 0,0,',','.') }}</b></div>
    <div class="card"><div class="muted">Laba Bersih</div><b>Rp {{ number_format($netProfit ?? 0,0,',','.') }}</b><div class="muted">OPEX {{ $opexRatio ?? 0 }}% • Netto {{ $netMarginPercent ?? 0 }}%</div></div>
  </div>
  <table>
    <thead><tr><th>No Struk</th><th>Tanggal</th><th>Metode</th><th class="right">Total</th><th class="right">HPP</th><th class="right">Laba</th><th>Kasir</th><th>Status</th></tr></thead>
    <tbody>
      @forelse(($allTx ?? collect()) as $t)
        <tr><td style="font-family:monospace">{{ $t->receipt_number }}</td><td>{{ $t->created_at->format('d/m/Y H:i') }}</td><td>{{ $t->payment_method }}</td><td class="right">{{ number_format($t->total_amount,0,',','.') }}</td><td class="right">{{ number_format($t->total_hpp,0,',','.') }}</td><td class="right">{{ number_format($t->total_amount - $t->total_hpp,0,',','.') }}</td><td>{{ $t->cashier_name }}</td><td><span class="badge">{{ $t->status ?? 'COMPLETED' }}</span></td></tr>
      @empty
        <tr><td colspan="8" style="text-align:center; color:#64748B;">Belum ada transaksi</td></tr>
      @endforelse
    </tbody>
  </table>
  <table style="margin-top:16px">
    <thead><tr><th>Judul Pengeluaran</th><th>Kategori</th><th class="right">Jumlah</th><th>Tanggal</th><th>Catatan</th></tr></thead>
    <tbody>
      @forelse(($allExp ?? collect()) as $e)
        <tr><td>{{ $e->title }}</td><td>{{ $e->category }}</td><td class="right">{{ number_format($e->amount,0,',','.') }}</td><td>{{ \Carbon\Carbon::parse($e->expense_date)->format('d/m/Y') }}</td><td>{{ $e->notes ?? '-' }}</td></tr>
      @empty
        <tr><td colspan="5" style="text-align:center; color:#64748B;">Belum ada pengeluaran</td></tr>
      @endforelse
    </tbody>
  </table>
  <p class="muted" style="margin-top:16px; text-align:center;">Dokumen dihasilkan otomatis oleh Kasiva POS • kasiva.biz.id</p>
</body>
</html>

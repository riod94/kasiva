<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Kasiva POS</title>
@vite(['resources/js/offline/shell.js'])
<style>
body{margin:0;background:#272d48;color:#fff;font:14px system-ui}
main{max-width:1100px;margin:auto;padding:20px}
.top{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px}
.grid{display:grid;grid-template-columns:1fr 320px;gap:16px}
.products,.cart{background:#505b93;border-radius:18px;padding:16px}
.items{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:10px}
.item,button{border:0;border-radius:12px;padding:12px;background:#00aaa6;color:#fff;font-weight:700;cursor:pointer}
.item{background:#272d48;text-align:left}
.muted{color:#c8d0ff;font-size:12px}
.cart-row{display:flex;justify-content:space-between;border-bottom:1px solid #8696ed;padding:10px 0}
.pay{width:100%;margin-top:12px;background:#3edAd7;color:#272d48}
.empty{padding:30px;text-align:center;color:#c8d0ff}
@media(max-width:700px){.grid{grid-template-columns:1fr}.cart{position:sticky;bottom:0}}
</style>
</head>
<body>
<main>
<div class="top">
<div><strong>Kasiva POS</strong><div class="muted">Mode lokal — transaksi tersimpan di perangkat</div></div>
<select id="cart-select" aria-label="Pilih cart"></select>
<button id="new-cart">Cart Baru</button>
<span id="status">Offline</span>
</div>

<div class="grid">
<section class="products">
<input id="search" placeholder="Cari produk lokal..." style="width:100%;box-sizing:border-box;padding:12px;border-radius:10px;border:0;margin-bottom:12px">
<div id="products" class="items"><div class="empty">Memuat katalog lokal...</div></div>
</section>
<aside class="cart">
<h2>Cart</h2>
<div id="member-badge" style="margin-bottom:10px"></div>
<div class="items" style="grid-template-columns:1fr auto;gap:6px;margin-bottom:10px">
<input id="member-qr" placeholder="Scan/ketik QR Member (Enter)" style="padding:10px;border-radius:10px;border:0">
<button id="member-qr-btn" class="muted" style="background:#3edAd7;color:#272d48">Hubungkan</button>
<input id="member-phone" placeholder="HP Member (Enter)" style="padding:10px;border-radius:10px;border:0">
<button id="member-phone-btn" class="muted" style="background:#3edAd7;color:#272d48">Cari HP</button>
</div>
<div id="cart"></div>
<strong id="total">Rp 0</strong>
<button id="cash" class="pay">Bayar Tunai</button>
<button id="qris" class="pay">QRIS Statis</button>
</aside>
</div>

<div id="sync-banner"></div>
<section class="products" style="margin-top:16px">
<h2>Riwayat Lokal</h2>
<div id="history" class="empty">Belum ada transaksi lokal</div>
</section>

<section class="products" style="margin-top:16px">
<h2>Pengeluaran</h2>
<div class="items" style="grid-template-columns:1fr 1fr">
<input id="offline-expense-title" placeholder="Judul" style="width:100%;box-sizing:border-box;padding:8px;border-radius:8px;border:0">
<input id="offline-expense-amount" type="number" placeholder="Jumlah (Rp)" style="width:100%;box-sizing:border-box;padding:8px;border-radius:8px;border:0">
<input id="offline-expense-category" placeholder="Kategori" style="width:100%;box-sizing:border-box;padding:8px;border-radius:8px;border:0">
<input id="offline-expense-date" type="date" style="width:100%;box-sizing:border-box;padding:8px;border-radius:8px;border:0">
<input id="offline-expense-notes" placeholder="Catatan" style="width:100%;box-sizing:border-box;padding:8px;border-radius:8px;border:0">
</div>
<button id="save-expense" class="pay">Simpan Pengeluaran</button>
<div id="expenses-list" class="empty" style="margin-top:12px">Belum ada pengeluaran</div>
</section>

</main>
</body>
</html>

# AGENTS.md — Workspace Rules for Kasiva POS

## Aturan Konsistensi Pengkodean & Pengembangan Kasiva

1. **Bahasa & Komunikasi**:
   - Selalu gunakan Bahasa Indonesia yang profesional, sopan, dan jelas.

2. **Single Source of Truth**:
   - Dokumen di folder `docs/` (diproyeksikan dari `.system-brain/`) adalah acuan arsitektur resmi proyek.
   - Jangan pernah mengubah variabel warna brand tanpa mengacu pada token warna resmi (`#272D48`, `#505B93`, `#00AAA6`, `#8696ED`, `#3EDAD7`).

3. **Keamanan & Eksekusi Perintah**:
   - Dilarang menjalankan perintah terminal yang merusak (seperti `rm -rf`, `drop table`, atau perubahan konfigurasi database tanpa persetujuan).

4. **Kualitas Kode & Testing**:
   - Setiap fitur baru harus diuji dengan `php artisan test` dan `npm run build` sebelum dinyatakan selesai.
   - Gunakan `DB::transaction()` pada setiap operasi pemotongan stok atau pembentukan transaksi kasir.

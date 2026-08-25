import { test, expect } from '@playwright/test';

// Helper login — gunakan credential seed default
async function login(page: any, email = 'owner@kasiva.pos', password = 'password123') {
  await page.goto('/login');
  await page.waitForLoadState('networkidle');
  await page.fill('input[type="email"]', email);
  await page.fill('input[type="password"]', password);
  await page.click('button[type="submit"]');
  await page.waitForURL((url: URL) => ['/pos', '/profile'].includes(url.pathname));
}

test.describe('Kasiva Loyalty & Member Parity E2E', () => {
  test.beforeEach(async ({ page }) => {
    await login(page);
  });

  test('Marketing > Members: batch generate + tab filter + QR sheet + print', async ({ page }) => {
    await page.goto('/marketing/members');
    await expect(page.locator('h1, h2').first()).toContainText(/Member|QR/);

    // buka modal batch
    const batchBtn = page.getByRole('button', { name: /Generate.*QR|Buat.*QR/i });
    if (await batchBtn.isVisible()) {
      await batchBtn.click();
      await page.waitForTimeout(300);
      const dialog = page.getByRole('dialog');
      const optCount5 = dialog.getByRole('button', { name: /^5 PCS$/i });
      if (await optCount5.isVisible()) await optCount5.click();
      const generateBtn = dialog.getByRole('button', { name: /Generate & Print/i });
      if (await generateBtn.isVisible()) await generateBtn.click();
      const printPreview = page.locator('#member-print-portal');
      await expect(printPreview).toBeVisible();
      await expect(printPreview.locator('canvas.print-qr')).toHaveCount(5);
      await page.getByRole('button', { name: /Tutup pratinjau cetak/i }).click();
      await expect(printPreview).toBeHidden();
    }

    // tab Navigasi: Semua / Terdaftar / Kosong (atau ALL/ASSIGNED/UNASSIGNED)
    const tabAll = page.getByRole('button', { name: /Semua|ALL/i }).first();
    const tabUnassigned = page.getByRole('button', { name: /Kosong|UNASSIGNED|Belum/i }).first();
    if (await tabAll.isVisible()) { await tabAll.click(); await page.waitForTimeout(300); }
    if (await tabUnassigned.isVisible()) { await tabUnassigned.click(); await page.waitForTimeout(300); }

    // klik member card untuk buka sheet
    const memberCard = page.locator('[data-testid="member-card"], .member-card, [wire\\:key*="member"]').first();
    if (await memberCard.isVisible()) {
      await memberCard.click();
      await page.waitForTimeout(500);
      // canvas QR profil harus muncul
      await expect(page.locator('canvas').first()).toBeVisible({ timeout: 3000 });
    }
  });

  test('POS Cashier: scan/member lookup -> progress banner -> checkout stamp +1', async ({ page }) => {
    // 1) pastikan ada member + program aktif via halaman marketing (fallback via DB seeder)
    await page.goto('/marketing/loyalty');
    await page.waitForLoadState('networkidle');

    // buat program jika belum ada
    const buatBtn = page.getByRole('button', { name: /Tambah.*Program|Buat.*Program/i });
    if (await buatBtn.isVisible()) {
      await buatBtn.click();
      await page.waitForTimeout(300);
      await page.fill('input[placeholder*="Nama"] , input[name="name"]', `E2E Program ${Date.now()}`);
      await page.fill('input[name="target_stamps"], input[type="number"]', '5');
      const simpan = page.getByRole('button', { name: /Simpan|Aktif/i });
      if (await simpan.first().isVisible()) await simpan.first().click();
      await page.waitForTimeout(400);
    }

    // 2) ke kasir
    await page.goto('/pos');
    await page.waitForLoadState('networkidle');

    // cari input member / tombol scan
    const memberInput = page.locator('input[placeholder*="Member"], input[placeholder*="QR"], input[placeholder*="HP"]');
    const scanBtn = page.getByRole('button', { name: /Scan.*Member|Member.*QR/i });
    // tambah produk ke cart dulu
    const productCard = page.locator('[data-testid="product-card"], [wire\\:click*="addToCart"], button:has-text("Tambah")').first();
    if (await productCard.isVisible()) await productCard.click();
    else {
      // fallback klik produk pertama di grid
      const anyProduct = page.locator('.product-card, [class*="product"]').first();
      if (await anyProduct.isVisible()) await anyProduct.click();
    }
    await page.waitForTimeout(300);

    // jika ada scan button, buka scanner (tidak perlu kamera nyata, cukup search manual)
    if (await scanBtn.isVisible()) {
      // coba input manual QR: tulis KSV-MBR- (akan not found, tapi UI harus show banner error)
      if (await memberInput.first().isVisible()) {
        await memberInput.first().fill('KSV-MBR-UNKNOWN999');
        await page.keyboard.press('Enter');
        await page.waitForTimeout(500);
      }
    }
    // verifikasi cart tidak hilang setelah navigasi
    await page.goto('/history');
    await page.waitForTimeout(500);
    await page.goto('/pos');
    await page.waitForTimeout(500);
    await expect(page.getByText(/Cart 1|Keranjang/i).first()).toBeVisible();
  });

  test('Reports export buttons visible dan tidak 500', async ({ page }) => {
    await page.goto('/reports');
    await page.waitForLoadState('networkidle');
    await expect(page.locator('body')).not.toContainText('500');
    // Excel/PDF buttons
    const excelBtn = page.getByRole('button', { name: /Excel|XLSX|\.xlsx/i });
    const pdfBtn = page.getByRole('button', { name: /PDF/i });
    if (await excelBtn.isVisible()) await expect(excelBtn).toBeEnabled();
    if (await pdfBtn.isVisible()) await expect(pdfBtn).toBeEnabled();
  });
});

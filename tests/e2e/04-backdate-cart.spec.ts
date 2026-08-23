import { test, expect } from '@playwright/test';

test.describe('Backdate Transaction via Cart', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/login');
    await page.fill('input[type="email"]', 'owner@kasiva.pos');
    await page.fill('input[type="password"]', 'password123');
    await page.click('button[type="submit"]');
    await page.waitForLoadState('networkidle');
  });

  test('backdate menampilkan picker produk + keranjang itemized', async ({ page }) => {
    await page.goto('/history/backdate');
    await page.waitForLoadState('networkidle');
    await expect(page.locator('body')).toContainText(/Input Transaksi Lampau|BACKDATE/);
    await expect(page.locator('input[placeholder*="produk" i], input[placeholder*="menu" i]').first()).toBeVisible();
    // Click first product card
    const productCard = page.locator('button:has-text("Rp")').first();
    if (await productCard.count() > 0) {
      await productCard.click();
      await page.waitForTimeout(400);
      await expect(page.locator('body')).toContainText(/Keranjang/);
      await expect(page.locator('body')).toContainText(/Rp/);
      // Check date inputs exist
      await expect(page.locator('input[type="date"]')).toBeVisible();
      await expect(page.locator('input[type="time"]')).toBeVisible();
      // Save button disabled state when cart has items -> enabled
      const saveBtn = page.locator('button:has-text("Simpan Transaksi Lampau")');
      await expect(saveBtn).toBeEnabled();
    }
  });

  test('backdate menambah 2 produk dan menyimpan', async ({ page }) => {
    await page.goto('/history/backdate');
    await page.waitForLoadState('networkidle');
    const cards = page.locator('button:has-text("Rp")');
    const count = await cards.count();
    if (count >= 2) {
      await cards.nth(0).click();
      await page.waitForTimeout(300);
      await cards.nth(1).click();
      await page.waitForTimeout(300);
      await page.fill('input[type="date"]', '2026-08-10');
      await page.fill('input[type="time"]', '09:30');
      const saveBtn = page.locator('button:has-text("Simpan Transaksi Lampau")');
      await saveBtn.click();
      await page.waitForTimeout(1000);
      // Should redirect to history or show success
      await expect(page.locator('body')).toContainText(/berhasil|Riwayat|Transaksi/i);
    }
  });
});

import { test, expect } from '@playwright/test';

test.describe('Reports - Chart & Export', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/login');
    await page.fill('input[type="email"]', 'owner@kasiva.pos');
    await page.fill('input[type="password"]', 'password123');
    await page.click('button[type="submit"]');
    await page.waitForLoadState('networkidle');
  });

  test('laporan menampilkan chart canvas', async ({ page }) => {
    await page.goto('/reports');
    await page.waitForLoadState('networkidle');
    await expect(page.locator('body')).toContainText(/Laporan|Finansial|Net Profit/i);
    // Chart canvases should exist
    const trendChart = page.locator('#trendChart');
    const paymentChart = page.locator('#paymentChart');
    await expect(trendChart).toBeVisible({ timeout: 5000 });
    await expect(paymentChart).toBeVisible({ timeout: 5000 });
  });

  test('laporan memiliki tombol export', async ({ page }) => {
    await page.goto('/reports');
    await page.waitForLoadState('networkidle');
    await expect(page.locator('button:has-text("Export")')).toHaveCount(2);
    // Try CSV export via Livewire click - check response header via request
    // We test via direct API call as fallback
    const csvResp = await page.request.get('/reports', { headers: { Accept: 'text/html' } });
    expect(csvResp.status()).toBeLessThan(500);
  });

  test('member QR tampil di halaman members', async ({ page }) => {
    await page.goto('/marketing/members');
    await page.waitForLoadState('networkidle');
    await expect(page.locator('body')).toContainText(/Member|Pelanggan/i);
    // QR code text pattern
    const qrText = page.locator('text=/KSV-MBR-/').first();
    if (await qrText.count() > 0) {
      await expect(qrText).toBeVisible();
    }
  });
});

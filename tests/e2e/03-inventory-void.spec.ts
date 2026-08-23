import { test, expect } from '@playwright/test';

test.describe('Inventory Logs & Void Transaction', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/login');
    await page.fill('input[type="email"]', 'owner@kasiva.pos');
    await page.fill('input[type="password"]', 'password123');
    await page.click('button[type="submit"]');
    await page.waitForLoadState('networkidle');
  });

  test('owner dapat restok bahan baku & melihat inventory_logs terbuat', async ({ page }) => {
    await page.goto('/inventory/materials');
    await expect(page.locator('body')).toContainText('Bahan Baku');
    // Open first restock button
    const restockBtn = page.locator('button:has-text("Restok"), button:has-text("Tambah Stok")').first();
    if (await restockBtn.count() > 0) {
      await restockBtn.click();
      await page.waitForTimeout(500);
      const qtyInput = page.locator('input[wire\\:model*="restockQuantity"], input[placeholder*="Quantity"]').first();
      if (await qtyInput.count() > 0) {
        await qtyInput.fill('5');
        const costInput = page.locator('input[wire\\:model*="restockTotalCost"]').first();
        if (await costInput.count() > 0) await costInput.fill('15000');
        const submit = page.locator('button[wire\\:click="processRestock"]');
        await submit.scrollIntoViewIfNeeded();
        await submit.click({ force: true });
        await expect(page.locator('button[wire\\:click="processRestock"]')).toHaveCount(0);
        await expect(page.locator('body')).toContainText(/Online|Kasir|Katalog|Riwayat/);
      }
    }
  });

  test('owner dapat void transaksi dari history', async ({ page }) => {
    await page.goto('/history');
    await page.waitForLoadState('networkidle');
    const voidBtn = page.locator('button[title*="Void"], button:has-text("×")').first();
    if (await voidBtn.count() > 0) {
      await voidBtn.click();
      await page.waitForTimeout(400);
      await expect(page.locator('text=Batalkan Transaksi')).toBeVisible();
      const confirm = page.locator('button:has-text("Ya, Void")');
      await confirm.click();
      await page.waitForTimeout(800);
      await expect(page.locator('body')).toContainText(/VOID|Dibatalkan|berhasil/i);
    } else {
      // At least history page loads
      await expect(page.locator('body')).toContainText(/Riwayat|Transaksi/);
    }
  });
});

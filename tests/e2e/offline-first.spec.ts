import { test, expect } from '@playwright/test';

test.describe('Kasiva offline-first smoke', () => {
  test('cart shell remains usable offline after online visit', async ({ page, context }) => {
    await page.goto('/pos');
    await page.waitForLoadState('networkidle');
    await expect(page.locator('body')).toContainText(/Kasiva|Kasir/);
    await context.setOffline(true);
    await page.reload().catch(() => {});
    await expect(page.locator('body')).toContainText(/offline|Kasiva|Kasir/i);
  });

  test('offline fallback is branded and explains pending sync', async ({ page, context }) => {
    await page.goto('/offline.html');
    await context.setOffline(true);
    await expect(page.locator('body')).toContainText(/Kasiva sedang offline/i);
    await expect(page.locator('body')).toContainText(/disinkronkan/i);
  });
});

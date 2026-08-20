import { test, expect, Page } from '@playwright/test';
import * as path from 'path';
import * as fs from 'fs';

test.describe('Kasiva POS - Error Pages UI/UX Suite (400, 401, 403, 404, 419, 429, 500, 502, 503)', () => {
  const proofDir = path.join(process.cwd(), 'public/images/e2e-proof/errors');

  test.beforeAll(() => {
    if (!fs.existsSync(proofDir)) {
      fs.mkdirSync(proofDir, { recursive: true });
    }
  });

  const errorCodes = [
    { code: 400, name: '400-bad-request', title: 'Permintaan Tidak Valid' },
    { code: 401, name: '401-unauthorized', title: 'Sesi Autentikasi Diperlukan' },
    { code: 403, name: '403-forbidden', title: 'Akses Modul Dibatasi' },
    { code: 404, name: '404-not-found', title: 'Halaman Tidak Ditemukan' },
    { code: 419, name: '419-page-expired', title: 'Sesi Formulir Kedaluwarsa' },
    { code: 429, name: '429-rate-limited', title: 'Terlalu Banyak Permintaan' },
    { code: 500, name: '500-server-error', title: 'Kendala Server Internal' },
    { code: 502, name: '502-bad-gateway', title: 'Gateway Bermasalah' },
    { code: 503, name: '503-maintenance', title: 'Layanan Sedang Dalam Pemeliharaan' },
  ];

  for (const err of errorCodes) {
    test(`Verify & Capture Error ${err.code} in Dark & Light Mode`, async ({ page }) => {
      const response = await page.goto(`/error-preview/${err.code}`);
      expect(response?.status()).toBe(err.code);

      // Verify Header Logo & Error Card Title
      await expect(page.locator('header img[alt="Kasiva POS"]')).toBeVisible();
      await expect(page.locator('h1')).toContainText(err.title);

      // 1. Dark Mode Screenshot
      await page.evaluate(() => {
        document.documentElement.classList.add('dark');
        document.documentElement.classList.remove('light');
        localStorage.setItem('kasiva_theme', 'dark');
      });
      await page.waitForTimeout(200);
      await page.screenshot({ path: path.join(proofDir, `error-${err.name}-dark.png`), fullPage: true });

      // 2. Light Mode Screenshot
      await page.evaluate(() => {
        document.documentElement.classList.add('light');
        document.documentElement.classList.remove('dark');
        localStorage.setItem('kasiva_theme', 'light');
      });
      await page.waitForTimeout(200);
      await page.screenshot({ path: path.join(proofDir, `error-${err.name}-light.png`), fullPage: true });
    });
  }

  test('Verify Real RBAC 403 Screen when Cashier accesses Settings', async ({ page }) => {
    // Login as cashier
    await page.context().clearCookies();
    await page.goto('/login');
    await page.waitForLoadState('networkidle');
    await page.fill('input[type="email"]', 'kasir@kasiva.pos');
    await page.fill('input[type="password"]', 'password123');
    await page.click('button[type="submit"]');
    await page.waitForURL('**/pos');

    // Access Settings directly
    const response = await page.goto('/settings');
    expect(response?.status()).toBe(403);

    // Verify Custom 403 Page with exact RBAC message
    await expect(page.locator('h1')).toContainText('Akses Modul Dibatasi');
    await expect(page.getByText('Akses Ditolak: Anda tidak memiliki izin untuk mengakses pusat pengaturan sistem.')).toBeVisible();

    await page.screenshot({ path: path.join(proofDir, 'real-cashier-403-rbac-dark.png'), fullPage: true });

    // Light Mode
    await page.evaluate(() => {
      document.documentElement.classList.add('light');
      document.documentElement.classList.remove('dark');
      localStorage.setItem('kasiva_theme', 'light');
    });
    await page.waitForTimeout(200);
    await page.screenshot({ path: path.join(proofDir, 'real-cashier-403-rbac-light.png'), fullPage: true });
  });
});

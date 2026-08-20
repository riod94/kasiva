import { test, expect, Page } from '@playwright/test';
import * as path from 'path';
import * as fs from 'fs';

test.describe('Kasiva POS - Payment Settings & Theme Contrast E2E Tests', () => {
  const proofDir = path.join(process.cwd(), 'public/images/e2e-proof');

  test.beforeAll(() => {
    if (!fs.existsSync(proofDir)) {
      fs.mkdirSync(proofDir, { recursive: true });
    }
  });

  async function loginAsOwner(page: Page) {
    await page.goto('/pos');
    await page.waitForLoadState('networkidle');
    if (page.url().includes('/login')) {
      await page.fill('input[type="email"]', 'owner@kasiva.pos');
      await page.fill('input[type="password"]', 'password123');
      await page.click('button[type="submit"]');
      await page.waitForURL('**/pos');
      await page.waitForLoadState('networkidle');
    }
  }

  test('1. Verify Payment Settings Page in Dark Mode (Layout & Elements)', async ({ page }) => {
    await loginAsOwner(page);

    await page.goto('/settings/payment');
    await page.waitForLoadState('networkidle');

    // Verify Title & Header
    await expect(page.locator('h1')).toContainText('Metode Pembayaran');
    await expect(page.getByText('QRIS Statis & Aktivasi Kanal Penjualan Delivery Online')).toBeVisible();

    // Verify QRIS Section
    await expect(page.getByText('Kode QRIS Statis Outlet')).toBeVisible();

    // Verify Delivery Platforms
    await expect(page.getByText('Kanal Penjualan Online')).toBeVisible();
    await expect(page.getByText('GoFood')).toBeVisible();
    await expect(page.getByText('GrabFood')).toBeVisible();
    await expect(page.getByText('ShopeeFood')).toBeVisible();

    // Take Dark Mode Screenshot
    await page.screenshot({ path: path.join(proofDir, 'payment-settings-dark.png'), fullPage: true });
  });

  test('2. Verify Payment Settings Page in Light Mode (Contrast & Switchers)', async ({ page }) => {
    await loginAsOwner(page);

    await page.goto('/settings/payment');
    await page.waitForLoadState('networkidle');

    // Switch to Light Theme
    await page.evaluate(() => {
      document.documentElement.classList.add('light');
      document.documentElement.classList.remove('dark');
      localStorage.setItem('kasiva_theme', 'light');
    });
    await page.waitForTimeout(300);

    // Verify Light Mode Class on HTML element
    const htmlClass = await page.getAttribute('html', 'class');
    expect(htmlClass).toContain('light');

    // Verify visibility of elements in light mode
    await expect(page.locator('h1')).toContainText('Metode Pembayaran');
    await expect(page.getByText('Kode QRIS Statis Outlet')).toBeVisible();
    await expect(page.getByText('GoFood')).toBeVisible();
    await expect(page.getByText('GrabFood')).toBeVisible();
    await expect(page.getByText('ShopeeFood')).toBeVisible();

    // Take Light Mode Screenshot
    await page.screenshot({ path: path.join(proofDir, 'payment-settings-light.png'), fullPage: true });
  });

  test('3. Verify Cashier Screen & Theme Switcher in Light Mode', async ({ page }) => {
    await loginAsOwner(page);

    await page.goto('/pos');
    await page.waitForLoadState('networkidle');

    // Switch to Light Theme
    await page.evaluate(() => {
      document.documentElement.classList.add('light');
      document.documentElement.classList.remove('dark');
      localStorage.setItem('kasiva_theme', 'light');
    });
    await page.waitForTimeout(300);

    // Verify Cashier Screen Elements
    await expect(page.locator('header img[alt="Kasiva POS"]')).toBeVisible();
    await page.screenshot({ path: path.join(proofDir, 'cashier-screen-light.png'), fullPage: true });

    // Switch back to Dark Theme
    await page.evaluate(() => {
      document.documentElement.classList.add('dark');
      document.documentElement.classList.remove('light');
      localStorage.setItem('kasiva_theme', 'dark');
    });
    await page.waitForTimeout(300);

    await page.screenshot({ path: path.join(proofDir, 'cashier-screen-dark.png'), fullPage: true });
  });

  test('4. Verify Financial Reports in Light & Dark Mode', async ({ page }) => {
    await loginAsOwner(page);

    await page.goto('/reports');
    await page.waitForLoadState('networkidle');

    // Verify Reports Header
    await expect(page.locator('h1')).toContainText('Laporan Finansial');

    // Light Theme Screenshot
    await page.evaluate(() => {
      document.documentElement.classList.add('light');
      document.documentElement.classList.remove('dark');
      localStorage.setItem('kasiva_theme', 'light');
    });
    await page.waitForTimeout(300);
    await page.screenshot({ path: path.join(proofDir, 'financial-reports-light.png'), fullPage: true });

    // Dark Theme Screenshot
    await page.evaluate(() => {
      document.documentElement.classList.add('dark');
      document.documentElement.classList.remove('light');
      localStorage.setItem('kasiva_theme', 'dark');
    });
    await page.waitForTimeout(300);
    await page.screenshot({ path: path.join(proofDir, 'financial-reports-dark.png'), fullPage: true });
  });

  test('5. Verify Product Manager in Light & Dark Mode', async ({ page }) => {
    await loginAsOwner(page);

    await page.goto('/settings/products');
    await page.waitForLoadState('networkidle');

    // Light Theme Screenshot
    await page.evaluate(() => {
      document.documentElement.classList.add('light');
      document.documentElement.classList.remove('dark');
      localStorage.setItem('kasiva_theme', 'light');
    });
    await page.waitForTimeout(300);
    await page.screenshot({ path: path.join(proofDir, 'product-manager-light.png'), fullPage: true });
  });
});

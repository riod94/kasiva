import { test, expect, Page } from '@playwright/test';
import * as path from 'path';
import * as fs from 'fs';

const targets = [
  { name: 'web-desktop', viewport: { width: 1280, height: 800 }, isMobile: false },
  { name: 'mobile-web', viewport: { width: 390, height: 844 }, isMobile: true },
  { name: 'android-app', viewport: { width: 412, height: 915 }, isMobile: true },
  { name: 'ios-app', viewport: { width: 390, height: 844 }, isMobile: true },
];

async function loginAsOwner(page: Page) {
  await page.goto('/login');
  await page.waitForLoadState('networkidle');
  await page.fill('input[type="email"]', 'owner@kasiva.pos');
  await page.fill('input[type="password"]', 'password123');
  await page.click('button[type="submit"]');
  await page.waitForURL(url => ['/pos', '/profile'].includes(url.pathname));
}

targets.forEach((target) => {
  test.describe(`Full Flow E2E Suite - Target: ${target.name}`, () => {
    const targetDir = path.join(process.cwd(), 'tests/e2e/screenshots', target.name);

    test.beforeAll(() => {
      if (!fs.existsSync(targetDir)) {
        fs.mkdirSync(targetDir, { recursive: true });
      }
    });

    test.use({ viewport: target.viewport });

    test(`01 - Capture Landing Page & Mobile Onboarding [${target.name}]`, async ({ page }) => {
      if (target.isMobile) {
        await page.goto('/onboarding');
        await page.waitForLoadState('networkidle');
        await expect(page.getByText(/Kasir Cepat/i)).toBeVisible();
        await page.screenshot({ path: path.join(targetDir, '01-mobile-onboarding-slide1.png'), fullPage: true });
      } else {
        await page.goto('/');
        await page.waitForLoadState('networkidle');
        await expect(page).toHaveTitle(/Kasiva POS/);
        await page.screenshot({ path: path.join(targetDir, '01-landing-page.png'), fullPage: true });
      }
    });

    test(`02 - Capture Sign In Page [${target.name}]`, async ({ page }) => {
      await page.goto('/login');
      await page.waitForLoadState('networkidle');
      await expect(page.getByRole('heading', { name: /Masuk ke Kasiva/i })).toBeVisible();
      await page.screenshot({ path: path.join(targetDir, '02-signin-page.png'), fullPage: true });
    });

    test(`03 - Capture Sign Up Page [${target.name}]`, async ({ page }) => {
      await page.goto('/register');
      await page.waitForLoadState('networkidle');
      await expect(page.getByRole('heading', { name: /Daftarkan outlet/i })).toBeVisible();
      await page.screenshot({ path: path.join(targetDir, '03-signup-page.png'), fullPage: true });
    });

    test(`04 - Capture POS Cashier Screen [${target.name}]`, async ({ page }) => {
      await loginAsOwner(page);
      await page.goto('/pos');
      await page.waitForLoadState('networkidle');
      await expect(page.getByRole('link', { name: /Kasiva POS, buka kasir/i })).toBeVisible();
      await page.screenshot({ path: path.join(targetDir, '04-pos-cashier-screen.png'), fullPage: true });
    });

    test(`05 - Capture POS Checkout Modal [${target.name}]`, async ({ page }) => {
      await loginAsOwner(page);
      await page.goto('/pos');
      await page.waitForLoadState('networkidle');
      
      const productCard = page.locator('div[wire\\:click^="handleProductClick"]').first();
      if (await productCard.isVisible()) {
        await productCard.click();
        await page.waitForTimeout(500);
      }

      const addVariantBtn = page.getByRole('button', { name: /Tambahkan ke Pesanan/i });
      if (await addVariantBtn.isVisible()) {
        await addVariantBtn.click();
        await page.waitForTimeout(500);
      }

      const payBtn = page.getByRole('button', { name: /Bayar/i }).first();
      if (await payBtn.isVisible()) {
        await payBtn.click({ timeout: 3000 }).catch(() => {});
        await page.waitForTimeout(500);
        await page.screenshot({ path: path.join(targetDir, '05-pos-checkout-modal.png') });
      }
    });

    test(`06 - Capture Transaction History [${target.name}]`, async ({ page }) => {
      await loginAsOwner(page);
      await page.goto('/history');
      await page.waitForLoadState('networkidle');
      await expect(page.getByText(/Riwayat Transaksi/i)).toBeVisible();
      await page.screenshot({ path: path.join(targetDir, '06-transaction-history.png'), fullPage: true });
    });

    test(`07 - Capture Expense Manager [${target.name}]`, async ({ page }) => {
      await loginAsOwner(page);
      await page.goto('/expenses');
      await page.waitForLoadState('networkidle');
      await expect(page.getByRole('heading', { name: /Pengeluaran Operasional Toko/i })).toBeVisible();
      await page.screenshot({ path: path.join(targetDir, '07-expense-manager.png'), fullPage: true });
    });

    test(`08 - Capture Financial Reports [${target.name}]`, async ({ page }) => {
      await loginAsOwner(page);
      await page.goto('/reports');
      await page.waitForLoadState('networkidle');
      await expect(page.locator('h1')).toContainText('Laporan Finansial');
      await page.screenshot({ path: path.join(targetDir, '08-financial-reports.png'), fullPage: true });
    });

    test(`09 - Capture Settings Hub [${target.name}]`, async ({ page }) => {
      await loginAsOwner(page);
      await page.goto('/settings');
      await page.waitForLoadState('networkidle');
      await expect(page.getByText(/Pusat Pengaturan Outlet/i)).toBeVisible();
      await page.screenshot({ path: path.join(targetDir, '09-settings-hub.png'), fullPage: true });
    });

    test(`10 - Capture Product Manager & HPP [${target.name}]`, async ({ page }) => {
      await loginAsOwner(page);
      await page.goto('/settings/products');
      await page.waitForLoadState('networkidle');
      await expect(page.getByText(/Katalog Produk/i)).toBeVisible();
      await page.screenshot({ path: path.join(targetDir, '10-product-manager-hpp.png'), fullPage: true });
    });

    test(`11 - Capture Inventory Hub & Categories [${target.name}]`, async ({ page }) => {
      await loginAsOwner(page);
      await page.goto('/inventory');
      await page.waitForLoadState('networkidle');
      await expect(page.getByRole('heading', { name: /Manajemen Inventaris & Menu/i })).toBeVisible();
      await page.screenshot({ path: path.join(targetDir, '11-inventory-hub.png'), fullPage: true });

      await page.goto('/inventory/categories');
      await page.waitForLoadState('networkidle');
      await expect(page.getByRole('heading', { name: /Kategori Menu/i })).toBeVisible();
      await page.screenshot({ path: path.join(targetDir, '11-inventory-categories.png'), fullPage: true });
    });

    test(`12 - Capture Materials Library & Restock [${target.name}]`, async ({ page }) => {
      await loginAsOwner(page);
      await page.goto('/inventory/materials');
      await page.waitForLoadState('networkidle');
      await expect(page.getByText(/Bahan Baku/i)).toBeVisible();
      await page.screenshot({ path: path.join(targetDir, '12-inventory-materials.png'), fullPage: true });
    });

    test(`13 - Capture Variation Manager [${target.name}]`, async ({ page }) => {
      await loginAsOwner(page);
      await page.goto('/inventory/variations');
      await page.waitForLoadState('networkidle');
      await expect(page.getByRole('heading', { name: /Master Varian & Opsi Tambahan/i })).toBeVisible();
      await page.screenshot({ path: path.join(targetDir, '13-inventory-variations.png'), fullPage: true });
    });

    test(`14 - Capture Marketing Hub & Loyalty Stamps [${target.name}]`, async ({ page }) => {
      await loginAsOwner(page);
      await page.goto('/marketing');
      await page.waitForLoadState('networkidle');
      await expect(page.getByText(/Pusat Pemasaran/i)).toBeVisible();
      await page.screenshot({ path: path.join(targetDir, '14-marketing-hub.png'), fullPage: true });

      await page.goto('/marketing/loyalty');
      await page.waitForLoadState('networkidle');
      await expect(page.getByRole('heading', { name: /Program Loyalitas Stempel Digital/i })).toBeVisible();
      await page.screenshot({ path: path.join(targetDir, '14-marketing-loyalty.png'), fullPage: true });
    });

    test(`15 - Capture Discounts & Promo Vouchers [${target.name}]`, async ({ page }) => {
      await loginAsOwner(page);
      await page.goto('/marketing/discounts');
      await page.waitForLoadState('networkidle');
      await expect(page.getByRole('heading', { name: /Diskon & Voucher Promo/i })).toBeVisible();
      await page.screenshot({ path: path.join(targetDir, '15-marketing-discounts.png'), fullPage: true });
    });

    test(`16 - Capture Outlet & Payment Settings [${target.name}]`, async ({ page }) => {
      await loginAsOwner(page);
      await page.goto('/settings/outlet');
      await page.waitForLoadState('networkidle');
      await expect(page.getByText(/Profil Outlet/i)).toBeVisible();
      await page.screenshot({ path: path.join(targetDir, '16-settings-outlet.png'), fullPage: true });

      await page.goto('/settings/payment');
      await page.waitForLoadState('networkidle');
      await expect(page.locator('h1')).toContainText('Metode Pembayaran');
      await page.screenshot({ path: path.join(targetDir, '16-settings-payment.png'), fullPage: true });
    });

    test(`17 - Capture Staff Manager & User Profile [${target.name}]`, async ({ page }) => {
      await loginAsOwner(page);
      await page.goto('/settings/staff');
      await page.waitForLoadState('networkidle');
      await expect(page.getByRole('heading', { name: /Manajemen Staf & PIN Kasir/i })).toBeVisible();
      await page.screenshot({ path: path.join(targetDir, '17-settings-staff.png'), fullPage: true });

      await page.goto('/profile');
      await page.waitForLoadState('networkidle');
      await expect(page.getByRole('heading', { name: /Profil & Keamanan Akun/i })).toBeVisible();
      await page.screenshot({ path: path.join(targetDir, '17-user-profile.png'), fullPage: true });
    });

    test(`18 - Capture About & Legal Pages [${target.name}]`, async ({ page }) => {
      await page.goto('/about');
      await page.waitForLoadState('networkidle');
      await expect(page.locator('h1').first()).toBeVisible();
      await page.screenshot({ path: path.join(targetDir, '18-about-page.png'), fullPage: true });

      await page.goto('/privacy');
      await page.waitForLoadState('networkidle');
      await expect(page.locator('h1').first()).toBeVisible();
      await page.screenshot({ path: path.join(targetDir, '18-privacy-page.png'), fullPage: true });

      await page.goto('/terms');
      await page.waitForLoadState('networkidle');
      await expect(page.locator('h1').first()).toBeVisible();
      await page.screenshot({ path: path.join(targetDir, '18-terms-page.png'), fullPage: true });
    });
  });
});

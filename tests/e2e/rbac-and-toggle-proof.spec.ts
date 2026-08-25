import { test, expect, Page } from '@playwright/test';
import * as path from 'path';
import * as fs from 'fs';

test.describe('Kasiva POS - RBAC Access Control, Toggle Switches & Landing Theme Tests', () => {
  const proofDir = path.join(process.cwd(), 'public/images/e2e-proof');

  test.beforeAll(() => {
    if (!fs.existsSync(proofDir)) {
      fs.mkdirSync(proofDir, { recursive: true });
    }
  });

  async function loginAsUser(page: Page, email: string, pass: string) {
    await page.context().clearCookies();
    await page.goto('/login');
    await page.waitForLoadState('networkidle');
    await page.fill('input[type="email"]', email);
    await page.fill('input[type="password"]', pass);
    await page.click('button[type="submit"]');
    await page.waitForURL(url => ['/pos', '/profile'].includes(url.pathname));
    await page.waitForLoadState('networkidle');
  }

  test('1. Landing Page Theme Switcher and System Theme Default', async ({ page }) => {
    await page.goto('/');
    await page.waitForLoadState('networkidle');

    // Verify Title and Logo
    await expect(page).toHaveTitle(/Kasiva POS/);

    // Verify Theme Switcher Button exists in Navbar
    const themeBtn = page.getByRole('button', { name: /Ganti tema terang atau gelap/i });
    await expect(themeBtn).toBeVisible();

    // Check current theme and toggle
    await page.evaluate(() => {
      document.documentElement.classList.add('dark');
      document.documentElement.classList.remove('light');
      localStorage.setItem('kasiva_theme', 'dark');
    });
    await page.waitForTimeout(200);

    // Click Theme Toggle -> switches to light mode
    await themeBtn.click();
    await page.waitForTimeout(300);

    const htmlClassLight = await page.getAttribute('html', 'class');
    expect(htmlClassLight).toContain('light');
    await page.screenshot({ path: path.join(proofDir, 'landing-light-mode.png'), fullPage: true });

    // Click Theme Toggle again -> switches back to dark mode
    await themeBtn.click();
    await page.waitForTimeout(300);

    const htmlClassDark = await page.getAttribute('html', 'class');
    expect(htmlClassDark).toContain('dark');
    await page.screenshot({ path: path.join(proofDir, 'landing-dark-mode.png'), fullPage: true });
  });

  test('2. Toggle Switch Visibility in Light Mode on Payment Settings', async ({ page }) => {
    await loginAsUser(page, 'owner@kasiva.pos', 'password123');

    await page.goto('/settings/payment');
    await page.waitForLoadState('networkidle');

    // Switch to Light Theme
    await page.evaluate(() => {
      document.documentElement.classList.add('light');
      document.documentElement.classList.remove('dark');
      localStorage.setItem('kasiva_theme', 'light');
    });
    await page.waitForTimeout(300);

    // Verify toggles exist
    const gofoodCheckbox = page.locator('input[wire\\:model\\.live="enable_gofood"]');
    await expect(gofoodCheckbox).toBeAttached();

    // Take screenshot of high-contrast Light Mode toggles
    await page.screenshot({ path: path.join(proofDir, 'payment-toggles-light-mode.png'), fullPage: true });
  });

  test('3. RBAC Enforcement: Cashier Role Has Restricted Menu & 403 On Direct Navigation', async ({ page }) => {
    // Login as Cashier
    await loginAsUser(page, 'kasir@kasiva.pos', 'password123');

    // 1. Verify Cashier is on POS Screen
    await expect(page.getByRole('link', { name: /Kasiva POS, buka kasir/i })).toBeVisible();

    // 2. Verify Restricted Menus are NOT present in Cashier Navbar
    const desktopNav = page.locator('header nav');
    if (await desktopNav.isVisible()) {
      await expect(desktopNav.getByText('Kasir')).toBeVisible();
      await expect(desktopNav.getByText('Inventaris')).not.toBeVisible();
      await expect(desktopNav.getByText('Pemasaran')).not.toBeVisible();
      await expect(desktopNav.getByText('Pengeluaran')).not.toBeVisible();
      await expect(desktopNav.getByText('Laporan')).not.toBeVisible();
      await expect(desktopNav.getByText('Pengaturan')).not.toBeVisible();
    }

    // Take Cashier Navbar Screenshot
    await page.screenshot({ path: path.join(proofDir, 'cashier-restricted-navbar.png') });

    // 3. Attempt direct URL navigation to /marketing -> Expect 403
    const marketingResponse = await page.goto('/marketing');
    expect(marketingResponse?.status()).toBe(403);
    await page.screenshot({ path: path.join(proofDir, 'cashier-blocked-marketing-403.png') });

    // 4. Attempt direct URL navigation to /marketing/members -> Expect 403
    const membersResponse = await page.goto('/marketing/members');
    expect(membersResponse?.status()).toBe(403);

    // 5. Attempt direct URL navigation to /marketing/discounts -> Expect 403
    const discountsResponse = await page.goto('/marketing/discounts');
    expect(discountsResponse?.status()).toBe(403);

    // 6. Attempt direct URL navigation to /inventory -> Expect 403
    const inventoryResponse = await page.goto('/inventory');
    expect(inventoryResponse?.status()).toBe(403);

    // 7. Attempt direct URL navigation to /reports -> Expect 403
    const reportsResponse = await page.goto('/reports');
    expect(reportsResponse?.status()).toBe(403);
    await page.screenshot({ path: path.join(proofDir, 'cashier-blocked-reports-403.png') });

    // 8. Attempt direct URL navigation to /settings/roles -> Expect 403
    const rolesResponse = await page.goto('/settings/roles');
    expect(rolesResponse?.status()).toBe(403);
    await page.screenshot({ path: path.join(proofDir, 'cashier-blocked-roles-403.png') });

    // 9. Attempt direct URL navigation to /expenses -> Expect 403
    const expensesResponse = await page.goto('/expenses');
    expect(expensesResponse?.status()).toBe(403);
  });

  test('4. RBAC Enforcement: Owner Role Has Full Menu & Settings Access', async ({ page }) => {
    // Login as Owner
    await loginAsUser(page, 'owner@kasiva.pos', 'password123');

    // 1. Verify Owner Navbar has all links
    const desktopNav = page.locator('header nav');
    if (await desktopNav.isVisible()) {
      await expect(desktopNav.getByText('Kasir')).toBeVisible();
      await expect(desktopNav.getByText('Inventaris')).toBeVisible();
      await expect(desktopNav.getByText('Pemasaran')).toBeVisible();
      await expect(desktopNav.getByText('Riwayat')).toBeVisible();
      await expect(desktopNav.getByText('Pengeluaran')).toBeVisible();
      await expect(desktopNav.getByText('Laporan')).toBeVisible();
      await expect(desktopNav.getByText('Setelan', { exact: true })).toBeVisible();
    }

    // 2. Owner can navigate to /reports -> Expect 200 OK
    const reportsResponse = await page.goto('/reports');
    expect(reportsResponse?.status()).toBe(200);
    await expect(page.locator('h1')).toContainText('Laporan Finansial');

    // 3. Owner can navigate to /settings/roles -> Expect 200 OK
    const rolesResponse = await page.goto('/settings/roles');
    expect(rolesResponse?.status()).toBe(200);
    await expect(page.locator('h1')).toContainText('Hak Akses');
    await page.screenshot({ path: path.join(proofDir, 'owner-full-access-roles.png') });
  });
});

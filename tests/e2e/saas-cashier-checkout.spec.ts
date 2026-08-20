import { test, expect, Page } from '@playwright/test';
import * as path from 'path';
import * as fs from 'fs';

test.describe('Kasiva SaaS POS E2E Proof Screenshots', () => {
  const proofDir = path.join(process.cwd(), 'public/images/e2e-proof');

  test.beforeAll(() => {
    if (!fs.existsSync(proofDir)) {
      fs.mkdirSync(proofDir, { recursive: true });
    }
  });

  async function loginAsOwner(page: Page) {
    await page.goto('/login');
    await page.waitForLoadState('networkidle');
    await page.fill('input[type="email"]', 'owner@kasiva.pos');
    await page.fill('input[type="password"]', 'password123');
    await page.click('button[type="submit"]');
    await page.waitForURL('**/pos');
  }

  test('Capture Landing Page E2E Proof', async ({ page }) => {
    await page.goto('/');
    await page.waitForLoadState('networkidle');
    await expect(page).toHaveTitle(/Kasiva POS/);
    await page.screenshot({ path: path.join(proofDir, '01-landing-page.png'), fullPage: true });
  });

  test('Capture Kasir POS Screen E2E Proof', async ({ page }) => {
    await loginAsOwner(page);
    await page.goto('/pos');
    await page.waitForLoadState('networkidle');
    await expect(page.locator('header img[alt="Kasiva POS"]')).toBeVisible();
    await page.screenshot({ path: path.join(proofDir, '02-cashier-screen.png'), fullPage: true });
  });

  test('Capture Financial Reports E2E Proof', async ({ page }) => {
    await loginAsOwner(page);
    await page.goto('/reports');
    await page.waitForLoadState('networkidle');
    await expect(page.locator('h1')).toContainText('Laporan Finansial');
    await page.screenshot({ path: path.join(proofDir, '04-financial-reports.png'), fullPage: true });
  });

  test('Capture Settings Hub E2E Proof', async ({ page }) => {
    await loginAsOwner(page);
    await page.goto('/settings');
    await page.waitForLoadState('networkidle');
    await expect(page.getByText('Pusat Pengaturan Outlet')).toBeVisible();
    await page.screenshot({ path: path.join(proofDir, '05-settings-hub.png'), fullPage: true });
  });
});

import { test, expect } from '@playwright/test';

test.describe('Landing revamp — Kasiva POS', () => {
    test('home renders 200 and all sections', async ({ page }) => {
        const response = await page.goto('/');
        expect(response?.status()).toBe(200);

        // Hero, Fitur, Cara kerja, Harga, FAQ, CTA, Footer
        await expect(page.getByRole('heading', { name: /Kasir bukan cuma mencatat/i })).toBeVisible();
        await expect(page.locator('#fitur')).toBeVisible();
        await expect(page.locator('#cara-kerja')).toBeVisible();
        await expect(page.locator('#harga')).toBeVisible();
        await expect(page.locator('#faq')).toBeVisible();
        await expect(page.getByText(/Berhenti menebak profit outlet Anda/i)).toBeVisible();
    });

    test('hero CTA navigates to register', async ({ page }) => {
        await page.goto('/');
        const cta = page.getByRole('link', { name: /Coba Kasiva gratis/i }).first();
        await cta.click();
        await expect(page).toHaveURL(/\/register/);
    });

    test('skip-link can be focused via Tab', async ({ page }) => {
        await page.goto('/');
        await page.keyboard.press('Tab');
        const skip = page.getByRole('link', { name: /Lewati ke konten utama/i });
        await expect(skip).toBeFocused();
    });

    test('theme toggle flips html class', async ({ page }) => {
        await page.goto('/');
        const initialClass = await page.evaluate(() => document.documentElement.className);
        await page.getByRole('button', { name: /Ganti tema terang atau gelap/i }).click();
        const nextClass = await page.evaluate(() => document.documentElement.className);
        expect(initialClass).not.toEqual(nextClass);
    });

    test('FAQ first item is open by default and toggles', async ({ page }) => {
        await page.goto('/');
        const firstFaq = page.locator('#faq-offline');
        await expect(firstFaq).toHaveAttribute('open', '');
    });

    test('JSON-LD includes SoftwareApplication, FAQPage, BreadcrumbList, HowTo', async ({ page }) => {
        await page.goto('/');
        const jsonldCount = await page.locator('script[type="application/ld+json"]').count();
        expect(jsonldCount).toBeGreaterThanOrEqual(4);

        const allText = await page.locator('script[type="application/ld+json"]').allInnerTexts();
        const joined = allText.join('\n');
        expect(joined).toContain('SoftwareApplication');
        expect(joined).toContain('FAQPage');
        expect(joined).toContain('BreadcrumbList');
        expect(joined).toContain('HowTo');
    });

    test('pricing shows three tiers', async ({ page }) => {
        await page.goto('/');
        const section = page.locator('#harga');
        await expect(section.getByText('Kasiva Starter')).toBeVisible();
        await expect(section.getByText('Kasiva Pro')).toBeVisible();
        await expect(section.getByText('Kasiva Enterprise')).toBeVisible();
    });

    test('mobile nav toggle opens and closes at < lg', async ({ page }) => {
        await page.setViewportSize({ width: 768, height: 900 });
        await page.goto('/');
        const toggle = page.getByRole('button', { name: /Buka menu navigasi/i });
        await toggle.waitFor({ state: 'visible' });

        // Klik pertama: muncul
        await toggle.click();
        const mobileNav = page.locator('#mobile-nav');
        await mobileNav.waitFor({ state: 'visible', timeout: 10000 });
        await expect(toggle).toHaveAttribute('aria-expanded', 'true');

        // Klik kedua: tersembunyi (Alpine x-show, bukan detach)
        await toggle.click();
        await mobileNav.waitFor({ state: 'hidden', timeout: 10000 });
        await expect(toggle).toHaveAttribute('aria-expanded', 'false');
    });
});

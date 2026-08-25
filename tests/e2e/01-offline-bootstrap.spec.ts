import { test, expect, type BrowserContext } from '@playwright/test';

const TEST_USER = 'kasir@kasiva.pos';
const TEST_PASS = 'password123';

test.describe('Phase 1: Offline bootstrap and reload', () => {
    test('catalog persists across offline reload after online bootstrap (AC-POS-01, AC-POS-02)', async ({ page, context }) => {
        // --- 1. Login online ---
        await page.goto('/login');
        await page.fill('form input[type="email"]', TEST_USER);
        await page.fill('form input[type="password"]', TEST_PASS);
        await page.click('button[type="submit"]');
        await page.waitForURL(url => ['/pos', '/profile'].includes(url.pathname), { timeout: 30000 });

        // --- 2. Navigate to local-first POS ---
        await page.goto('/app/pos');
        await page.waitForLoadState('networkidle', { timeout: 20000 });

        // --- 3. Wait for catalog bootstrap (products loaded from API → IndexedDB) ---
        await page.waitForSelector('#products .item', { timeout: 20000 });
        const productCountBefore = await page.locator('#products .item').count();
        expect(productCountBefore).toBeGreaterThan(0);

        // --- 4. Wait for service worker to be active ---
        await page.evaluate(async () => {
            await navigator.serviceWorker.ready;
            if (!navigator.serviceWorker.controller) {
                await new Promise<void>(resolve => {
                    navigator.serviceWorker.addEventListener('controllerchange', () => resolve(), { once: true });
                });
            }
        });

        // --- 5. Reload online so the SW caches the page HTML + JS ---
        await page.reload();
        await page.waitForLoadState('networkidle', { timeout: 20000 });

        // --- 6. Go offline and reload — catalog must survive from IndexedDB ---
        await context.setOffline(true);
        await page.reload();
        await page.waitForLoadState('load', { timeout: 15000 });

        // AC-POS-02: Same UI, same URL, catalog still visible after offline reload
        expect(page.url()).toContain('/app/pos');
        await expect(page.locator('#products .item').first()).toBeVisible();
        expect(await page.locator('#products .item').count()).toBe(productCountBefore);

        // --- 6. Offline checkout (AC-POS-03) ---
        await page.locator('#products .item').first().click();

        // Handle the checkout alert dialog
        page.once('dialog', dialog => dialog.accept());

        await page.click('#cash');
        await page.waitForTimeout(1500);

        // Transaction should appear in local history with PENDING_SYNC
        await page.waitForSelector('#history .cart-row');
        const historyRows = await page.locator('#history .cart-row').count();
        expect(historyRows).toBeGreaterThanOrEqual(1);
        await expect(page.locator('#history')).toContainText('PENDING_SYNC');

        // --- 7. Reload again — transaction must survive (AC-POS-04 crash safety) ---
        await page.reload();
        await page.waitForLoadState('load', { timeout: 15000 });
        await expect(page.locator('#history .cart-row').first()).toBeVisible();
        await expect(page.locator('#history')).toContainText('PENDING_SYNC');

        // --- 8. Expense save offline ---
        await page.fill('#offline-expense-title', 'Test Expense Offline');
        await page.fill('#offline-expense-amount', '50000');
        await page.fill('#offline-expense-category', 'OPERATIONAL');
        page.once('dialog', dialog => dialog.accept());
        await page.click('#save-expense');
        await page.waitForTimeout(1000);
        await expect(page.locator('#expenses-list')).toContainText('Test Expense Offline');
    });
});

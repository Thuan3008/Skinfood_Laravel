import fs from 'fs';
import path from 'path';
import { createRequire } from 'module';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const require = createRequire(import.meta.url);
const { chromium } = require('playwright');

const baseUrl = 'http://127.0.0.1:8001';
const output = path.join(__dirname, 'screenshots');

async function login(page, username, password) {
    await page.goto(`${baseUrl}/dang-nhap`, { waitUntil: 'domcontentloaded' });
    await page.locator('input[name="username"]').fill(username);
    await page.locator('input[name="password"]').fill(password);
    await Promise.all([
        page.waitForURL(url => !url.pathname.endsWith('/dang-nhap')),
        page.locator('form').getByRole('button', { name: 'Đăng nhập' }).click(),
    ]);
}

(async () => {
    fs.mkdirSync(output, { recursive: true });
    const browser = await chromium.launch({
        headless: true,
        executablePath: 'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe',
    });
    const context = await browser.newContext({ viewport: { width: 1440, height: 900 }, deviceScaleFactor: 1 });
    const page = await context.newPage();

    await page.goto(baseUrl, { waitUntil: 'domcontentloaded' });
    await page.screenshot({ path: path.join(output, '01-trang-chu.png') });

    await login(page, 'admin@skinfood.com', '123456');
    await page.goto(`${baseUrl}/admin`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1500);
    await page.screenshot({ path: path.join(output, '02-quan-tri.png') });
    if (process.env.DASHBOARD_ONLY === '1') {
        console.log(`Saved dashboard screenshot to ${output}`);
        process.exit(0);
    }

    const userContext = await browser.newContext({ viewport: { width: 1440, height: 900 }, deviceScaleFactor: 1 });
    const userPage = await userContext.newPage();
    await login(userPage, 'user@skinfood.com', '123456');
    await userPage.goto(`${baseUrl}/san-pham/11`, { waitUntil: 'networkidle' });
    await Promise.all([
        userPage.waitForLoadState('networkidle'),
        userPage.locator('form[action*="/gio-hang/"]').first().getByRole('button').click(),
    ]);
    await userPage.goto(`${baseUrl}/thanh-toan`, { waitUntil: 'networkidle' });
    await userPage.locator('select[name="payment"]').selectOption('BankTransfer');
    await Promise.all([
        userPage.waitForLoadState('networkidle'),
        userPage.locator('form[action$="/thanh-toan"]').getByRole('button', { name: 'Đặt hàng' }).click(),
    ]);
    await userPage.waitForSelector('#payment-panel');
    await userPage.waitForFunction(() => {
        const image = document.querySelector('#vietqr-api-image');
        const canvas = document.querySelector('#vietqr-code canvas');
        return (image && image.naturalWidth > 0) || (canvas && canvas.offsetWidth > 0);
    }, { timeout: 15000 });
    await userPage.screenshot({ path: path.join(output, '03-thanh-toan-vietqr.png') });

    await browser.close();
    console.log(`Saved screenshots to ${output}`);
})();

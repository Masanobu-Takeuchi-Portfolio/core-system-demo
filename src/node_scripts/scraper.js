/**
 * Puppeteerを使用したスクレイピングスクリプト
 *
 * 使い方:
 *   node scraper.js <URL> [セレクタ]
 *
 * 引数:
 *   URL       - スクレイピング対象のURL（必須）
 *   セレクタ  - 取得する要素のCSSセレクタ（省略時は全体を取得）
 *
 * 出力: JSON形式で結果を標準出力に返す
 */

const puppeteer = require('puppeteer');

(async () => {
    const args = process.argv.slice(2);
    const url = args[0];
    const selector = args[1] || null;

    if (!url) {
        console.error(JSON.stringify({ error: 'URLが指定されていません。' }));
        process.exit(1);
    }

    let browser;
    try {
        const launchOptions = {
            headless: 'new',
            args: [
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--disable-dev-shm-usage',
                '--disable-gpu',
            ],
        };

        // Docker環境などでシステムのChromiumを使用する場合
        if (process.env.PUPPETEER_EXECUTABLE_PATH) {
            launchOptions.executablePath = process.env.PUPPETEER_EXECUTABLE_PATH;
        }

        browser = await puppeteer.launch(launchOptions);

        const page = await browser.newPage();

        // ユーザーエージェントを設定
        await page.setUserAgent(
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
        );

        // タイムアウトを30秒に設定
        await page.setDefaultNavigationTimeout(30000);

        // ページに遷移
        await page.goto(url, { waitUntil: 'networkidle2' });

        // ページタイトルを取得
        const title = await page.title();

        // メタディスクリプションを取得
        const metaDescription = await page.evaluate(() => {
            const meta = document.querySelector('meta[name="description"]');
            return meta ? meta.getAttribute('content') : '';
        });

        let elements = [];

        if (selector) {
            // 指定セレクタの要素を取得
            elements = await page.evaluate((sel) => {
                const nodes = document.querySelectorAll(sel);
                return Array.from(nodes).map((node) => ({
                    tag: node.tagName.toLowerCase(),
                    text: node.innerText ? node.innerText.trim() : '',
                    html: node.innerHTML ? node.innerHTML.trim() : '',
                    href: node.getAttribute('href') || '',
                    src: node.getAttribute('src') || '',
                }));
            }, selector);
        } else {
            // セレクタ未指定の場合、主要な要素を取得
            elements = await page.evaluate(() => {
                const results = [];

                // 見出し要素を取得
                const headings = document.querySelectorAll('h1, h2, h3');
                headings.forEach((h) => {
                    results.push({
                        tag: h.tagName.toLowerCase(),
                        text: h.innerText ? h.innerText.trim() : '',
                        html: '',
                        href: '',
                        src: '',
                    });
                });

                // リンク要素を取得
                const links = document.querySelectorAll('a[href]');
                links.forEach((a) => {
                    results.push({
                        tag: 'a',
                        text: a.innerText ? a.innerText.trim() : '',
                        html: '',
                        href: a.getAttribute('href') || '',
                        src: '',
                    });
                });

                // 画像要素を取得
                const images = document.querySelectorAll('img[src]');
                images.forEach((img) => {
                    results.push({
                        tag: 'img',
                        text: img.getAttribute('alt') || '',
                        html: '',
                        href: '',
                        src: img.getAttribute('src') || '',
                    });
                });

                return results;
            });
        }

        // スクリーンショットをBase64で取得
        const screenshot = await page.screenshot({ encoding: 'base64', fullPage: false });

        // 結果をJSON形式で出力
        const result = {
            success: true,
            url: url,
            title: title,
            meta_description: metaDescription,
            selector: selector,
            elements_count: elements.length,
            elements: elements,
            screenshot: screenshot,
        };

        console.log(JSON.stringify(result));
    } catch (error) {
        console.error(
            JSON.stringify({
                success: false,
                error: error.message,
                url: url,
            })
        );
        process.exit(1);
    } finally {
        if (browser) {
            await browser.close();
        }
    }
})();

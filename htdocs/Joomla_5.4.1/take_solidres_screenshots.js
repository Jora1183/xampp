const { chromium } = require('playwright');

(async () => {
  let browser;
  try {
    browser = await chromium.launch({ headless: true });
    const page = await browser.newPage();
    await page.setViewportSize({ width: 1280, height: 900 });

    console.log('Navigating to booking page...');
    await page.goto('http://localhost/Joomla_5.4.1/search-book', { waitUntil: 'networkidle', timeout: 30000 });

    // Full page screenshot
    await page.screenshot({
      path: 'C:/xampp/htdocs/Joomla_5.4.1/solidres_fullpage.png',
      fullPage: true
    });
    console.log('Full page screenshot taken');

    // Viewport screenshot (top of page)
    await page.screenshot({
      path: 'C:/xampp/htdocs/Joomla_5.4.1/solidres_viewport_top.png',
      fullPage: false
    });
    console.log('Viewport top screenshot taken');

    // Scroll down a bit and screenshot
    await page.evaluate(() => window.scrollBy(0, 300));
    await page.waitForTimeout(500);
    await page.screenshot({
      path: 'C:/xampp/htdocs/Joomla_5.4.1/solidres_scrolled.png',
      fullPage: false
    });
    console.log('Scrolled screenshot taken');

    // Check for elements
    const anchorNav = await page.$('.sr-anchor-nav, [class*="anchor-nav"], .sr-roomtype-anchor');
    console.log('Anchor nav found:', anchorNav !== null);

    const progressBar = await page.$('.sr-reservation-stepper, .sr-wizard, [class*="progress"], [class*="stepper"]');
    console.log('Progress bar/stepper found:', progressBar !== null);

    const title = await page.title();
    console.log('Page title:', title);

    const url = page.url();
    console.log('Current URL:', url);

  } catch (e) {
    console.error('Error:', e.message);
  } finally {
    if (browser) await browser.close();
  }
})();

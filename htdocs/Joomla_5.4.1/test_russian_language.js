const playwright = require('playwright');

(async () => {
  console.log('Starting Playwright browser test...\n');
  
  // Launch browser
  const browser = await playwright.chromium.launch({ headless: false });
  const context = await browser.newContext();
  const page = await context.newPage();
  
  try {
    console.log('Step 1: Opening setup_russian.php...');
    await page.goto('http://localhost/Joomla_5.4.1/setup_russian.php');
    await page.waitForLoadState('networkidle');
    await page.screenshot({ path: 'setup_russian_result.png' });
    console.log('✓ Setup script executed! Screenshot saved: setup_russian_result.png\n');
    
    // Wait a bit to see the result
    await page.waitForTimeout(2000);
    
    console.log('Step 2: Opening Joomla site...');
    await page.goto('http://localhost/Joomla_5.4.1/');
    await page.waitForLoadState('networkidle');
    await page.screenshot({ path: 'joomla_site_russian.png', fullPage: true });
    console.log('✓ Site loaded! Screenshot saved: joomla_site_russian.png\n');
    
    // Check for Russian text
    const bodyText = await page.textContent('body');
    const hasRussian = /[А-Яа-яЁё]/.test(bodyText);
    
    console.log('Step 3: Verifying Russian language...');
    if (hasRussian) {
      console.log('✓ SUCCESS! Russian text detected on the page!');
      console.log('✓ Language configuration is working!\n');
    } else {
      console.log('⚠ WARNING: No Russian text detected.');
      console.log('   The site may still be in English or needs cache clearing.\n');
    }
    
    // Get page title
    const title = await page.title();
    console.log('Page Title:', title);
    
    // Check if there's any visible Russian text in headers
    const headers = await page.$$eval('h1, h2, h3, h4, h5, h6', elements => 
      elements.map(el => el.textContent.trim())
    );
    console.log('\nHeaders found:');
    headers.forEach(header => {
      if (header) console.log(' -', header);
    });
    
    console.log('\n✓ Test completed! Browser will stay open for 10 seconds...');
    await page.waitForTimeout(10000);
    
  } catch (error) {
    console.error('Error:', error.message);
  } finally {
    await browser.close();
    console.log('\n✓ Browser closed.');
  }
})();

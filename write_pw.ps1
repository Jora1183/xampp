$scriptPath = 'C:\Users\vyugo\AppData\Roaming\npm\node_modules\@playwright\mcp\node_modules\pw_diag.js'
$chromePath = 'C:\Users\vyugo\AppData\Local\ms-playwright\chromium-1208\chrome-win64\chrome.exe'
Write-Host "Chrome exists: $(Test-Path $chromePath)"
$doubleSlashPath = $chromePath.Replace('\', '\')
$js = @"
const { chromium } = require('playwright');
(async () => {
  const browser = await chromium.launch({
    headless: true,
    executablePath: '$doubleSlashPath'
  });
  const page = await browser.newPage();
  await page.goto('http://localhost/Joomla_5.4.1/ru?tmpl=base_map&Itemid=125', { waitUntil: 'networkidle', timeout: 30000 });
  const galleryInfo = await page.evaluate(() => {
    const results = [];
    const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
    let node;
    while ((node = walker.nextNode())) {
      const text = node.textContent.trim();
      if (text.includes('\u0413\u0430\u043b\u043b\u0435\u0440\u0435\u044f') || text.includes('\u041a\u0430\u0440\u0442\u0430 \u0431\u0430\u0437\u044b')) {
        const el = node.parentElement;
        const parent = el ? el.parentElement : null;
        const inHeader = el ? Boolean(el.closest('header, nav')) : false;
        results.push({
          matchedText: text,
          elementTag: el ? el.tagName : null,
          elementClassName: el ? el.className : null,
          parentTag: parent ? parent.tagName : null,
          parentClassName: parent ? parent.className : null,
          parentOuterHTML: parent ? parent.outerHTML.substring(0, 500) : null,
          isInHeader: inHeader
        });
      }
    }
    return results;
  });
  console.log('=== 1. GALLEREJA / KARTA BAZY ===');
  console.log(JSON.stringify(galleryInfo, null, 2));
  const gridInfo = await page.evaluate(() => {
    const el = document.querySelector('.site-grid');
    if (!el) return { found: false };
    const cs = window.getComputedStyle(el);
    return {
      found: true, className: el.className, display: cs.display,
      gridTemplateColumns: cs.gridTemplateColumns,
      gridTemplateRows: cs.gridTemplateRows,
      gridTemplateAreas: cs.gridTemplateAreas,
      outerHTMLPreview: el.outerHTML.substring(0, 300)
    };
  });
  console.log('\n=== 2. site-grid LAYOUT ===');
  console.log(JSON.stringify(gridInfo, null, 2));
  const childrenInfo = await page.evaluate(() => {
    const grid = document.querySelector('.site-grid');
    if (!grid) return [];
    return Array.from(grid.children).map((child, i) => {
      const cs = window.getComputedStyle(child);
      return {
        index: i, tag: child.tagName, id: child.id || '',
        className: child.className,
        offsetWidth: child.offsetWidth, offsetHeight: child.offsetHeight,
        gridColumn: cs.gridColumn, gridRow: cs.gridRow,
        innerTextPreview: child.innerText ? child.innerText.substring(0, 80).replace(/\n/g, ' ') : ''
      };
    });
  });
  console.log('\n=== 3. site-grid CHILDREN ===');
  console.log(JSON.stringify(childrenInfo, null, 2));
  await browser.close();
})().catch(e => { console.error('ERROR:', e.message); process.exit(1); });
"@
Set-Content -Path $scriptPath -Value $js -Encoding UTF8
Write-Host "Script written."
Select-String "executablePath" $scriptPath

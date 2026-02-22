const { chromium } = require('playwright');
(async () => {
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage();
  await page.goto('http://localhost/Joomla_5.4.1/ru?tmpl=base_map&Itemid=125', { waitUntil: 'networkidle', timeout: 30000 });

  // ── 1. Find elements containing 'Галлерея' and 'Карта базы' ──────────────
  const galleryInfo = await page.evaluate(() => {
    const results = [];
    const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
    let node;
    while ((node = walker.nextNode())) {
      const text = node.textContent.trim();
      if (text.includes('Галлерея') || text.includes('Карта базы')) {
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

  console.log('=== 1. ГАЛЛЕРЕЯ / КАРТА БАЗЫ ELEMENTS ===');
  console.log(JSON.stringify(galleryInfo, null, 2));

  // ── 2. .site-grid computed style ─────────────────────────────────────────
  const gridInfo = await page.evaluate(() => {
    const el = document.querySelector('.site-grid');
    if (!el) return { found: false };
    const cs = window.getComputedStyle(el);
    return {
      found: true,
      className: el.className,
      display: cs.display,
      gridTemplateColumns: cs.gridTemplateColumns,
      gridTemplateRows: cs.gridTemplateRows,
      gridTemplateAreas: cs.gridTemplateAreas,
      outerHTMLPreview: el.outerHTML.substring(0, 300)
    };
  });

  console.log('\n=== 2. .site-grid LAYOUT ===');
  console.log(JSON.stringify(gridInfo, null, 2));

  // ── 3. Direct children of .site-grid ────────────────────────────────────
  const childrenInfo = await page.evaluate(() => {
    const grid = document.querySelector('.site-grid');
    if (!grid) return [];
    return Array.from(grid.children).map((child, i) => {
      const cs = window.getComputedStyle(child);
      return {
        index: i,
        tag: child.tagName,
        id: child.id || '',
        className: child.className,
        offsetWidth: child.offsetWidth,
        offsetHeight: child.offsetHeight,
        gridColumn: cs.gridColumn,
        gridRow: cs.gridRow,
        innerTextPreview: child.innerText ? child.innerText.substring(0, 80).replace(/\n/g, ' ') : ''
      };
    });
  });

  console.log('\n=== 3. .site-grid DIRECT CHILDREN ===');
  console.log(JSON.stringify(childrenInfo, null, 2));

  await browser.close();
})().catch(e => { console.error('ERROR:', e.message); process.exit(1); });

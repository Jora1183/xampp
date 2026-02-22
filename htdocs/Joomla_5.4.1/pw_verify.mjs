import { chromium } from 'playwright';
import { writeFileSync } from 'fs';

const VIEWPORT = { width: 1280, height: 900 };

const pages = [
  {
    name: 'services',
    url: 'http://localhost/Joomla_5.4.1/services_preview.php',
    sections: [
      { label: 'header+pool',  y: 0    },
      { label: 'pool-pricing', y: 550  },
      { label: 'grill',        y: 1350 },
      { label: 'cafe',         y: 2150 },
      { label: 'transfer',     y: 3000 },
      { label: 'sports',       y: 3850 },
      { label: 'misc+day',     y: 4700 },
      { label: 'cta',          y: 5500 },
    ],
  },
  {
    name: 'accommodations',
    url: 'http://localhost/Joomla_5.4.1/accommodations_preview.php',
    sections: [
      { label: 'header+house1', y: 0    },
      { label: 'house1-info',   y: 700  },
      { label: 'house2',        y: 1500 },
      { label: 'house3',        y: 2800 },
      { label: 'house4',        y: 4100 },
      { label: 'house5',        y: 5400 },
      { label: 'house6',        y: 6700 },
      { label: 'tents+cta',     y: 8000 },
    ],
  },
];

const browser = await chromium.launch();
const ctx     = await browser.newContext({ viewport: VIEWPORT });

const results = [];

for (const p of pages) {
  const page = await ctx.newPage();
  await page.goto(p.url, { waitUntil: 'networkidle', timeout: 15000 }).catch(() => {});
  await page.waitForTimeout(800);

  for (const { label, y } of p.sections) {
    await page.evaluate(n => window.scrollTo(0, n), y);
    await page.waitForTimeout(300);
    const path = `C:/xampp/htdocs/Joomla_5.4.1/pw_${p.name}_${label}.png`;
    await page.screenshot({ path, clip: { x: 0, y: 0, width: 1280, height: 900 } });
    results.push(`${p.name}/${label} → ${path}`);
  }

  // Full page
  const fullPath = `C:/xampp/htdocs/Joomla_5.4.1/pw_${p.name}_FULL.png`;
  await page.evaluate(() => window.scrollTo(0, 0));
  await page.screenshot({ path: fullPath, fullPage: true });
  results.push(`${p.name}/FULL → ${fullPath}`);

  await page.close();
}

await browser.close();
writeFileSync('C:/xampp/htdocs/Joomla_5.4.1/pw_results.txt', results.join('\n'));
console.log('Done:\n' + results.join('\n'));

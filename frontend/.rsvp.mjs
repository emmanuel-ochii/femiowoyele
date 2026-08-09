import { chromium } from '@playwright/test';
const out = process.argv[2], label = process.argv[3];
const b = await chromium.launch({ channel: 'chrome' });
const p = await b.newPage({ viewport: { width: 1440, height: 1000 }, deviceScaleFactor: 2 });
const errs = [];
p.on('pageerror', (e) => errs.push(String(e)));
p.on('console', (m) => m.type() === 'error' && errs.push(m.text().slice(0, 120)));
await p.goto('http://127.0.0.1:5173/rsvp', { waitUntil: 'networkidle' });
await p.waitForTimeout(1400);
await p.screenshot({ path: `${out}/rsvp-${label}.png`, fullPage: true });
const txt = await p.locator('body').innerText();
console.log(label.padEnd(7),
  '| form present:', await p.locator('#rsvp-name').count() > 0,
  '| deadline shown:', txt.includes('11 August 2026'),
  '| closed msg:', txt.includes('RSVPs have closed'),
  '| errors:', errs.length ? errs[0] : 'none');
await b.close();

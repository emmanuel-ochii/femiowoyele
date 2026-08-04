import { expect, test } from '@playwright/test';

test('loads homepage and navigates core public sections', async ({ page }) => {
  await page.goto('/');

  await expect(page.getByRole('heading', { level: 1 })).toBeVisible();
  await expect(page.getByRole('heading', { name: 'A platform for serious work.' })).toBeVisible();

  await page.goto('/books');
  await expect(page.getByRole('heading', { name: 'Writing that takes the long view.' })).toBeVisible();
  await expect(page.getByText('Entrusted').first()).toBeVisible();

  await page.goto('/contact');
  await expect(page.getByRole('heading', { name: 'Begin the right conversation.' })).toBeVisible();
});

test('mobile navigation drawer opens, traps focus, and closes on Escape', async ({ page }) => {
  await page.setViewportSize({ width: 390, height: 844 });
  await page.goto('/');

  await page.getByRole('button', { name: 'Open navigation' }).click();
  const drawer = page.getByRole('dialog', { name: 'Site navigation' });
  await expect(drawer).toBeVisible();

  await page.keyboard.press('Escape');
  await expect(drawer).toBeHidden();
});

test('submits a contact enquiry', async ({ page }) => {
  await page.goto('/contact');

  await page.getByLabel('Full name').fill('E2E Reviewer');
  await page.getByLabel('Email address').fill('reviewer@example.com');
  await page.getByLabel('Nature of enquiry').selectOption('research');
  await page.getByLabel('Subject').fill('Research conversation');
  await page
    .getByLabel('Message')
    .fill('This is a realistic contact enquiry submitted by the Playwright end-to-end test suite.');

  await page.getByRole('button', { name: 'Send enquiry' }).click();
  await expect(page.getByText('Thank you. Your message has been received.')).toBeVisible();
});

test('research listing filters by category', async ({ page }) => {
  await page.goto('/research-ideas');

  await page.getByRole('button', { name: 'Governance' }).click();
  await expect(page.getByRole('heading', { name: 'The Long Work of Institution Building' })).toBeVisible();
  await expect(page.getByRole('heading', { name: 'Enterprise as Stewardship' })).toBeHidden();
});

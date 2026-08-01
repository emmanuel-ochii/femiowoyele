import { expect, test } from '@playwright/test';

test('loads homepage and navigates core public sections', async ({ page }) => {
  await page.goto('/');

  await expect(page.getByRole('heading', { name: 'Femi Owoyele' }).first()).toBeVisible();
  await expect(page.getByText('Built for substance')).toBeVisible();

  await page.getByRole('link', { name: 'Books' }).first().click();
  await expect(page.getByRole('heading', { name: 'Authorship with a long view.' })).toBeVisible();
  await expect(page.getByText('Entrusted').first()).toBeVisible();

  await page.getByRole('link', { name: 'Contact' }).first().click();
  await expect(page.getByRole('heading', { name: 'Begin the right conversation.' })).toBeVisible();
});

test('submits a contact enquiry', async ({ page }) => {
  await page.goto('/contact');

  await page.getByLabel('Name').fill('E2E Reviewer');
  await page.getByLabel('Email').fill('reviewer@example.com');
  await page.getByLabel('Type').selectOption('research');
  await page.getByLabel('Subject').fill('Research conversation');
  await page
    .getByLabel('Message')
    .fill('This is a realistic contact enquiry submitted by the Playwright end-to-end test suite.');

  await page.getByRole('button', { name: 'Send enquiry' }).click();
  await expect(page.getByText('Thank you. Your message has been received.')).toBeVisible();
});

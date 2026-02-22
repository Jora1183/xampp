// @ts-check
const { test, expect } = require('@playwright/test');

// Clear localStorage before each test for a clean slate
test.beforeEach(async ({ page }) => {
  await page.goto('./');
  await page.evaluate(() => localStorage.removeItem('nextask_tasks'));
  await page.reload();
});

// ─── Page Load ───────────────────────────────────────────────────────────────

test.describe('Page Load', () => {
  test('has correct title', async ({ page }) => {
    await expect(page).toHaveTitle(/NexTask/);
  });

  test('shows app header with logo', async ({ page }) => {
    await expect(page.locator('h1')).toHaveText('NexTask');
  });

  test('shows zero stats on fresh load', async ({ page }) => {
    await expect(page.locator('#total-tasks')).toHaveText('0');
    await expect(page.locator('#pending-tasks')).toHaveText('0');
  });

  test('shows empty state message', async ({ page }) => {
    await expect(page.locator('#empty-state')).toBeVisible();
    await expect(page.locator('#empty-state p')).toContainText('No tasks found');
  });

  test('task input is visible and focusable', async ({ page }) => {
    const input = page.locator('#task-input');
    await expect(input).toBeVisible();
    await input.click();
    await expect(input).toBeFocused();
  });
});

// ─── Adding Tasks ────────────────────────────────────────────────────────────

test.describe('Adding Tasks', () => {
  test('adds a task via form submit', async ({ page }) => {
    await page.locator('#task-input').fill('Buy groceries');
    await page.locator('#task-form button[type="submit"]').click();

    await expect(page.locator('.task-item')).toHaveCount(1);
    await expect(page.locator('.task-text').first()).toHaveText('Buy groceries');
  });

  test('adds a task by pressing Enter', async ({ page }) => {
    await page.locator('#task-input').fill('Read a book');
    await page.keyboard.press('Enter');

    await expect(page.locator('.task-item')).toHaveCount(1);
    await expect(page.locator('.task-text').first()).toHaveText('Read a book');
  });

  test('clears input after adding a task', async ({ page }) => {
    await page.locator('#task-input').fill('Write tests');
    await page.keyboard.press('Enter');

    await expect(page.locator('#task-input')).toHaveValue('');
  });

  test('does not add empty task', async ({ page }) => {
    await page.locator('#task-input').fill('');
    await page.locator('#task-form button[type="submit"]').click();

    await expect(page.locator('.task-item')).toHaveCount(0);
  });

  test('does not add whitespace-only task', async ({ page }) => {
    await page.locator('#task-input').fill('   ');
    await page.keyboard.press('Enter');

    await expect(page.locator('.task-item')).toHaveCount(0);
  });

  test('adds multiple tasks', async ({ page }) => {
    for (const task of ['Task A', 'Task B', 'Task C']) {
      await page.locator('#task-input').fill(task);
      await page.keyboard.press('Enter');
    }

    await expect(page.locator('.task-item')).toHaveCount(3);
  });

  test('hides empty state after adding a task', async ({ page }) => {
    await page.locator('#task-input').fill('Something');
    await page.keyboard.press('Enter');

    await expect(page.locator('#empty-state')).toBeHidden();
  });
});

// ─── Stats Counter ───────────────────────────────────────────────────────────

test.describe('Stats Counter', () => {
  test('updates total and pending count when task is added', async ({ page }) => {
    await page.locator('#task-input').fill('New task');
    await page.keyboard.press('Enter');

    await expect(page.locator('#total-tasks')).toHaveText('1');
    await expect(page.locator('#pending-tasks')).toHaveText('1');
  });

  test('updates pending count when task is completed', async ({ page }) => {
    await page.locator('#task-input').fill('Task one');
    await page.keyboard.press('Enter');

    await page.locator('.checkbox-container').first().click();

    await expect(page.locator('#total-tasks')).toHaveText('1');
    await expect(page.locator('#pending-tasks')).toHaveText('0');
  });

  test('decrements total when task is deleted', async ({ page }) => {
    await page.locator('#task-input').fill('Delete me');
    await page.keyboard.press('Enter');

    await page.locator('.btn-delete').first().click();

    await expect(page.locator('#total-tasks')).toHaveText('0');
    await expect(page.locator('#pending-tasks')).toHaveText('0');
  });
});

// ─── Completing Tasks ────────────────────────────────────────────────────────

test.describe('Completing Tasks', () => {
  test('marks a task as completed on checkbox click', async ({ page }) => {
    await page.locator('#task-input').fill('Complete me');
    await page.keyboard.press('Enter');

    await page.locator('.checkbox-container').first().click();

    await expect(page.locator('.task-item').first()).toHaveClass(/completed/);
  });

  test('toggles task back to pending on second click', async ({ page }) => {
    await page.locator('#task-input').fill('Toggle me');
    await page.keyboard.press('Enter');

    await page.locator('.checkbox-container').first().click();
    await page.locator('.checkbox-container').first().click();

    await expect(page.locator('.task-item').first()).not.toHaveClass(/completed/);
    await expect(page.locator('#pending-tasks')).toHaveText('1');
  });
});

// ─── Deleting Tasks ──────────────────────────────────────────────────────────

test.describe('Deleting Tasks', () => {
  test('removes a task on delete button click', async ({ page }) => {
    await page.locator('#task-input').fill('Delete me');
    await page.keyboard.press('Enter');

    await page.locator('.btn-delete').first().click();

    await expect(page.locator('.task-item')).toHaveCount(0);
  });

  test('shows empty state after last task is deleted', async ({ page }) => {
    await page.locator('#task-input').fill('Only task');
    await page.keyboard.press('Enter');

    await page.locator('.btn-delete').first().click();

    await expect(page.locator('#empty-state')).toBeVisible();
  });

  test('deletes the correct task when multiple exist', async ({ page }) => {
    for (const t of ['Alpha', 'Beta', 'Gamma']) {
      await page.locator('#task-input').fill(t);
      await page.keyboard.press('Enter');
    }

    // Delete the first visible task (newest first = Gamma)
    await page.locator('.btn-delete').first().click();

    const remaining = page.locator('.task-text');
    await expect(remaining).toHaveCount(2);
    await expect(remaining.first()).not.toHaveText('Gamma');
  });
});

// ─── Filtering ───────────────────────────────────────────────────────────────

test.describe('Filtering', () => {
  test.beforeEach(async ({ page }) => {
    // Add two tasks, complete one
    for (const t of ['Pending task', 'Done task']) {
      await page.locator('#task-input').fill(t);
      await page.keyboard.press('Enter');
    }
    // Complete "Done task" (newest is first, so index 0)
    await page.locator('.checkbox-container').first().click();
  });

  test('All filter shows all tasks', async ({ page }) => {
    await page.locator('.filter-btn[data-filter="all"]').click();
    await expect(page.locator('.task-item')).toHaveCount(2);
  });

  test('Pending filter shows only pending tasks', async ({ page }) => {
    await page.locator('.filter-btn[data-filter="pending"]').click();
    await expect(page.locator('.task-item')).toHaveCount(1);
    await expect(page.locator('.task-text').first()).toHaveText('Pending task');
  });

  test('Completed filter shows only completed tasks', async ({ page }) => {
    await page.locator('.filter-btn[data-filter="completed"]').click();
    await expect(page.locator('.task-item')).toHaveCount(1);
    await expect(page.locator('.task-item').first()).toHaveClass(/completed/);
  });

  test('active filter button is highlighted', async ({ page }) => {
    const pendingBtn = page.locator('.filter-btn[data-filter="pending"]');
    await pendingBtn.click();
    await expect(pendingBtn).toHaveClass(/active/);
    await expect(page.locator('.filter-btn[data-filter="all"]')).not.toHaveClass(/active/);
  });

  test('empty state shown when no tasks match filter', async ({ page }) => {
    // Delete the pending task so only completed remains
    await page.locator('.filter-btn[data-filter="pending"]').click();
    await page.locator('.btn-delete').first().click();

    // Switch to pending — should be empty
    await page.locator('.filter-btn[data-filter="pending"]').click();
    await expect(page.locator('#empty-state')).toBeVisible();
  });
});

// ─── Sorting ─────────────────────────────────────────────────────────────────

test.describe('Sorting', () => {
  test.beforeEach(async ({ page }) => {
    for (const t of ['Banana', 'Apple', 'Cherry']) {
      await page.locator('#task-input').fill(t);
      await page.keyboard.press('Enter');
      // Small delay so timestamps differ
      await page.waitForTimeout(10);
    }
  });

  test('newest first is the default order', async ({ page }) => {
    const texts = await page.locator('.task-text').allTextContents();
    expect(texts[0]).toBe('Cherry');
    expect(texts[2]).toBe('Banana');
  });

  test('oldest first reverses the order', async ({ page }) => {
    await page.locator('#sort-tasks').selectOption('oldest');
    const texts = await page.locator('.task-text').allTextContents();
    expect(texts[0]).toBe('Banana');
    expect(texts[2]).toBe('Cherry');
  });

  test('alphabetical sort orders tasks A-Z', async ({ page }) => {
    await page.locator('#sort-tasks').selectOption('alphabetical');
    const texts = await page.locator('.task-text').allTextContents();
    expect(texts[0]).toBe('Apple');
    expect(texts[1]).toBe('Banana');
    expect(texts[2]).toBe('Cherry');
  });
});

// ─── Persistence ─────────────────────────────────────────────────────────────

test.describe('LocalStorage Persistence', () => {
  test('tasks survive a page reload', async ({ page }) => {
    await page.locator('#task-input').fill('Persistent task');
    await page.keyboard.press('Enter');

    await page.reload();

    await expect(page.locator('.task-item')).toHaveCount(1);
    await expect(page.locator('.task-text').first()).toHaveText('Persistent task');
  });

  test('completed state survives a page reload', async ({ page }) => {
    await page.locator('#task-input').fill('Will be done');
    await page.keyboard.press('Enter');

    await page.locator('.checkbox-container').first().click();
    await page.reload();

    await expect(page.locator('.task-item').first()).toHaveClass(/completed/);
  });
});

# Account module and double-entry accounting

This document describes the **Account Module** hub and the **general ledger accounting** layer added so the system can support proper double-entry bookkeeping (debits, credits, journals, chart of accounts, general ledger, and trial balance). It also clarifies how this relates to the older operational “Profit & Loss” and “Balance Sheet” screens.

---

## Goals

- Provide a **chart of accounts** with standard account types: asset, liability, equity, revenue, expense.
- Support **journal entries** with multiple lines, each line having either a debit or a credit (not both).
- Enforce **double-entry**: total debits must equal total credits before a journal can be posted.
- Expose a **general ledger** view per account with date range and running balance (respecting debit-normal vs credit-normal accounts).
- Provide a **trial balance** for a period: posted debits and credits by account; grand totals must match when the books are in balance.
- Keep **sales, receivables, and invoicing** flows as they are today, while documenting that **meaningful financial statements** should eventually be driven from posted journals—not from the legacy order/stock summaries alone.

---

## Where to start in the UI

1. **SuperAdmin sidebar** → **Receivables** → **Account Module**  
   Route: `GET /admin/account-module` (`admin.account-module`)

2. On the Account Module page, the top card **“General ledger accounting”** links to:
   - Chart of accounts  
   - Journals (list)  
   - New journal  
   - General ledger  
   - Trial balance  

The same page explains that **legacy operational reports** (Profit & Loss, Balance Sheet, Financial Oversight) aggregate **orders and stock** data and are **not** substitutes for statements built from the GL until postings are automated or entered manually.

---

## Database schema

Migration file:

`database/migrations/2026_05_13_120000_create_double_entry_accounting_tables.php`

### `gl_accounts` (chart of accounts)

| Column        | Purpose                                      |
|---------------|----------------------------------------------|
| `code`        | Unique account code (e.g. `1000`, `4000`)    |
| `name`        | Display name                                 |
| `type`        | `asset`, `liability`, `equity`, `revenue`, `expense` |
| `parent_id`   | Optional hierarchy (self-FK to `gl_accounts`) |
| `is_active`   | Whether the account can be selected on new journals |
| `description` | Optional notes                               |

The migration also **seeds** a starter chart (e.g. Cash, A/R, Inventory, A/P, Equity, Sales, COGS, Operating expenses).

### `journal_entries`

| Column        | Purpose                                      |
|---------------|----------------------------------------------|
| `entry_date`  | Accounting date                              |
| `reference`   | Optional external reference                  |
| `description` | Optional header text                         |
| `status`      | `draft` or `posted`                          |
| `user_id`     | Who created the entry (nullable FK)          |
| `posted_at`   | Set when status becomes `posted`             |
| `store_id`    | Optional FK for future multi-store GL        |
| `source_type` / `source_id` | Optional polymorphic link for future automation (e.g. order id) |

### `journal_lines`

| Column             | Purpose                          |
|--------------------|----------------------------------|
| `journal_entry_id` | Parent journal                 |
| `gl_account_id`    | Account                          |
| `line_description` | Optional line memo               |
| `debit` / `credit` | Amounts; one side should be positive per line |

Posted journals are intended to be **immutable** from a business perspective (no edit UI for posted entries in v1); drafts can be deleted and recreated.

---

## Models

| Model          | File                         |
|----------------|------------------------------|
| `GlAccount`    | `app/Models/GlAccount.php`   |
| `JournalEntry` | `app/Models/JournalEntry.php` |
| `JournalLine`  | `app/Models/JournalLine.php` |

`GlAccount::isDebitNormal()` returns true for **asset** and **expense** (normal debit balance); **liability**, **equity**, and **revenue** use credit-normal logic in the general ledger running balance.

---

## HTTP routes

All routes use the `admin` prefix and `auth` middleware like the rest of the admin panel. Group name: `admin.accounting.*`.

| Method | URI | Name | Controller action |
|--------|-----|------|---------------------|
| GET | `/admin/accounting/chart-of-accounts` | `admin.accounting.chart-of-accounts` | Gl chart list + add form |
| POST | `/admin/accounting/chart-of-accounts` | `admin.accounting.chart-of-accounts.store` | Create account |
| PUT | `/admin/accounting/chart-of-accounts/{glAccount}` | `admin.accounting.chart-of-accounts.update` | Update account |
| POST | `/admin/accounting/chart-of-accounts/{glAccount}/toggle` | `admin.accounting.chart-of-accounts.toggle` | Active / inactive |
| GET | `/admin/accounting/journals` | `admin.accounting.journals.index` | Journal list |
| GET | `/admin/accounting/journals/create` | `admin.accounting.journals.create` | New journal form |
| POST | `/admin/accounting/journals` | `admin.accounting.journals.store` | Save draft (balanced) |
| GET | `/admin/accounting/journals/{journalEntry}` | `admin.accounting.journals.show` | View / post / delete draft |
| POST | `/admin/accounting/journals/{journalEntry}/post` | `admin.accounting.journals.post` | Post to GL |
| DELETE | `/admin/accounting/journals/{journalEntry}` | `admin.accounting.journals.destroy` | Delete draft only |
| GET | `/admin/accounting/general-ledger` | `admin.accounting.general-ledger` | GL register + filters |
| GET | `/admin/accounting/trial-balance` | `admin.accounting.trial-balance` | Trial balance + filters |

The **Account Module** landing route remains:

- `GET /admin/account-module` → `admin.account-module` → `AccountModuleController@index`

---

## Controllers

| Controller | File | Responsibility |
|------------|------|------------------|
| `AccountModuleController` | `app/Http/Controllers/admin/AccountModuleController.php` | Hub view only |
| `GlAccountController` | `app/Http/Controllers/admin/GlAccountController.php` | Chart of accounts CRUD + toggle |
| `JournalEntryController` | `app/Http/Controllers/admin/JournalEntryController.php` | Draft journals, validate balance, post, delete draft |
| `GeneralLedgerController` | `app/Http/Controllers/admin/GeneralLedgerController.php` | GL register and trial balance queries |

---

## Views

Under `resources/views/admin/accounting/`:

- `chart-of-accounts.blade.php` — list, add account, edit modals, toggle active  
- `journals/index.blade.php` — paginated journal list  
- `journals/create.blade.php` — multi-line journal form (dynamic add row)  
- `journals/show.blade.php` — lines, totals, post / delete draft  
- `general-ledger.blade.php` — account + date filters, opening balance, lines with running balance  
- `trial-balance.blade.php` — date range, all active accounts, debit/credit columns, totals  

Hub:

- `resources/views/admin/account-module.blade.php` — links and the distinction between GL accounting and legacy reports  

---

## Deployment checklist

1. Run migrations on the target environment:

   ```bash
   php artisan migrate
   ```

2. Confirm the default chart of accounts exists (inserted by the migration).

3. Train users: **post only balanced journals**; use **trial balance** to verify period totals.

4. Plan follow-up work: **automatic journal posting** from invoices, payments, inventory movements, and expenses so operational activity flows into the same ledger without duplicate manual entry.

---

## What is intentionally not in scope (v1)

- **No automatic posting** from `orders`, `order_items`, stock, or customer payments into `journal_entries` yet.  
- **No draft edit UI** — unbalanced or wrong drafts should be deleted and recreated.  
- **Legacy P&L / Balance Sheet** controllers were **not** rewritten to read from `journal_lines`; they remain order/stock–based until you build GL-driven reports.

---

## Related files (sidebar and routes registration)

- SuperAdmin sidebar link to the hub: `resources/views/partials/admin/sidebar_superadmin.blade.php` (Account Module under Receivables).  
- Route imports and accounting group: `routes/web.php`.

---

## Revision history (documentation)

| Date       | Notes |
|------------|--------|
| 2026-05-13 | Initial write-up of Account Module + double-entry tables, UI, and routes. |

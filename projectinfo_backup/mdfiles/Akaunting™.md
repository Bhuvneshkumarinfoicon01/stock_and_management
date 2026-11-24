Akaunting™
Release Downloads Translations Tests

Online accounting software designed for small businesses and freelancers. Akaunting is built with modern technologies such as Laravel, VueJS, Tailwind, RESTful API etc. Thanks to its modular structure, Akaunting provides an awesome App Store for users and developers.

Home - The house of Akaunting
Forum - Ask for support
Documentation - Learn how to use
Developer Portal - Generate passive income
App Store - Extend your Akaunting
Translations - Help us translate Akaunting
Requirements
PHP 8.1 or higher
Database (e.g.: MariaDB, MySQL, PostgreSQL, SQLite)
Web Server (eg: Apache, Nginx, IIS)
Other libraries
Framework
Akaunting uses Laravel, the best existing PHP framework, as the foundation framework and Module package for Apps.

Installation
Before installing Akaunting, make sure your environment has the required dependencies installed:

PHP 8.1 or higher with the required PHP extensions
Composer
Node.js and npm
Git
A supported database server, such as MariaDB, MySQL, PostgreSQL, or SQLite
A web server, such as Apache, Nginx, or IIS
Build tools required by some npm packages, such as build-essential on Debian/Ubuntu systems
For the full list of PHP extensions and server requirements, see the on-premise requirements.

Then install Akaunting:

Clone the repository: git clone https://github.com/akaunting/akaunting.git
Install dependencies: composer install ; npm install ; npm run dev
Install Akaunting:
php artisan install --db-name="akaunting" --db-username="root" --db-password="pass" --admin-email="admin@company.com" --admin-password="123456"
Create sample data (optional): php artisan sample-data:seed
Contributing
Please, be very clear on your commit messages and Pull Requests, empty Pull Request messages may be rejected without reason.

When contributing code to Akaunting, you must follow the PSR coding standards. The golden rule is: Imitate the existing Akaunting code.

Please note that this project is released with a Contributor Code of Conduct. By participating in this project you agree to abide by its terms.

Translation
If you'd like to contribute translations, please check out our Crowdin project.

Changelog
Please see Releases for more information about what has changed recently.

Security
Please review our security policy on how to report security vulnerabilities.

================================================================================================================================================
```sql
CREATE TABLE account_groups (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    type ENUM('asset','liability','equity','income','expense') NOT NULL,
    parent_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    CONSTRAINT fk_account_groups_parent
        FOREIGN KEY (parent_id)
        REFERENCES account_groups(id)
        ON DELETE SET NULL
);



CREATE TABLE accounts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    account_group_id BIGINT UNSIGNED NOT NULL,

    code VARCHAR(50) NOT NULL UNIQUE,

    name VARCHAR(255) NOT NULL,

    type ENUM('asset','liability','equity','income','expense') NOT NULL,

    opening_balance DECIMAL(18,2) DEFAULT 0,

    is_system TINYINT(1) DEFAULT 0,

    is_active TINYINT(1) DEFAULT 1,

    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    CONSTRAINT fk_accounts_group
        FOREIGN KEY (account_group_id)
        REFERENCES account_groups(id)
);



CREATE TABLE fiscal_years (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    name VARCHAR(100) NOT NULL,

    start_date DATE NOT NULL,

    end_date DATE NOT NULL,

    is_closed TINYINT(1) DEFAULT 0,

    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
);



CREATE TABLE journal_entries (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    fiscal_year_id BIGINT UNSIGNED NOT NULL,

    voucher_no VARCHAR(100) NOT NULL UNIQUE,

    entry_date DATE NOT NULL,

    reference VARCHAR(255) NULL,

    source_type VARCHAR(100) NULL,

    source_id BIGINT UNSIGNED NULL,

    description TEXT NULL,

    total_debit DECIMAL(18,2) DEFAULT 0,

    total_credit DECIMAL(18,2) DEFAULT 0,

    created_by BIGINT UNSIGNED NULL,

    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    INDEX idx_source (source_type, source_id),

    CONSTRAINT fk_journal_fiscal
        FOREIGN KEY (fiscal_year_id)
        REFERENCES fiscal_years(id)
);



CREATE TABLE journal_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    journal_entry_id BIGINT UNSIGNED NOT NULL,

    account_id BIGINT UNSIGNED NOT NULL,

    description TEXT NULL,

    debit DECIMAL(18,2) DEFAULT 0,

    credit DECIMAL(18,2) DEFAULT 0,

    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    INDEX idx_account_id (account_id),

    INDEX idx_journal_entry_id (journal_entry_id),

    CONSTRAINT fk_journal_items_entry
        FOREIGN KEY (journal_entry_id)
        REFERENCES journal_entries(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_journal_items_account
        FOREIGN KEY (account_id)
        REFERENCES accounts(id)
);



CREATE TABLE customers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    name VARCHAR(255) NOT NULL,

    phone VARCHAR(50) NULL,

    email VARCHAR(255) NULL,

    gstin VARCHAR(100) NULL,

    address TEXT NULL,

    account_id BIGINT UNSIGNED NULL,

    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    CONSTRAINT fk_customers_account
        FOREIGN KEY (account_id)
        REFERENCES accounts(id)
        ON DELETE SET NULL
);



CREATE TABLE suppliers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    name VARCHAR(255) NOT NULL,

    phone VARCHAR(50) NULL,

    email VARCHAR(255) NULL,

    gstin VARCHAR(100) NULL,

    address TEXT NULL,

    account_id BIGINT UNSIGNED NULL,

    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    CONSTRAINT fk_suppliers_account
        FOREIGN KEY (account_id)
        REFERENCES accounts(id)
        ON DELETE SET NULL
);



CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    sku VARCHAR(100) NOT NULL UNIQUE,

    name VARCHAR(255) NOT NULL,

    cost_price DECIMAL(18,2) NOT NULL,

    sale_price DECIMAL(18,2) NOT NULL,

    tax_percent DECIMAL(5,2) DEFAULT 0,

    stock_alert INT DEFAULT 0,

    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
);



CREATE TABLE sales (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    customer_id BIGINT UNSIGNED NULL,

    invoice_no VARCHAR(100) NOT NULL UNIQUE,

    sale_date DATETIME NOT NULL,

    subtotal DECIMAL(18,2) DEFAULT 0,

    tax_amount DECIMAL(18,2) DEFAULT 0,

    discount_amount DECIMAL(18,2) DEFAULT 0,

    grand_total DECIMAL(18,2) DEFAULT 0,

    payment_status ENUM('paid','partial','unpaid') DEFAULT 'unpaid',

    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    CONSTRAINT fk_sales_customer
        FOREIGN KEY (customer_id)
        REFERENCES customers(id)
        ON DELETE SET NULL
);



CREATE TABLE sale_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    sale_id BIGINT UNSIGNED NOT NULL,

    product_id BIGINT UNSIGNED NOT NULL,

    quantity DECIMAL(18,2) NOT NULL,

    unit_price DECIMAL(18,2) NOT NULL,

    tax_percent DECIMAL(5,2) DEFAULT 0,

    tax_amount DECIMAL(18,2) DEFAULT 0,

    discount_amount DECIMAL(18,2) DEFAULT 0,

    total DECIMAL(18,2) NOT NULL,

    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    CONSTRAINT fk_sale_items_sale
        FOREIGN KEY (sale_id)
        REFERENCES sales(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_sale_items_product
        FOREIGN KEY (product_id)
        REFERENCES products(id)
);



CREATE TABLE purchases (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    supplier_id BIGINT UNSIGNED NOT NULL,

    invoice_no VARCHAR(100) NOT NULL,

    purchase_date DATE NOT NULL,

    subtotal DECIMAL(18,2) DEFAULT 0,

    tax_amount DECIMAL(18,2) DEFAULT 0,

    grand_total DECIMAL(18,2) DEFAULT 0,

    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    CONSTRAINT fk_purchases_supplier
        FOREIGN KEY (supplier_id)
        REFERENCES suppliers(id)
);



CREATE TABLE purchase_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    purchase_id BIGINT UNSIGNED NOT NULL,

    product_id BIGINT UNSIGNED NOT NULL,

    quantity DECIMAL(18,2) NOT NULL,

    unit_cost DECIMAL(18,2) NOT NULL,

    tax_amount DECIMAL(18,2) DEFAULT 0,

    total DECIMAL(18,2) NOT NULL,

    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    CONSTRAINT fk_purchase_items_purchase
        FOREIGN KEY (purchase_id)
        REFERENCES purchases(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_purchase_items_product
        FOREIGN KEY (product_id)
        REFERENCES products(id)
);



CREATE TABLE payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    payable_type VARCHAR(100) NOT NULL,

    payable_id BIGINT UNSIGNED NOT NULL,

    account_id BIGINT UNSIGNED NOT NULL,

    amount DECIMAL(18,2) NOT NULL,

    payment_date DATETIME NOT NULL,

    reference VARCHAR(255) NULL,

    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    INDEX idx_payable (payable_type, payable_id),

    CONSTRAINT fk_payments_account
        FOREIGN KEY (account_id)
        REFERENCES accounts(id)
);



CREATE TABLE inventory_movements (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    product_id BIGINT UNSIGNED NOT NULL,

    type ENUM('purchase','sale','adjustment','return') NOT NULL,

    quantity DECIMAL(18,2) NOT NULL,

    unit_cost DECIMAL(18,2) NOT NULL,

    reference_type VARCHAR(100) NULL,

    reference_id BIGINT UNSIGNED NULL,

    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    INDEX idx_reference (reference_type, reference_id),

    CONSTRAINT fk_inventory_product
        FOREIGN KEY (product_id)
        REFERENCES products(id)
);



CREATE TABLE taxes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    name VARCHAR(100) NOT NULL,

    rate DECIMAL(5,2) NOT NULL,

    type ENUM('cgst','sgst','igst','vat') NOT NULL,

    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
);



CREATE TABLE payment_methods (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    name VARCHAR(100) NOT NULL,

    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
);
```

================================================================================================================================================
Your client is correct.

What you currently have is:

* POS reporting
* inventory tracking
* sales summaries

But NOT a true accounting system yet.

To become a real accounting/ERP module, you must implement:

* Double-entry bookkeeping
* Journal system
* General ledger
* Chart of accounts
* Debit/Credit posting engine
* Trial balance
* Proper P&L
* Proper Balance Sheet
* Asset/Liability tracking
* Expense accounting
* Inventory accounting
* Accrual accounting support

Right now your “Profit & Loss” is probably computed directly from sales and expenses tables, which is not accounting-grade.

You need accounting-driven financial statements generated from journal entries.

---

# What You Need To Build

# CORE ACCOUNTING MODULE

## 1. Chart of Accounts

Foundation of accounting.

Example:

| Code | Account             |
| ---- | ------------------- |
| 1000 | Cash                |
| 1100 | Bank                |
| 1200 | Accounts Receivable |
| 1300 | Inventory           |
| 2000 | Accounts Payable    |
| 3000 | Capital             |
| 4000 | Sales Revenue       |
| 5000 | COGS                |
| 6000 | Rent Expense        |

---

## 2. Journal Entries

Every business event creates accounting entries.

Example sale:

| Account       | Debit | Credit |
| ------------- | ----- | ------ |
| Cash          | 1000  |        |
| Sales Revenue |       | 1000   |

---

## 3. Double Entry Engine

Critical rule:

\text{Total Debits} = \text{Total Credits}

Every transaction MUST balance.

---

# REQUIRED DATABASE TABLES

You need these accounting tables:

```text id="6dyqyv"
accounts
account_groups
journal_entries
journal_items
general_ledger
fiscal_years
```

And integrate with:

```text id="dxqjgc"
sales
purchases
expenses
payments
inventory_movements
customers
suppliers
```

---

# REQUIRED ACCOUNTING FLOWS

# 1. SALES POSTING

When POS sale happens:

## Entry 1 — Revenue

| Account       | Debit | Credit |
| ------------- | ----- | ------ |
| Cash/Bank/AR  | 1180  |        |
| Sales Revenue |       | 1000   |
| GST Payable   |       | 180    |

---

## Entry 2 — Inventory Reduction

If item cost = 700:

| Account            | Debit | Credit |
| ------------------ | ----- | ------ |
| Cost of Goods Sold | 700   |        |
| Inventory          |       | 700    |

This is currently missing in most POS systems.

Without this:

* inventory valuation wrong
* balance sheet wrong
* profit wrong

---

# 2. PURCHASE POSTING

Supplier bill:

| Account          | Debit | Credit |
| ---------------- | ----- | ------ |
| Inventory        | 5000  |        |
| GST Input        | 900   |        |
| Accounts Payable |       | 5900   |

---

# 3. EXPENSE POSTING

Electricity bill:

| Account             | Debit | Credit |
| ------------------- | ----- | ------ |
| Electricity Expense | 2000  |        |
| Cash/Bank           |       | 2000   |

---

# 4. CUSTOMER CREDIT SALES

| Account             | Debit | Credit |
| ------------------- | ----- | ------ |
| Accounts Receivable | 1000  |        |
| Sales Revenue       |       | 1000   |

Payment later:

| Account             | Debit | Credit |
| ------------------- | ----- | ------ |
| Cash                | 1000  |        |
| Accounts Receivable |       | 1000   |

---

# 5. SUPPLIER PAYMENTS

| Account          | Debit | Credit |
| ---------------- | ----- | ------ |
| Accounts Payable | 5000  |        |
| Bank             |       | 5000   |

---

# WHAT CLIENT EXPECTS

Client expects proper:

## General Ledger

Per-account transaction history.

Example Cash Ledger:

| Date    | Debit | Credit | Balance |
| ------- | ----- | ------ | ------- |
| Sale    | 1000  |        | 1000    |
| Expense |       | 200    | 800     |

---

## Trial Balance

Generated from journal_items.

Must balance:

\sum \text{Debit} = \sum \text{Credit}

---

## Proper Profit & Loss

Generated from:

* income accounts
* expense accounts

NOT from sales table directly.

---

## Proper Balance Sheet

Generated from:

* assets
* liabilities
* equity

Equation:

\text{Assets} = \text{Liabilities} + \text{Equity}

---

# ARCHITECTURE YOU SHOULD IMPLEMENT

```text id="rbj12v"
POS Transaction
      ↓
Accounting Posting Service
      ↓
Journal Entries
      ↓
General Ledger
      ↓
Financial Statements
```

---

# IMPORTANT IMPLEMENTATION RULES

# DO NOT

```text id="2nl8vz"
sales_total - expense_total
```

for profit.

That is NOT accounting.

---

# DO

Generate everything from journals:

```sql id="gzx8zz"
SUM(debit) - SUM(credit)
```

per account.

---

# REQUIRED SERVICES

Create:

```text id="bqor0m"
app/Services/Accounting/
```

## Files

```text id="p8a72t"
PostingService.php
JournalService.php
LedgerService.php
TrialBalanceService.php
ProfitLossService.php
BalanceSheetService.php
InventoryValuationService.php
```

---

# REQUIRED MODELS

```text id="uoky9k"
Account
JournalEntry
JournalItem
Ledger
FiscalYear
```

---

# REQUIRED REPORTS

Client likely expects:

* Trial Balance
* General Ledger
* Cash Book
* Bank Book
* Journal Register
* Balance Sheet
* Profit & Loss
* Accounts Receivable Aging
* Accounts Payable Aging
* GST Reports
* Inventory Valuation

---

# INVENTORY ACCOUNTING

Critical missing part.

Inventory must be an ASSET account.

When selling inventory:

| Debit | Credit    |
| ----- | --------- |
| COGS  | Inventory |

Without this:

* balance sheet invalid
* profits overstated
* inventory inaccurate

---

# WHAT YOU SHOULD TELL CLIENT

You are upgrading from:

* operational POS reporting

to:

* full double-entry accounting ERP architecture

with:

* accounting-compliant journals
* ledger-based reporting
* auditable transactions
* proper financial statements

---

# BEST NEXT STEP

Implement in this order:

## Phase 1

* Chart of Accounts
* Journal Entries
* Posting Engine

## Phase 2

* Ledger
* Trial Balance
* P&L
* Balance Sheet

## Phase 3

* AR/AP
* GST
* Inventory valuation
* Fiscal periods
* Closing entries

---

I can also help you create:

* complete MySQL accounting schema
* Laravel accounting models
* automatic posting engine
* journal posting traits
* ledger SQL queries
* accounting APIs
* trial balance generator
* P&L generator
* balance sheet engine
* GST accounting
* inventory valuation FIFO/AVCO
* accounting seeder
* full ERP accounting architecture in Laravel.

================================================================================================================================================
Here is what was added to support real double-entry bookkeeping (separate from the old order/stock–based “P&L” and “balance sheet” screens).

What you get
Chart of accounts (gl_accounts)
Types: asset, liability, equity, revenue, expense. Optional parent, active flag, CRUD from the UI.

Journal entries (journal_entries + journal_lines)

Save as draft (balanced debits/credits required).
Post when ready; only posted lines hit the GL.
Delete allowed only for drafts.
Each line is either debit or credit (not both); totals must match.
General ledger
Pick an account and date range: opening balance before the range, then each posted line with a running balance (debit-normal vs credit-normal).

Trial balance
For the period, sums of posted debits and credits per account; grand totals must agree if the books balance.

Account module page

Primary card: General ledger accounting (links to all of the above).
Clear note that legacy P&L / Balance Sheet / Financial Oversight are not GL-based and have limited accounting meaning until you drive reporting from this ledger.
Files (high level)
Area	Path
Migration + seed COA
database/migrations/2026_05_13_120000_create_double_entry_accounting_tables.php
Models
app/Models/GlAccount.php, JournalEntry.php, JournalLine.php
Controllers
GlAccountController, JournalEntryController, GeneralLedgerController
Views
resources/views/admin/accounting/*.blade.php
Routes
admin/accounting/* under routes/web.php
Hub
resources/views/admin/account-module.blade.php
Routes (prefix admin/accounting/)
chart-of-accounts, journals, journals/create, journals/{id}, post & delete
general-ledger, trial-balance
Names: admin.accounting.*

What you need to run
php artisan migrate
(This failed here only because MySQL was not available in the sandbox.)

Default chart of accounts (seeded in migration)
Example codes: 1000 Cash, 1100 A/R, 1200 Inventory, 2000 A/P, 3000 Equity, 4000 Sales, 5000 COGS, 5100 Operating expenses, etc.

Not done yet (explicit)
Automatic journals from invoices, inventory, COGS, expenses, and other assets/liabilities are not wired in yet. Those should call the same posting rules (balanced lines, correct accounts) so the trial balance and future real P&L/Balance Sheet come from journal_lines.
Draft editing in the UI is not implemented (only delete draft and create again).
If you want a next step, the most valuable one is posting templates for: cash sale, credit sale (A/R + revenue + tax + inventory/COGS), receipts on account, and simple expense journals—implemented as a service that creates balanced JournalEntry rows from your existing orders / order_items / payments data.


================================================================================================================================================

For linking google calendar we need below details with json file downloaded from account 

GOOGLE_CLIENT_ID=xxxxx
GOOGLE_CLIENT_SECRET=xxxxx
GOOGLE_REDIRECT_URI=http://app.purrfectandpooch.com/google/callback

for above get you have to login with google console account

Step 1 — Create Google Cloud Project

Go to:

Google Cloud Console

Enable APIs

Enable:

Google Calendar API

Step 2 — Create OAuth Credentials

Inside APIs & Services → Credentials:

Create:

OAuth Client ID

Add redirect URI:

http://app.purrfectandpooch.com/google/callback

Production example:

http://app.purrfectandpooch.com/google/callback

Copy:

CLIENT_ID
CLIENT_SECRET

================================================================================================================================================
Here is what was implemented.

Sidebar (sidebar_superadmin.blade.php)
The old empty Supplier / Purchases blocks are commented and preserved.
Suppliers submenu (icon truck): Add Supplier, Supplier List, Supplier Ledger, Supplier Payments, Supplier Due Report — with subdrop active when any of those routes match.
Purchases submenu (icon shopping-bag): Purchase Order, Add Purchase (goes straight to create), Purchase Return, GRN, Purchase Invoice — with matching active state.
Add Purchase uses admin.store-purchases.create. Lists are still available from each section’s “Back” link (e.g. store purchases list).
Migration (database/migrations/2026_05_14_100000_create_suppliers_and_purchases_tables.php)
Creates, in order: suppliers, supplier_purchase_invoices + supplier_purchase_invoice_items, supplier_payments, purchase_orders + purchase_order_items, store_purchases + store_purchase_items, purchase_returns + purchase_return_items, goods_receipt_notes + goods_receipt_note_items.

Models
Supplier, SupplierPayment, SupplierPurchaseInvoice, SupplierPurchaseInvoiceItem, PurchaseOrder, PurchaseOrderItem, StorePurchase, StorePurchaseItem, PurchaseReturn, PurchaseReturnItem, GoodsReceiptNote, GoodsReceiptNoteItem (with relations to Store where applicable).

Controllers
SupplierController — list / add / edit suppliers; auto SUP-xxxxx code when code is left blank.
SupplierLedgerController — merged view of purchase invoices (debit) and payments (credit) with running balance.
SupplierPaymentController — list + record payment; optional allocation to an invoice (updates paid_amount / due_amount / status); validates invoice belongs to supplier.
SupplierDueReportController — suppliers with sum of open due_amount on purchase invoices.
PurchaseDocumentController (+ NormalizesPurchaseLines trait) — PO, store purchase, purchase return, GRN, supplier purchase invoice (tax + line totals, unique document numbers).
Views
Under resources/views/admin/suppliers/ and admin/purchases/ (including partials/line-items.blade.php for shared lines; GRN uses qty_received / unit_cost).

Routes (prefix admin.)
suppliers resource (index, create, store, edit, update), supplier-ledger, supplier-payments (index/create/store), supplier-due-report, and the purchase routes listed in routes/web.php (after account-module).

Apply DB changes
php artisan migrate
Stock is not increased automatically from GRN/purchases yet; that can be wired next to Stock / IncomingStock if you want.
================================================================================================================================================
================================================================================================================================================
================================================================================================================================================
================================================================================================================================================
================================================================================================================================================
================================================================================================================================================
================================================================================================================================================
================================================================================================================================================
================================================================================================================================================
================================================================================================================================================
================================================================================================================================================
================================================================================================================================================
================================================================================================================================================
================================================================================================================================================
================================================================================================================================================
================================================================================================================================================
================================================================================================================================================
================================================================================================================================================
================================================================================================================================================
================================================================================================================================================
================================================================================================================================================
================================================================================================================================================
================================================================================================================================================
================================================================================================================================================
================================================================================================================================================
================================================================================================================================================
================================================================================================================================================
================================================================================================================================================
================================================================================================================================================
================================================================================================================================================
================================================================================================================================================
================================================================================================================================================
================================================================================================================================================
================================================================================================================================================
================================================================================================================================================
================================================================================================================================================
================================================================================================================================================

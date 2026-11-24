======================================================================================================================================
# Professional ERP Style Database Structure

For a Laravel-based:

* POS
* Inventory
* Accounting

This structure is inspired by real ERP systems like:

* Odoo
* ERPNext
* SAP ERP

The goal is:

* Proper accounting
* Inventory valuation
* Auto journal posting
* Scalable architecture
* ERP-style normalization

---

# 1. chart_of_accounts

Master accounting table.

```sql id="ewd78o"
CREATE TABLE chart_of_accounts (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    account_code VARCHAR(20) UNIQUE,
    account_name VARCHAR(255),
    account_type ENUM(
        'asset',
        'liability',
        'equity',
        'income',
        'expense'
    ),
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

---

# Dummy Data

```sql id="jlwm2l"
INSERT INTO chart_of_accounts
(account_code, account_name, account_type)
VALUES
('1000', 'Cash', 'asset'),
('1100', 'Accounts Receivable', 'asset'),
('1200', 'Inventory Asset', 'asset'),
('4000', 'Sales Revenue', 'income'),
('5000', 'Cost of Goods Sold', 'expense'),
('6000', 'Purchase Expense', 'expense');
```

---

# 2. product_categories

```sql id="5fck7t"
CREATE TABLE product_categories (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

---

# Dummy Data

```sql id="3symsi"
INSERT INTO product_categories (name)
VALUES
('Beverages'),
('Electronics'),
('Services');
```

---

# 3. products

Professional ERP-style product table.

```sql id="qfxl9d"
CREATE TABLE products (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,

    sku VARCHAR(100) UNIQUE,
    name VARCHAR(255),

    category_id BIGINT,

    product_type ENUM(
        'service',
        'inventory',
        'non_inventory'
    ),

    selling_price DECIMAL(15,2),
    cost_price DECIMAL(15,2),

    stock_qty DECIMAL(15,2) DEFAULT 0,

    income_account_id BIGINT,
    expense_account_id BIGINT,
    inventory_account_id BIGINT,

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (category_id)
        REFERENCES product_categories(id),

    FOREIGN KEY (income_account_id)
        REFERENCES chart_of_accounts(id),

    FOREIGN KEY (expense_account_id)
        REFERENCES chart_of_accounts(id),

    FOREIGN KEY (inventory_account_id)
        REFERENCES chart_of_accounts(id)
);
```

---

# Dummy Products

```sql id="d9u1d0"
INSERT INTO products
(
sku,
name,
category_id,
product_type,
selling_price,
cost_price,
stock_qty,
income_account_id,
expense_account_id,
inventory_account_id
)
VALUES

(
'COKE001',
'Coca Cola',
1,
'inventory',
100,
60,
200,
4,
5,
3
),

(
'SERV001',
'Laptop Repair Service',
3,
'service',
500,
0,
0,
4,
NULL,
NULL
),

(
'TAPE001',
'Packaging Tape',
2,
'non_inventory',
50,
20,
0,
4,
NULL,
NULL
);
```

---

# 4. customers

```sql id="y1kk3k"
CREATE TABLE customers (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,

    name VARCHAR(255),
    phone VARCHAR(50),
    email VARCHAR(255),
    address TEXT,

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

---

# Dummy Customer

```sql id="8h5g1o"
INSERT INTO customers
(name, phone)
VALUES
('John Doe', '9876543210');
```

---

# 5. sales

POS invoice header.

```sql id="zfxccu"
CREATE TABLE sales (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,

    invoice_no VARCHAR(100) UNIQUE,

    customer_id BIGINT NULL,

    sale_date DATETIME,

    subtotal DECIMAL(15,2),
    tax_amount DECIMAL(15,2),
    discount_amount DECIMAL(15,2),
    total_amount DECIMAL(15,2),

    payment_method ENUM(
        'cash',
        'card',
        'bank',
        'credit'
    ),

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (customer_id)
        REFERENCES customers(id)
);
```

---

# 6. sale_items

Invoice items.

```sql id="m1ejv0"
CREATE TABLE sale_items (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,

    sale_id BIGINT,
    product_id BIGINT,

    qty DECIMAL(15,2),
    price DECIMAL(15,2),
    cost DECIMAL(15,2),

    line_total DECIMAL(15,2),

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (sale_id)
        REFERENCES sales(id),

    FOREIGN KEY (product_id)
        REFERENCES products(id)
);
```

---

# Dummy Sale

```sql id="0n9u79"
INSERT INTO sales
(
invoice_no,
customer_id,
sale_date,
subtotal,
tax_amount,
discount_amount,
total_amount,
payment_method
)
VALUES
(
'INV-1001',
1,
NOW(),
100,
0,
0,
100,
'cash'
);
```

---

# Dummy Sale Item

```sql id="12br64"
INSERT INTO sale_items
(
sale_id,
product_id,
qty,
price,
cost,
line_total
)
VALUES
(
1,
1,
1,
100,
60,
100
);
```

---

# 7. journals

Accounting journal header.

```sql id="1r1w2l"
CREATE TABLE journals (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,

    journal_date DATETIME,

    reference_type VARCHAR(50),
    reference_id BIGINT,

    description TEXT,

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

---

# 8. journal_entries

Accounting debit/credit lines.

```sql id="1wsm4m"
CREATE TABLE journal_entries (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,

    journal_id BIGINT,
    account_id BIGINT,

    debit DECIMAL(15,2) DEFAULT 0,
    credit DECIMAL(15,2) DEFAULT 0,

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (journal_id)
        REFERENCES journals(id),

    FOREIGN KEY (account_id)
        REFERENCES chart_of_accounts(id)
);
```

---

# Dummy Journal

Sale of Coca Cola ₹100

---

## Revenue Entry

```sql id="2m4ndx"
INSERT INTO journals
(
journal_date,
reference_type,
reference_id,
description
)
VALUES
(
NOW(),
'sale',
1,
'Sale Invoice INV-1001'
);
```

---

# Journal Lines

```sql id="j0m0v6"
INSERT INTO journal_entries
(journal_id, account_id, debit, credit)
VALUES

(1, 1, 100, 0),

(1, 4, 0, 100),

(1, 5, 60, 0),

(1, 3, 0, 60);
```

---

# Resulting Accounting

---

## Revenue Side

| Account       | DR  | CR  |
| ------------- | --- | --- |
| Cash          | 100 |     |
| Sales Revenue |     | 100 |

---

## Inventory Side

| Account         | DR | CR |
| --------------- | -- | -- |
| COGS            | 60 |    |
| Inventory Asset |    | 60 |

---

# 9. stock_movements

Professional inventory ledger.

```sql id="aij1a8"
CREATE TABLE stock_movements (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,

    product_id BIGINT,

    movement_type ENUM(
        'purchase',
        'sale',
        'adjustment',
        'return'
    ),

    reference_id BIGINT,

    qty DECIMAL(15,2),

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (product_id)
        REFERENCES products(id)
);
```

---

# Dummy Stock Movement

```sql id="q6q6wy"
INSERT INTO stock_movements
(
product_id,
movement_type,
reference_id,
qty
)
VALUES
(
1,
'sale',
1,
-1
);
```

---

# ERP Workflow

---

# Product Setup

Each product defines:

* Revenue account
* Expense account
* Inventory account

---

# During Sale

System automatically:

## 1. Creates Invoice

## 2. Creates Journal

## 3. Creates Journal Entries

## 4. Deducts Inventory

## 5. Creates Stock Movement

---

# Recommended Future Tables

Later you can add:

| Module        | Tables                    |
| ------------- | ------------------------- |
| Purchasing    | purchases, purchase_items |
| Warehouses    | warehouses                |
| Multi Stock   | warehouse_stock           |
| Taxes         | taxes                     |
| Returns       | sale_returns              |
| Payments      | payments                  |
| Expenses      | expenses                  |
| Manufacturing | BOM tables                |

---

# Important ERP Architecture Rule

Professional ERPs NEVER:

* Store balance in accounts directly
* Store profit directly
* Store inventory value manually

Everything is calculated from:

* Journal entries
* Stock movements

This gives:

* Audit trail
* Financial accuracy
* Reversibility
* Proper reporting

---

# Recommended Laravel Models

```text id="1cwbr8"
Product
Category
ChartOfAccount
Sale
SaleItem
Journal
JournalEntry
StockMovement
Customer
```

---

# Recommended Service Layer

Create services like:

```text id="t11o0i"
SaleService
InventoryService
AccountingService
JournalService
```

This keeps ERP logic clean and scalable.

======================================================================================================================================
If you are using [nWidart Laravel Modules](https://nwidart.com/laravel-modules/v6/introduction?utm_source=chatgpt.com), it provides separate artisan commands for modules.

These commands automatically create files inside the correct module namespace and folders.

---

# Create Module

```bash id="n0on4d"
php artisan module:make User
```

Creates:

```text id="x6d4bs"
Modules/User
```

---

# Create Controller

```bash id="lqmp75"
php artisan module:make-controller UserController User
```

Creates:

```text id="s0e5v9"
Modules/User/app/Http/Controllers/UserController.php
```

---

# Create Model

```bash id="87d7z5"
php artisan module:make-model User User
```

Creates:

```text id="b0fx6z"
Modules/User/app/Models/User.php
```

Namespace:

```php id="1h4bq2"
namespace Modules\User\app\Models;
```

---

# Create Migration

```bash id="pnk4g7"
php artisan module:make-migration create_users_table User
```

Creates:

```text id="ij8tt9"
Modules/User/database/migrations/
```

---

# Create Seeder

```bash id="y2lqby"
php artisan module:make-seed UserSeeder User
```

---

# Create Factory

```bash id="j95a7f"
php artisan module:make-factory UserFactory User
```

---

# Create Middleware

```bash id="0m6r3v"
php artisan module:make-middleware CheckUser User
```

---

# Create Request Validation

```bash id="0rb25o"
php artisan module:make-request StoreUserRequest User
```

---

# Create Event

```bash id="2yyc63"
php artisan module:make-event UserCreated User
```

---

# Create Listener

```bash id="yw7nwu"
php artisan module:make-listener SendWelcomeEmail User
```

---

# Create Job

```bash id="22okuk"
php artisan module:make-job ProcessUserJob User
```

---

# Create Policy

```bash id="stn67e"
php artisan module:make-policy UserPolicy User
```

---

# Create Notification

```bash id="gvknk6"
php artisan module:make-notification UserNotification User
```

---

# Create Mail Class

```bash id="t0g41r"
php artisan module:make-mail WelcomeMail User
```

---

# Create Command

```bash id="6fv9sm"
php artisan module:make-command SyncUsers User
```

---

# Create Service Provider

```bash id="pv1v1u"
php artisan module:make-provider UserServiceProvider User
```

---

# ERP Recommended Module Structure

```text id="x8rj39"
Modules
├── User
│   ├── app
│   │   ├── Http
│   │   ├── Models
│   │   ├── Services
│   │   ├── Repositories
│   │   └── Providers
│   │
│   ├── database
│   │   ├── migrations
│   │   ├── seeders
│   │   └── factories
│   │
│   ├── resources
│   │   ├── views
│   │   └── js
│   │
│   └── routes
```

---

# Recommended ERP Architecture

Inside modules, separate business logic:

```text id="w6b5v1"
Controller → Service → Repository → Model
```

Example:

```text id="65nkot"
SaleController
    ↓
SaleService
    ↓
SaleRepository
    ↓
Sale Model
```

This keeps large ERP systems maintainable.

---

# Useful Module Commands

List all commands:

```bash id="7q5f0z"
php artisan list module
```

---

# Generate Complete CRUD Quickly

Example:

```bash id="mhrk7x"
php artisan module:make-model Product Inventory -mfscr
```

Meaning:

| Flag | Creates             |
| ---- | ------------------- |
| `-m` | migration           |
| `-f` | factory             |
| `-s` | seeder              |
| `-c` | controller          |
| `-r` | resource controller |

This single command creates almost full CRUD structure.

======================================================================================================================================
======================================================================================================================================
======================================================================================================================================
======================================================================================================================================
======================================================================================================================================
======================================================================================================================================
======================================================================================================================================
======================================================================================================================================
======================================================================================================================================
======================================================================================================================================
======================================================================================================================================
======================================================================================================================================
======================================================================================================================================
======================================================================================================================================
======================================================================================================================================
======================================================================================================================================
======================================================================================================================================
======================================================================================================================================
======================================================================================================================================
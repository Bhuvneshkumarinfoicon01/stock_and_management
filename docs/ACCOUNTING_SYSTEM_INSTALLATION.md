# Accounting System — Server Installation Guide

**Point 18** — Instructions for installing the **Accounting System** (general ledger, chart of accounts, journals, automatic transaction postings) on any server.

The Accounting System is a module inside the **CSL POS & Inventory Management System** (`stock_mgnt`). It is **not** a separate application. You must install and run the full Laravel application; the accounting layer depends on the same database, users, stores, products, sales, and purchases data.

For functional details of the module itself, see [ACCOUNTING_MODULE.md](./ACCOUNTING_MODULE.md).

---

## 1. Overview

The Accounting System provides:

- **Chart of accounts** (`gl_accounts`)
- **Journal entries** and **journal lines** (double-entry bookkeeping)
- **General ledger** and **trial balance** reports
- **Automatic GL posting** from operational transactions (sales invoices, purchase invoices, payments, etc.) via `AccountingService`
- **Transaction account mappings** — configurable debit/credit GL accounts per transaction type

**Key application paths**

| Item | Location |
|------|----------|
| Accounting config | `config/accounting.php` |
| Posting service | `app/Services/AccountingService.php` |
| Transaction mappings | `app/Services/TransactionAccountMappingService.php` |
| GL migration | `database/migrations/2026_05_13_120000_create_double_entry_accounting_tables.php` |
| Mappings migration | `database/migrations/2026_06_05_171000_create_transaction_account_mappings_table.php` |
| Admin UI hub | `/admin/account-module` |

---

## 2. Hardware specifications

Requirements depend on store count, transaction volume, and concurrent users. The figures below cover the **combined** POS + inventory + accounting workload.

### 2.1 Minimum (single store, up to ~5 users, &lt; 500 transactions/day)

| Component | Specification |
|-----------|---------------|
| CPU | 2 cores (x86_64 or ARM64) |
| RAM | 4 GB |
| Storage | 40 GB SSD (OS + application + database + logs) |
| Network | 10 Mbps stable uplink |

### 2.2 Recommended (1–10 stores, up to ~25 users, moderate volume)

| Component | Specification |
|-----------|---------------|
| CPU | 4 cores |
| RAM | 8 GB |
| Storage | 100 GB SSD |
| Network | 50 Mbps+ with low latency to users |

### 2.3 Production / multi-store (10+ stores, high transaction volume)

| Component | Specification |
|-----------|---------------|
| CPU | 8+ cores |
| RAM | 16 GB or more |
| Storage | 250 GB+ SSD (separate volume for MySQL data recommended) |
| Database | Dedicated database server or managed MySQL instance |
| Network | 100 Mbps+, redundant connectivity |
| Backups | Off-server backup storage (minimum 2× database size) |

### 2.4 Notes

- **SSD storage is strongly recommended** — journal and ledger queries are I/O sensitive as data grows.
- **Do not run production on SQLite** — use MySQL or MariaDB (see Section 4).
- If you also host the optional **Node.js notification server** (`server.js`) on the same machine, add **512 MB–1 GB RAM**.
- Plan disk growth: journal lines accumulate with every posted sale, purchase, and payment.

---

## 3. Operating system prerequisites

Supported **64-bit** Linux distributions (recommended):

- Ubuntu 22.04 LTS or 24.04 LTS
- Debian 12
- Rocky Linux 9 / AlmaLinux 9
- RHEL 9 (with equivalent packages)

Windows Server and macOS can be used for **development only**, not recommended for production.

### 3.1 Base OS packages

Install before any application software:

```bash
sudo apt update && sudo apt upgrade -y   # Debian/Ubuntu
sudo apt install -y curl git unzip zip ca-certificates software-properties-common
```

Ensure the server clock is synchronized (accounting dates depend on correct system time):

```bash
sudo timedatectl set-ntp true
timedatectl
```

---

## 4. Software prerequisites

Install **all** of the following before deploying the Accounting System.

### 4.1 Required

| Software | Version | Purpose |
|----------|---------|---------|
| **PHP** | **8.2 or 8.3** (8.2+ required by `composer.json`) | Application runtime |
| **Composer** | 2.x | PHP dependency management |
| **MySQL** or **MariaDB** | MySQL **8.0+** or MariaDB **10.6+** | Primary database (GL, journals, operational data) |
| **Web server** | **Nginx 1.18+** or **Apache 2.4+** with `mod_rewrite` | HTTP/HTTPS delivery |
| **Node.js** | **18 LTS or 20 LTS** | Build frontend assets (`npm run build`); optional notification server |
| **npm** | 9+ (bundled with Node) | Frontend build |

### 4.2 PHP extensions (required)

Install these PHP extensions — Laravel and the accounting module will fail without them:

| Extension | Required for |
|-----------|--------------|
| `bcmath` | Decimal money calculations (debits/credits) |
| `ctype` | Laravel framework |
| `curl` | HTTP client |
| `dom` | XML/HTML processing |
| `fileinfo` | File uploads |
| `json` | API and configuration |
| `mbstring` | Multibyte strings |
| `openssl` | HTTPS, encryption |
| `pdo` | Database abstraction |
| `pdo_mysql` | MySQL/MariaDB connection |
| `tokenizer` | Laravel |
| `xml` | Framework |

**Ubuntu/Debian example:**

```bash
sudo apt install -y php8.2-fpm php8.2-cli php8.2-mysql php8.2-mbstring \
  php8.2-xml php8.2-curl php8.2-zip php8.2-bcmath php8.2-intl php8.2-gd
```

Verify:

```bash
php -v
php -m | grep -E 'pdo_mysql|bcmath|mbstring|openssl'
```

### 4.3 Recommended (production)

| Software | Purpose |
|----------|---------|
| **Redis** 6+ | Cache and queue backend (optional; default config uses database) |
| **Supervisor** or **systemd** | Keep queue workers and Node notification process running |
| **Certbot** (Let's Encrypt) | TLS/SSL certificates |
| **fail2ban** | Brute-force protection on SSH and web |

### 4.4 Optional

| Software | Purpose |
|----------|---------|
| **PM2** | Process manager for `server.js` (real-time notifications) |
| **phpredis** extension | If using Redis for cache/queue |

---

## 5. Network and ports

| Port | Service | Notes |
|------|---------|-------|
| **80** | HTTP | Redirect to HTTPS in production |
| **443** | HTTPS | Primary user access |
| **3306** | MySQL/MariaDB | **Bind to localhost only** unless using a remote DB server |
| **3050** | Node notification server | Optional; internal or reverse-proxied via Nginx |
| **22** | SSH | Restrict by IP where possible |

---

## 6. Pre-installation checklist

Complete this checklist **before** running migrations or enabling accounting postings.

- [ ] Server meets hardware specs (Section 2)
- [ ] OS updated and time synchronized
- [ ] PHP 8.2+, required extensions, Composer, Node.js, and web server installed
- [ ] MySQL/MariaDB installed; empty database and dedicated user created
- [ ] Firewall configured (80/443 open; 3306 not public)
- [ ] Domain name DNS points to server (production)
- [ ] TLS certificate plan in place (production)
- [ ] Backup strategy defined (database + `storage/` + `.env`)
- [ ] Git or deployment package available for `stock_mgnt` application code

---

## 7. Installation steps

### Step 1 — Deploy application code

```bash
cd /var/www
sudo git clone <repository-url> stock_mgnt
sudo chown -R www-data:www-data stock_mgnt
cd stock_mgnt
```

Or upload and extract your release archive to `/var/www/html/stock_mgnt` (adjust paths to match your web root).

### Step 2 — Install PHP dependencies

```bash
composer install --no-dev --optimize-autoloader
```

For development:

```bash
composer install
```

### Step 3 — Install and build frontend assets

```bash
npm ci
npm run build
```

### Step 4 — Environment configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` for production:

```env
APP_NAME="CSL POS & Inventory"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.example

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=stock_mgnt
DB_USERNAME=stock_mgnt_user
DB_PASSWORD=<strong-password>

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

LOG_CHANNEL=daily
LOG_LEVEL=warning
```

Create the database and user:

```sql
CREATE DATABASE stock_mgnt CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'stock_mgnt_user'@'localhost' IDENTIFIED BY '<strong-password>';
GRANT ALL PRIVILEGES ON stock_mgnt.* TO 'stock_mgnt_user'@'localhost';
FLUSH PRIVILEGES;
```

**Alternative:** import the full baseline schema from `stockmgnt.sql` if provided by your vendor, then run only pending migrations.

### Step 5 — File permissions

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Step 6 — Run database migrations

Run **all** migrations so operational tables (users, stores, orders, purchases) exist before accounting tables:

```bash
php artisan migrate --force
```

If you need to run accounting migrations individually (e.g. after a partial failure):

```bash
php artisan migrate --path=database/migrations/2026_05_13_120000_create_double_entry_accounting_tables.php --force
php artisan migrate --path=database/migrations/2026_06_05_171000_create_transaction_account_mappings_table.php --force
```

The accounting migration **seeds** a default chart of accounts (codes `1000`–`5200`). The mappings migration **seeds** default debit/credit pairs for sale invoice, purchase invoice, credit note, debit note, customer payment, and supplier payment.

### Step 7 — Optimize Laravel (production)

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 8 — Configure web server

**Nginx** (example server block — adjust `root` and `server_name`):

```nginx
server {
    listen 80;
    server_name your-domain.example;
    root /var/www/stock_mgnt/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable HTTPS with Certbot after the site responds on port 80.

### Step 9 — Queue worker (recommended)

The default queue driver is `database`. Run a worker so background jobs complete:

```bash
php artisan queue:work --tries=3
```

Use **Supervisor** to keep the worker running in production.

### Step 10 — Optional notification server

Not required for accounting, but used elsewhere in the app. See `projectinfo_backup/mdfiles/HOSTING_NOTIFICATION_SERVER.md`.

```bash
pm2 start server.js --name stockmgnt-notify
```

---

## 8. Accounting-specific configuration (post-install)

Perform these steps **after** migrations succeed and you can log in as Super Admin.

### 8.1 Verify chart of accounts

1. Log in as **Super Admin**.
2. Go to **Receivables → Account Module** (`/admin/account-module`).
3. Open **Chart of accounts** (`/admin/accounting/chart-of-accounts`).
4. Confirm default accounts exist (e.g. `1000` Cash, `1100` A/R, `1200` Inventory, `2000` A/P, `4000` Sales, `5000` COGS).

If the list is empty, re-run the GL migration or insert accounts manually before enabling postings.

### 8.2 Review transaction account mappings

1. Open **Transaction account mappings** (`/admin/accounting/transaction-mappings`).
2. Confirm each transaction type has valid **Debit** and **Credit** GL accounts:

   | Transaction type | Typical debit | Typical credit |
   |------------------|---------------|----------------|
   | Sale invoice (revenue) | Accounts Receivable | Sales Revenue |
   | Purchase invoice | Inventory | Accounts Payable |
   | Customer payment | Cash | Accounts Receivable |
   | Supplier payment | Accounts Payable | Cash |

3. Adjust mappings to match your organization's chart before going live.

### 8.3 Review `config/accounting.php`

Default GL codes used by `AccountingService` when product-level accounts are not set:

```php
'accounts' => [
    'cash' => '1000',
    'accounts_receivable' => '1100',
    'inventory' => '1200',
    'accounts_payable' => '2000',
    'sales_revenue' => '4000',
    'cogs' => '5000',
],
```

Ensure these codes exist and are **active** in the chart of accounts. Change codes here only if you renamed accounts in the database.

### 8.4 Product-level GL accounts (if used)

Products can require **Debit Account** and **Credit Account** from the chart of accounts. Populate these on products before saving if your deployment enforces product-level posting rules.

### 8.5 Store document sequences

Configure purchase/sale document number sequences per store (Super Admin → Stores) so invoice and journal references align with your numbering policy.

---

## 9. Verification and smoke tests

Run these checks to confirm the Accounting System is ready:

| # | Test | Expected result |
|---|------|-----------------|
| 1 | Open Chart of accounts | At least 12 default GL accounts listed |
| 2 | Create a **balanced manual journal** (draft) → Post | Status `posted`; lines appear in General ledger |
| 3 | Run **Trial balance** for current month | Total debits = total credits (or only opening/zero activity) |
| 4 | Post a **test sale invoice** (non-estimate) | Journal entry created with `source_type` linked to order; revenue + COGS journals per configuration |
| 5 | Post a **supplier purchase invoice** | Journal entry with inventory / accounts payable mapping |
| 6 | Record **customer payment** | Cash debited, A/R credited per mapping |
| 7 | Check `storage/logs/laravel.log` | No `Accounting: unbalanced entry skipped` warnings for valid transactions |

---

## 10. Security requirements (production)

- Set `APP_DEBUG=false` and `APP_ENV=production`.
- Use **HTTPS only**; enforce redirect from HTTP.
- Restrict database access to localhost or a private network.
- Never commit `.env` to version control.
- Restrict Super Admin and accounting screens to authorized roles.
- Schedule **daily encrypted database backups**; test restore quarterly.
- Keep PHP, MySQL, OS, and Composer packages patched.

---

## 11. Backup and maintenance

### 11.1 What to back up

| Asset | Frequency |
|-------|-----------|
| MySQL database (includes `gl_accounts`, `journal_entries`, `journal_lines`, `transaction_account_mappings`) | Daily (minimum) |
| `storage/app` (uploads) | Daily |
| `.env` | On change (secure store) |
| Application code | On each release |

### 11.2 Example database backup

```bash
mysqldump -u stock_mgnt_user -p stock_mgnt | gzip > /backups/stock_mgnt_$(date +%F).sql.gz
```

### 11.3 Ongoing maintenance

```bash
php artisan migrate --force          # After each application upgrade
php artisan config:cache             # After .env or config changes
php artisan queue:restart            # After code deployment
```

Monitor disk space on journal tables; archive or report off old periods per your retention policy.

---

## 12. Troubleshooting

| Symptom | Likely cause | Action |
|---------|--------------|--------|
| Chart of accounts empty | GL migration not run | Run `2026_05_13_120000_create_double_entry_accounting_tables.php` |
| Automatic postings not created | Missing mappings or inactive GL accounts | Check Transaction mappings and `config/accounting.php` codes |
| Log: `unbalanced entry skipped` | Debit ≠ credit for computed lines | Review transaction amounts and mapping accounts |
| `500` after deploy | Permissions or cached config | Fix `storage/` permissions; run `php artisan config:clear` |
| Trial balance does not balance | Unposted drafts or manual data issue | Post or delete drafts; reconcile journal lines |
| Migration FK errors | Base tables missing | Run full `php artisan migrate` from clean base schema |

---

## 13. Related documentation

| Document | Description |
|----------|-------------|
| [ACCOUNTING_MODULE.md](./ACCOUNTING_MODULE.md) | Module features, schema, routes, and UI |
| `config/accounting.php` | Default GL account code mapping |
| `projectinfo_backup/mdfiles/HOSTING_NOTIFICATION_SERVER.md` | Optional Node.js notification server |
| [Laravel 12 deployment docs](https://laravel.com/docs/12.x/deployment) | Framework deployment reference |

---

## 14. Revision history

| Date | Notes |
|------|--------|
| 2026-06-10 | Point 18 — Initial server installation guide (hardware, prerequisites, steps, accounting configuration). |

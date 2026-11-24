//
=================================================================================================================================================================
=================================================================================================================================================================
=================================================================================================================================================================
=================================================================================================================================================================
=================================================================================================================================================================
=========================================================================================================================================================
Requirement **17** is extending requirement **16** to support **double-entry accounting with inventory costing**.

## Requirement 16 Recap

For each transaction type, configure the accounts used for the **transaction value**.

### Sale Invoice

| Debit               | Credit        |
| ------------------- | ------------- |
| Accounts Receivable | Sales Revenue |

If sale amount is ₹1,000:

```text
Dr Accounts Receivable    1,000
    Cr Sales Revenue      1,000
```

This records the revenue.

---

## Requirement 17

A Sale Invoice should create **two separate journal entries**:

### Entry 1: Revenue Entry

Uses the accounts configured in requirement 16.

Sale price = ₹1,000

```text
Dr Accounts Receivable    1,000
    Cr Sales Revenue      1,000
```

---

### Entry 2: Cost Entry (COGS)

Uses accounts configured on the **product itself**.

Suppose product:

```text
Product: Laptop

Inventory Asset Account = Inventory
Cost of Sales Account   = Cost of Goods Sold (COGS)

Cost Price = ₹700
Selling Price = ₹1,000
```

When sold:

```text
Dr Cost of Goods Sold      700
    Cr Inventory           700
```

This removes inventory from stock and records the cost expense.

---

## Complete Example

Product:

```text
Cost Price    = ₹700
Selling Price = ₹1,000
Qty           = 1
```

Sale Invoice created.

### Journal Entry #1 (Revenue)

```text
Dr Accounts Receivable    1,000
    Cr Sales Revenue      1,000
```

### Journal Entry #2 (Inventory Cost)

```text
Dr Cost of Goods Sold       700
    Cr Inventory            700
```

---

## Why Two Entries?

If only the revenue entry exists:

```text
Dr A/R       1,000
Cr Revenue   1,000
```

then Profit = ₹1,000 (incorrect).

The system must also record the inventory cost:

```text
Dr COGS       700
Cr Inventory  700
```

Now:

```text
Revenue = 1,000
COGS    = 700
Profit  = 300
```

which is correct.

---

## Suggested Product Setup

Each product should have:

```text
Product Name

Sales Account
Inventory Account
COGS Account
```

Example:

```text
Sales Account      : Sales Revenue
Inventory Account  : Finished Goods Inventory
COGS Account       : Cost of Goods Sold
```

---

## Database Design Example

### Product table

```php
$table->foreignId('sales_account_id')->nullable();
$table->foreignId('inventory_account_id')->nullable();
$table->foreignId('cogs_account_id')->nullable();
```

### Transaction mapping table (requirement 16)

```php
sale_invoice
    debit_account_id  => Accounts Receivable
    credit_account_id => Sales Revenue

purchase_invoice
    debit_account_id  => Inventory
    credit_account_id => Accounts Payable
```

---

### Flow When Saving Sale Invoice

```text
Create Sale Invoice

1. Revenue Journal
   Dr Accounts Receivable
   Cr Sales Revenue

2. Cost Journal
   Dr COGS
   Cr Inventory

3. Reduce stock quantity
```

This is how accounting systems such as QuickBooks, Xero, and Sage 50 typically handle inventory-based sales.
=========================================================================================================================================================
For requirements **16** and **17**, your current product table (`stocks`) already covers most of **17**, but you still need a separate configuration for **16**.

## Requirement 16: Transaction Account Mappings

You need a table to configure the default debit/credit accounts for each transaction type.

### Create a table

```php
Schema::create('transaction_account_mappings', function (Blueprint $table) {
    $table->id();

    $table->string('source_type'); // sale_invoice, purchase_invoice, credit_note, debit_note

    $table->foreignId('debit_account_id')
          ->constrained('gl_accounts');

    $table->foreignId('credit_account_id')
          ->constrained('gl_accounts');

    $table->timestamps();
});
```

### Example data

| source_type      | debit_account_id        | credit_account_id        |
| ---------------- | ----------------------- | ------------------------ |
| sale_invoice     | 2 (Accounts Receivable) | 9 (Sales Revenue)        |
| purchase_invoice | 3 (Inventory)           | 5 (Accounts Payable)     |
| credit_note      | Sales Returns Account   | 2                        |
| debit_note       | 5                       | Purchase Returns Account |

This satisfies **Point 16** because the user can configure the accounts used by each document type.

---

## Requirement 17: Product Cost Accounts

You already have:

```text
stocks.debit_account_id
stocks.credit_account_id
```

For inventory products:

```text
debit_account_id  = 10 (Cost of Goods Sold)
credit_account_id = 3  (Inventory)
```

So **no schema change is required** for point 17.

---

## What Happens on Sale Invoice

Assume:

```text
Product Cost  = 700
Product Price = 1000
Qty           = 1
```

### Journal Entry #1 (Requirement 16)

Get mapping:

```text
sale_invoice
Debit  = Accounts Receivable (id 2)
Credit = Sales Revenue (id 9)
```

Create:

```text
Dr Accounts Receivable    1,000
    Cr Sales Revenue      1,000
```

---

### Journal Entry #2 (Requirement 17)

Get accounts from product (`stocks`):

```text
debit_account_id  = 10 (COGS)
credit_account_id = 3  (Inventory)
```

Create:

```text
Dr Cost of Goods Sold      700
    Cr Inventory           700
```

---

## Additional Tables Needed

You will also need journal tables if you don't already have them:

### journal_entries

```php
id
reference_type
reference_id
transaction_date
description
```

### journal_entry_lines

```php
id
journal_entry_id
account_id
debit
credit
```

Example:

### Entry 1

| account       | debit | credit |
| ------------- | ----- | ------ |
| A/R           | 1000  | 0      |
| Sales Revenue | 0     | 1000   |

### Entry 2

| account   | debit | credit |
| --------- | ----- | ------ |
| COGS      | 700   | 0      |
| Inventory | 0     | 700    |

---

### Summary

**For Point 16**

* Add a `transaction_account_mappings` table.
* Create an Accounting Settings page where users select debit and credit accounts for:

  * Sale Invoice
  * Purchase Invoice
  * Credit Note
  * Debit Note

**For Point 17**

* Your `stocks.debit_account_id` and `stocks.credit_account_id` can already be used.
* When posting a Sale Invoice:

  1. Create the revenue journal using the mapping from Point 16.
  2. Create the cost journal using the accounts stored on the product.
  3. Reduce stock quantity.

=========================================================================================================================================================
=================================================================================================================================================================
The best practice is **not to assign an invoice number when the invoice is in Draft/Hold status**.

### Recommended Design

#### orders table

```sql
id
invoice_no          NULL
status              ENUM('draft','confirmed','cancelled')
customer_id
invoice_date
total_amount
created_at
updated_at
```

#### order_items table

```sql
id
order_id
product_id
qty
price
amount
```

### Workflow

#### 1. Save Draft

When user clicks **Save Draft**:

```php
$order = Order::create([
    'status' => 'draft',
    'invoice_no' => null,
    'customer_id' => $request->customer_id,
]);
```

Invoice number remains `NULL`.

Draft invoices can be edited unlimited times.

---

#### 2. Convert Draft to Final Invoice

When user clicks **Post / Confirm Invoice**:

```php
DB::transaction(function () use ($order) {

    $nextInvoiceNo = InvoiceSequence::generate();

    $order->update([
        'invoice_no' => $nextInvoiceNo,
        'status' => 'confirmed'
    ]);
});
```

Only now is the invoice number consumed.

Example:

```
Draft A → no number
Draft B → no number

Confirm B → INV-1001
Confirm A → INV-1002
```

No gaps.

---

### Alternative (Not Recommended)

Assign numbers immediately:

```
Draft A = INV-1001
Draft B = INV-1002

Delete Draft A

Next invoice = INV-1003
```

Now INV-1001 is missing.

Most accounting systems dislike missing invoice numbers.

---

### Better Status Structure

```php
const STATUS_DRAFT = 0;
const STATUS_CONFIRMED = 1;
const STATUS_CANCELLED = 2;
```

or

```php
draft
hold
confirmed
cancelled
```

---

### Laravel Example

```php
if ($request->action == 'draft') {

    $order = Order::create([
        'status' => 'draft',
        'invoice_no' => null,
        // other fields
    ]);

} elseif ($request->action == 'confirm') {

    DB::transaction(function () use ($order) {

        $order->update([
            'invoice_no' => InvoiceHelper::nextNumber(),
            'status' => 'confirmed'
        ]);
    });
}
```

This is how systems like **Tally, Zoho Books, QuickBooks, Odoo, ERPNext**, and many ERP/accounting applications handle draft invoices without breaking the invoice serial sequence.

=================================================================================================================================================================
I would **not** add "Draft" to `invoice_options`.

Your current table represents **invoice types**:

| id | name         |
| -- | ------------ |
| 1  | Cash Sales   |
| 2  | Credit Sales |
| 3  | Estimates    |

These answer the question:

> What kind of invoice is this?

A draft is not a type of invoice. It's a **status**.

---

### Better Design

#### invoice_options

```text
1 = Cash Sales
2 = Credit Sales
3 = Estimates
```

#### orders

```sql
id
invoice_option_id
invoice_no
status
```

Example statuses:

```php
Draft
Confirmed
Cancelled
```

or

```php
0 = Draft
1 = Confirmed
2 = Cancelled
```

---

### Example Data

| id | invoice_option_id | status    | invoice_no |
| -- | ----------------- | --------- | ---------- |
| 1  | 1 (Cash Sales)    | Draft     | NULL       |
| 2  | 2 (Credit Sales)  | Draft     | NULL       |
| 3  | 1 (Cash Sales)    | Confirmed | CS-1001    |
| 4  | 3 (Estimate)      | Confirmed | EST-1001   |

This allows:

* Draft Cash Sale
* Draft Credit Sale
* Draft Estimate
* Confirmed Cash Sale
* Confirmed Credit Sale
* Confirmed Estimate

without mixing concepts.

---

### If you add Draft to invoice_options

You end up with:

| id | name         |
| -- | ------------ |
| 1  | Cash Sales   |
| 2  | Credit Sales |
| 3  | Estimates    |
| 4  | Draft        |

Then what does this mean?

```text
invoice_option_id = 4
```

Is it:

* Draft Cash Sale?
* Draft Credit Sale?
* Draft Estimate?

You lose the actual invoice type.

---

### Recommended Structure

```sql
orders
-------
id
invoice_option_id   -- Cash/Credit/Estimate
status              -- Draft/Confirmed/Cancelled
invoice_no
```

Example:

```php
$order->invoice_option_id = 1; // Cash Sale
$order->status = 'draft';
$order->invoice_no = null;
```

Later:

```php
$order->status = 'confirmed';
$order->invoice_no = 'CS-1001';
```

This keeps invoice types and invoice states separate, which is the cleaner and more scalable design.

=================================================================================================================================================================
No, **Draft** and **Confirmed** should not be an invoice type.

You should add an **invoice status** field.

### Invoice Type (what kind of invoice)

```text
Cash Sales
Credit Sales
Estimate
Purchase
```

### Invoice Status (current state)

```text
Draft
Confirmed
Cancelled
```

So your order/invoice record would look like:

| invoice_type | status    |
| ------------ | --------- |
| Cash Sales   | Draft     |
| Cash Sales   | Confirmed |
| Credit Sales | Draft     |
| Credit Sales | Confirmed |
| Estimate     | Draft     |
| Estimate     | Confirmed |

### Database Example

Keep your existing table:

```text
invoice_options
---------------
1 Cash Sales
2 Credit Sales
3 Estimates
```

Add a new column to orders:

```sql
ALTER TABLE orders
ADD COLUMN invoice_status ENUM('draft','confirmed','cancelled')
DEFAULT 'draft';
```

Or use integers:

```php
0 = Draft
1 = Confirmed
2 = Cancelled
```

### Invoice Number Logic

When status is `draft`:

```text
invoice_no = NULL
```

When user confirms:

```text
invoice_no = INV-1001
status = confirmed
```

This prevents gaps in invoice numbering.

### My recommendation

```text
invoice_option_id   -> Cash Sale / Credit Sale / Estimate
invoice_status      -> Draft / Confirmed / Cancelled
invoice_no          -> NULL until confirmed
```

This is the structure most accounting and ERP systems use.
=================================================================================================================================================================
In this requirement, **"work in tandem with"** means:

> The two fields should work together to generate invoice numbers.

For example, when creating a store, you add:

```text
Invoice Sequence Format : AB######
Next Sale Invoice Number : 2348
```

Then invoice numbers become:

```text
AB002348
AB002349
AB002350
AB002351
```

---

### Suggested Database Design

#### stores table

```sql
stores
------
id
name
sale_invoice_format           -- AB######
next_sale_invoice_number      -- 2348
```

Example data:

```text
sale_invoice_format      = AB######
next_sale_invoice_number = 2348
```

---

### Generate Invoice Number

When saving a confirmed invoice:

```php
$store = Store::find($storeId);

$number = str_pad(
    $store->next_sale_invoice_number,
    6,
    '0',
    STR_PAD_LEFT
);

$invoiceNo = str_replace(
    '######',
    $number,
    $store->sale_invoice_format
);
```

Result:

```text
AB002348
```

---

### Increment After Use

After invoice creation:

```php
$store->increment('next_sale_invoice_number');
```

Now:

```text
next_sale_invoice_number = 2349
```

Next invoice:

```text
AB002349
```

---

### What the client means by:

> "ensure that once the next number in sequence has been generated, then the system doesn't check that field anymore"

I believe they mean:

1. Use the value **2348** only as the starting point.
2. After invoice generation, the system should use the updated value (**2349**, **2350**, etc.).
3. Don't keep re-reading the original starting number from configuration elsewhere.

In practice, simply storing and incrementing `next_sale_invoice_number` achieves this.

---

### Better Naming

Instead of:

```text
next_sale_invoice_number
```

I would use:

```text
current_sale_invoice_sequence
```

or

```text
next_invoice_counter
```

because the value continuously changes:

```text
2348
2349
2350
2351
```

---

### Handling Draft Invoices

If you implement drafts:

```text
Draft Invoice
    -> do NOT generate invoice number
    -> do NOT increment sequence

Confirmed Invoice
    -> generate AB002348
    -> increment to 2349
```

This prevents gaps in invoice numbering.

### Complete Example

Store settings:

```text
Format  : AB######
Next No : 2348
```

Invoices:

```text
Draft Invoice              -> no number

Confirm Invoice #1         -> AB002348
counter becomes            -> 2349

Confirm Invoice #2         -> AB002349
counter becomes            -> 2350

Confirm Invoice #3         -> AB002350
counter becomes            -> 2351
```

This is likely exactly what the requirement is asking for.

=================================================================================================================================================================
In a Laravel migration, you can add a `credit_limit` column like this:

```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->decimal('credit_limit', 12, 2)
                  ->default(0.00);
        });
    }

    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('credit_limit');
        });
    }
};
```

### Create the migration

```bash current_sale_invoice_sequence
php artisan make:migration add_credit_limit_to_suppliers_table --table=suppliers
php artisan make:migration add_current_sale_invoice_sequence_to_stores_table --table=stores
```

Then run:

```bash
php artisan migrate
```

This will create a `DECIMAL(12,2)` column named `credit_limit` with a default value of `0.00`. For example, it can store values up to `9999999999.99`.


=================================================================================================================================================================
Point 11 is implemented. Here is what was completed:

Store create / edit UI
All document types now have prefix + next number fields on:

Create store modal (partials/admin/model.blade.php)
Edit store modal (SuperAdmin/store-list.blade.php)
Shared partial: admin/partials/store-document-sequences-fields.blade.php covers: Sale Invoice, Purchase Invoice, Purchase Order, Purchase Return, GRN, Credit Note, Debit Note, Receipt.

Document numbering (wired to store sequences)
Document	When issued	Store columns
Purchase Order
On create
purchase_order_prefix / next_purchase_order_number
Purchase Return
On create
purchase_return_prefix / next_purchase_return_number
GRN
On create
grn_prefix / next_grn_number
Purchase Invoice
On confirm (or resume from hold)
purchase_invoice_prefix / next_purchase_invoice_number
Receipt (customer payment)
When reference_no is blank
receipt_prefix / next_receipt_number
Receipt (supplier payment)
When reference_no is blank
same, using invoice store or selected store

📄 Document Numbering (Store-Based Sequences)
| Document Type              | When Number is Generated      | Store Columns Used                                                      | Notes                                        |
| -------------------------- | ----------------------------- | ----------------------------------------------------------------------- | -------------------------------------------- |
| Purchase Order             | On create                     | `purchase_order_prefix`, `next_purchase_order_number`                   | Generated immediately when PO is created     |
| Purchase Return            | On create                     | `purchase_return_prefix`, `next_purchase_return_number`                 | Generated immediately when return is created |
| GRN (Goods Receipt Note)   | On create                     | `grn_prefix`, `next_grn_number`                                         | Generated when GRN is created                |
| Purchase Invoice           | On confirm / resume from hold | `purchase_invoice_prefix`, `next_purchase_invoice_number`               | Only assigned when invoice is finalized      |
| Receipt (Customer Payment) | When `reference_no` is blank  | `receipt_prefix`, `next_receipt_number`                                 | Generated only if no reference number exists |
| Receipt (Supplier Payment) | When `reference_no` is blank  | `receipt_prefix`, `next_receipt_number` (or store-based selected store) | Uses same sequence logic as customer receipt |




Added resolvePurchaseInvoiceNumber() in PurchaseDocumentController — uses manual supplier number if entered, otherwise issues from the store sequence (old uniqueRef('PINV') kept as fallback).

Purchase invoice confirm no longer requires a supplier invoice number; blank uses the store sequence.

Credit / Debit notes
Store fields are saved and editable. Generators are not wired yet because those modules/tables are not in the codebase.

How it works
Example: prefix AB, next 2348 → AB002348, AB002349, …
Counter increments after each issued number. Changing “next number” on store edit resets the sequence from that value.

Old validation/requirements are commented, not deleted, per your request.

To verify: create a store with custom prefixes (e.g. PO=PO, PINV=PINV, start at 100), then create a PO, GRN, purchase return, confirm a purchase invoice with blank supplier number, and record a payment without a reference — each should use the store’s sequence.
=================================================================================================================================================================


Point 15 — Two purchase invoice numbers
Purchase invoices now have:

Internal # (internal_invoice_number) — auto-generated from the store purchase-invoice sequence on confirm
Supplier # (supplier_invoice_number form field → invoice_number column) — optional free text
The create form, list, payments, returns, and supplier ledger all reflect both fields. Old single-field logic is commented, not removed.

Point 16 — Transaction account mappings
New Transaction mappings screen under Accounting lets you set default debit/credit GL accounts per document type:

Sale invoice, Purchase invoice, Credit note, Debit note, Customer receipt, Supplier payment
Path: Accounting → Transaction mappings (/admin/accounting/transaction-mappings)

Point 17 — Two sale invoice journals
Sale confirmation now posts two separate journals:

Journal	Source type	Accounts used
Revenue
order_sale
Mapping from point 16 (e.g. A/R + Sales Revenue)
Cost
order_cogs
Product debit_account_id (COGS) + credit_account_id (Inventory)
The old single combined order journal is commented out in AccountingService.

File change list
New files
File	Location
Migration — internal invoice number
database/migrations/2026_06_05_170000_add_internal_invoice_number_to_supplier_purchase_invoices.php
Migration — transaction mappings
database/migrations/2026_06_05_171000_create_transaction_account_mappings_table.php
Model
app/Models/TransactionAccountMapping.php
Mapping service
app/Services/TransactionAccountMappingService.php
Mapping controller
app/Http/Controllers/admin/TransactionAccountMappingController.php
Mapping UI
resources/views/admin/accounting/transaction-account-mappings.blade.php
Modified files
File	Location	Points
Purchase invoice model
app/Models/SupplierPurchaseInvoice.php
15
Purchase document controller
app/Http/Controllers/admin/PurchaseDocumentController.php
15
Draft reference service
app/Services/DraftReferenceService.php
15
Accounting service
app/Services/AccountingService.php
16, 17
Supplier ledger controller
app/Http/Controllers/admin/SupplierLedgerController.php
15
Transaction mapping controller
app/Http/Controllers/admin/TransactionAccountMappingController.php
16
Routes
routes/web.php
16
Purchase invoice create view
resources/views/admin/purchases/purchase-invoices-create.blade.php
15
Purchase invoice index view
resources/views/admin/purchases/purchase-invoices-index.blade.php
15
Purchase returns create view
resources/views/admin/purchases/purchase-returns-create.blade.php
15
Supplier payments create view
resources/views/admin/suppliers/payments-create.blade.php
15
Supplier payments index view
resources/views/admin/suppliers/payments-index.blade.php
15
Account module hub
resources/views/admin/account-module.blade.php
16, 17
Sidebar
resources/views/partials/admin/sidebar_superadmin.blade.php
16
Note: Credit note and debit note mappings are configured in the UI, but those document generators are not wired yet — the table is ready for when those modules are added.

📁 File Change List
🆕 New Files
| Type       | File                       | Location                                                                                              |
| ---------- | -------------------------- | ----------------------------------------------------------------------------------------------------- |
| Migration  | Internal invoice number    | `database/migrations/2026_06_05_170000_add_internal_invoice_number_to_supplier_purchase_invoices.php` |
| Migration  | Transaction mappings table | `database/migrations/2026_06_05_171000_create_transaction_account_mappings_table.php`                 |
| Model      | TransactionAccountMapping  | `app/Models/TransactionAccountMapping.php`                                                            |
| Service    | Mapping service            | `app/Services/TransactionAccountMappingService.php`                                                   |
| Controller | Mapping controller         | `app/Http/Controllers/admin/TransactionAccountMappingController.php`                                  |
| View       | Mapping UI                 | `resources/views/admin/accounting/transaction-account-mappings.blade.php`                             |


✏️ Modified Files
| File                                | Location                                                             | Notes / Points |
| ----------------------------------- | -------------------------------------------------------------------- | -------------- |
| SupplierPurchaseInvoice model       | `app/Models/SupplierPurchaseInvoice.php`                             | 15             |
| PurchaseDocumentController          | `app/Http/Controllers/admin/PurchaseDocumentController.php`          | 15             |
| DraftReferenceService               | `app/Services/DraftReferenceService.php`                             | 15             |
| AccountingService                   | `app/Services/AccountingService.php`                                 | 16, 17         |
| SupplierLedgerController            | `app/Http/Controllers/admin/SupplierLedgerController.php`            | 15             |
| TransactionAccountMappingController | `app/Http/Controllers/admin/TransactionAccountMappingController.php` | 16             |
| Routes                              | `routes/web.php`                                                     | 16             |
| Purchase invoice create view        | `resources/views/admin/purchases/purchase-invoices-create.blade.php` | 15             |
| Purchase invoice index view         | `resources/views/admin/purchases/purchase-invoices-index.blade.php`  | 15             |
| Purchase returns create view        | `resources/views/admin/purchases/purchase-returns-create.blade.php`  | 15             |
| Supplier payments create view       | `resources/views/admin/suppliers/payments-create.blade.php`          | 15             |
| Supplier payments index view        | `resources/views/admin/suppliers/payments-index.blade.php`           | 15             |
| Account module hub                  | `resources/views/admin/account-module.blade.php`                     | 16, 17         |
| Sidebar                             | `resources/views/partials/admin/sidebar_superadmin.blade.php`        | 16             |

=================================================================================================================================================================
=================================================================================================================================================================
=================================================================================================================================================================
=================================================================================================================================================================
=================================================================================================================================================================
=================================================================================================================================================================
=================================================================================================================================================================
=================================================================================================================================================================
=================================================================================================================================================================
=================================================================================================================================================================
=================================================================================================================================================================
=================================================================================================================================================================
=================================================================================================================================================================
=================================================================================================================================================================
=================================================================================================================================================================
=================================================================================================================================================================
=================================================================================================================================================================
=================================================================================================================================================================
=================================================================================================================================================================
=================================================================================================================================================================

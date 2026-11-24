1. Core Concept (What you’re adding)

Right now your POS likely does:

Sale → Payment → Done

You need to support:

Sale → Unpaid / Partially Paid → Track → Later Payment(s)

So instead of “every sale is paid immediately”, you allow:

credit sales
due dates
outstanding balances
2. Database Design Changes
A. Update sales table

Add fields:

$table->enum('payment_status', ['paid', 'partial', 'unpaid'])->default('paid');
$table->decimal('total_amount', 10, 2);
$table->decimal('paid_amount', 10, 2)->default(0);
$table->decimal('due_amount', 10, 2);
$table->date('due_date')->nullable();
B. Create payments table

This is critical—don’t store payments directly in sales.

Schema::create('payments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
    $table->decimal('amount', 10, 2);
    $table->dateTime('paid_at');
    $table->string('method')->nullable(); // cash, UPI, card
    $table->text('note')->nullable();
    $table->timestamps();
});
C. Ensure customers table exists

Each credit sale MUST belong to a customer.

$table->foreignId('customer_id')->nullable()->constrained();
3. Models (Laravel)
Sale.php
public function payments()
{
    return $this->hasMany(Payment::class);
}

public function customer()
{
    return $this->belongsTo(Customer::class);
}
Payment.php
public function sale()
{
    return $this->belongsTo(Sale::class);
}
4. Sale Creation Logic

When creating a sale:

Case 1: Paid immediately
$paidAmount = $request->paid_amount;
$total = $request->total;

$status = 'paid';
if ($paidAmount == 0) $status = 'unpaid';
elseif ($paidAmount < $total) $status = 'partial';

Sale::create([
    'total_amount' => $total,
    'paid_amount' => $paidAmount,
    'due_amount' => $total - $paidAmount,
    'payment_status' => $status,
    'due_date' => $request->due_date,
    'customer_id' => $request->customer_id
]);

If paid > 0 → also insert into payments.

5. Adding a Payment Later

Controller method:

public function addPayment(Request $request, $saleId)
{
    $sale = Sale::findOrFail($saleId);

    $payment = Payment::create([
        'sale_id' => $sale->id,
        'amount' => $request->amount,
        'paid_at' => now(),
        'method' => $request->method
    ]);

    $sale->paid_amount += $request->amount;
    $sale->due_amount -= $request->amount;

    if ($sale->due_amount <= 0) {
        $sale->payment_status = 'paid';
        $sale->due_amount = 0;
    } else {
        $sale->payment_status = 'partial';
    }

    $sale->save();
}
6. Tracking Accounts Receivable

Now you can easily query:

All unpaid / credit sales
Sale::where('payment_status', '!=', 'paid')->get();
Overdue sales
Sale::where('due_date', '<', now())
    ->where('payment_status', '!=', 'paid')
    ->get();
7. UI Features You Should Add
A. During Sale
Payment type:
Full Payment
Partial Payment
Credit
Input:
Paid amount
Due date
B. Customer Ledger Page

Show:

Total purchases
Total paid
Outstanding balance
C. Receivables Dashboard
All unpaid invoices
Overdue invoices (highlight in red)
Filter by customer
D. Payment Entry Screen
Select sale
Enter amount
Choose method
8. Optional (But Powerful)
Aging Report

Group dues:

0–30 days
31–60 days
60+ days
Notifications
Email/SMS reminders before due date
Soft Rules
Prevent credit sale without customer
Prevent overpayment
9. Key Design Advice

Don’t try to “hack” this into your existing payment logic.

Instead:

Treat Sale as invoice
Treat Payment as separate entity
Always compute balance from these

This keeps your system scalable and accounting-friendly.

If you want, I can:

Design full migration files
Build API endpoints
Or show Blade UI for this system

Just tell me 👍

////=======================================================================
That’s actually a solid starting point—you already have the right business concepts. The issue isn’t adding something new, it’s making your “credit sales” behave like real accounts receivable instead of just a label.

Right now you likely have:

Cash Sales → fully paid
Credit Sales → probably just marked as “unpaid”
Estimates → quotations (not real sales yet)

What you need is to formalize the lifecycle and data behind “credit sales.”

1. Normalize Your Invoice Types

Instead of mixing logic everywhere, define this clearly in your sales table:

$table->enum('type', ['cash', 'credit', 'estimate']);
2. Define Behavior for Each Type
🟢 Cash Sale
Must be fully paid
due_amount = 0
payment_status = paid
🔵 Credit Sale (this is your main work)
Can be:
unpaid
partially paid
fully paid later
Requires:
customer_id
due_date
Uses payments table
🟡 Estimate
Not a real transaction
No payments allowed
Can be converted → sale
3. Enforce Rules in Code (Important)

Don’t rely on UI only—enforce in backend:

if ($type === 'cash' && $paid_amount < $total) {
    throw new Exception('Cash sale must be fully paid');
}

if ($type === 'credit' && !$customer_id) {
    throw new Exception('Credit sale requires customer');
}

if ($type === 'estimate' && $paid_amount > 0) {
    throw new Exception('Cannot accept payment for estimate');
}
4. Convert Estimate → Sale

This is a key feature many POS systems miss.

Add a method:

public function convertToSale($type)
{
    if ($this->type !== 'estimate') {
        throw new Exception('Only estimates can be converted');
    }

    $this->type = $type; // cash or credit
    $this->save();
}
5. Payment Flow (Only for Cash + Credit)
When creating:
Type	Paid Amount	Result
Cash	= total	Paid
Credit	0	Unpaid
Credit	< total	Partial
6. Add Payments ONLY for Credit Sales
if ($sale->type !== 'credit') {
    throw new Exception('Payments only allowed for credit sales');
}
7. Key Queries You’ll Use
All receivables:
Sale::where('type', 'credit')
    ->where('payment_status', '!=', 'paid')
    ->get();
Customer outstanding:
Sale::where('customer_id', $id)
    ->where('type', 'credit')
    ->sum('due_amount');
8. UI Structure (Simple but effective)
Invoice Screen
Select type:
Cash
Credit
Estimate

If Credit selected:

Show:
Customer dropdown
Due date
Paid amount (optional)
Credit Management Page
List all credit sales
Columns:
Customer
Total
Paid
Due
Due date
Status
Payment Modal
Enter amount
Select method
Submit
9. Common Mistake to Avoid

Don’t do this:

❌ Storing only “remaining balance”
❌ Updating totals without tracking payments

Always:

✅ Store every payment as a record
✅ Derive totals from it
10. If Your Current System is Messy

If your current POS:

mixes payments inside sales
doesn’t track partial payments

Then honestly, the cleanest approach is:

Introduce payments table and gradually migrate credit sales to it

If you want, I can next:

Refactor your existing schema step-by-step
Or give a full Laravel controller + routes for this system
Or design a clean UI workflow for your POS

Just tell me what part you're stuck on.

///================================================
Your current structure is fine for sales + items, but it’s missing one critical layer:
👉 financial tracking (ledger / payments)

Right now, orders = invoice
But for credit sales, you need:

orders = invoice
payments = money received
ledger = running balance (optional but powerful)

1. Don’t Modify order_items ❌

Leave it as-is. It’s correct.

2. Extend orders Table (Minimal Changes)

Add these columns:

$table->decimal('paid_amount', 10, 2)->default(0);
$table->decimal('due_amount', 10, 2)->default(0);
$table->date('due_date')->nullable();
$table->enum('payment_status', ['paid', 'partial', 'unpaid'])->default('paid');

👉 Use your existing invoice_type:

cash
credit
estimate
3. Create payments Table (MUST HAVE)

This is the core of your ledger system.

Schema::create('payments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained()->cascadeOnDelete();
    $table->foreignId('customer_id')->nullable();
    $table->decimal('amount', 10, 2);
    $table->string('method')->nullable(); // cash, upi, card
    $table->timestamp('paid_at');
    $table->text('note')->nullable();
    $table->timestamps();
});
4. (Optional but Recommended) Customer Ledger Table

If you want a proper accounting-style ledger:

Schema::create('customer_ledgers', function (Blueprint $table) {
    $table->id();
    $table->foreignId('customer_id');
    $table->foreignId('order_id')->nullable();

    $table->enum('type', ['debit', 'credit']); 
    // debit = sale (customer owes)
    // credit = payment (customer pays)

    $table->decimal('amount', 10, 2);
    $table->decimal('balance', 10, 2);

    $table->string('description')->nullable();
    $table->timestamps();
});
5. Flow: When Invoice is Created
🧾 Cash Sale
paid_amount = total_price
due_amount = 0
payment_status = paid

Insert:

1 payment record
💳 Credit Sale
Case 1: Fully unpaid
paid_amount = 0
due_amount = total_price
payment_status = unpaid
Case 2: Partial payment
paid_amount = X
due_amount = total - X
payment_status = partial

Insert:

payment (if X > 0)
Ledger Entry (if using ledger table)

For credit sale:

CustomerLedger::create([
    'customer_id' => $order->customer_id,
    'order_id' => $order->id,
    'type' => 'debit',
    'amount' => $order->total_price,
    'balance' => previous_balance + total_price,
    'description' => 'Invoice #' . $order->id
]);
6. When Payment is Made Later
Step 1: Insert payment
Payment::create([
    'order_id' => $order->id,
    'customer_id' => $order->customer_id,
    'amount' => $request->amount,
    'method' => $request->method,
    'paid_at' => now()
]);
Step 2: Update order
$order->paid_amount += $request->amount;
$order->due_amount -= $request->amount;

if ($order->due_amount <= 0) {
    $order->payment_status = 'paid';
    $order->due_amount = 0;
} else {
    $order->payment_status = 'partial';
}

$order->save();
Step 3: Ledger entry
CustomerLedger::create([
    'customer_id' => $order->customer_id,
    'order_id' => $order->id,
    'type' => 'credit',
    'amount' => $request->amount,
    'balance' => previous_balance - $request->amount,
    'description' => 'Payment for Invoice #' . $order->id
]);
7. How to Track Credit Sales
All unpaid invoices:
Order::where('invoice_type', 'credit')
    ->where('payment_status', '!=', 'paid')
    ->get();
Customer total due:
Order::where('customer_id', $id)
    ->where('invoice_type', 'credit')
    ->sum('due_amount');
Ledger view (best UX)

Show:

Date	Type	Amount	Balance
Sale	+5000	5000	
Payment	-2000	3000	
8. Important Design Advice

Right now your system is sales-centric.
To support credit properly, you must think:

❗ “Money flow is separate from sales”

That’s why:

Orders ≠ payments
Payments can be multiple
Ledger is cumulative
9. Minimum vs Best Approach
✅ Minimum (quick implementation)
Add fields to orders
Create payments table
🚀 Best (scalable accounting)
Add customer_ledgers table
Maintain running balance
10. One Critical Rule

Never do this:

$order->due_amount = something random

Always derive it from:

due = total - sum(payments)

If you want, next I can:

Refactor your exact migration files
Write full Laravel controllers (store, payment, ledger)
Or design a clean POS UI flow for this system

Just tell me 👍

/////////////////========================================================
What your client is asking for is essentially an **Accounts Receivable (credit sales) module** layered on top of your existing POS. This isn’t just a small tweak—it touches sales flow, database design, and reporting—so you’ll want to structure it cleanly.

Here’s a practical, developer-friendly flow for implementing it in Laravel.

---

# 🧾 1. Understand the New Flow (Conceptually)

Current POS flow (likely):

```
Create Sale → Immediate Payment → Invoice Closed
```

New flow with credit:

```
Create Sale → Choose Payment Type:
    ├── Cash → Paid immediately
    └── Credit → Mark as "Unpaid" → Track as Receivable

Later:
Receive Payment → Reduce Balance → Mark Paid when cleared
```

---

# 🗂️ 2. Database Changes

### A. Update `sales` table

Add fields:

```php
$table->enum('payment_type', ['cash', 'credit'])->default('cash');
$table->decimal('total_amount', 10, 2);
$table->decimal('paid_amount', 10, 2)->default(0);
$table->decimal('due_amount', 10, 2)->default(0);
$table->timestamp('due_date')->nullable();
$table->enum('status', ['paid', 'partial', 'unpaid'])->default('paid');
```

---

### B. Create `payments` table

Track all payments (important for partial payments):

```php
Schema::create('payments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
    $table->decimal('amount', 10, 2);
    $table->timestamp('payment_date');
    $table->string('method')->nullable(); // cash, bank, etc.
    $table->text('note')->nullable();
    $table->timestamps();
});
```

---

### C. Optional: Customers table (if not already)

Credit only makes sense if tied to a customer.

Add:

```php
$table->decimal('credit_limit', 10, 2)->nullable();
```

---

# 🔄 3. Update Sale Creation Flow

### UI Changes:

* Add **Payment Type dropdown**

  * Cash
  * Credit

If **Credit selected**:

* Show:

  * Due date
  * Optional initial payment

---

### Controller Logic:

```php
if ($request->payment_type == 'credit') {
    $sale->paid_amount = $request->paid_amount ?? 0;
    $sale->due_amount = $sale->total_amount - $sale->paid_amount;

    if ($sale->paid_amount == 0) {
        $sale->status = 'unpaid';
    } elseif ($sale->due_amount > 0) {
        $sale->status = 'partial';
    } else {
        $sale->status = 'paid';
    }
} else {
    $sale->paid_amount = $sale->total_amount;
    $sale->due_amount = 0;
    $sale->status = 'paid';
}
```

---

# 💰 4. Payment Collection Flow

Create a new module:

## "Receive Payment"

### Steps:

1. Search sale (by invoice / customer)
2. Show:

   * Total
   * Paid
   * Due
3. Enter payment amount
4. Save payment

---

### Controller Example:

```php
$payment = Payment::create([
    'sale_id' => $sale->id,
    'amount' => $request->amount,
    'payment_date' => now(),
]);

$sale->paid_amount += $request->amount;
$sale->due_amount -= $request->amount;

if ($sale->due_amount <= 0) {
    $sale->status = 'paid';
} else {
    $sale->status = 'partial';
}

$sale->save();
```

---

# 📊 5. Accounts Receivable Tracking

Create a new page:

## "Credit Sales / Receivables"

Show:

* Customer name
* Invoice number
* Total amount
* Paid
* Due
* Due date
* Status

---

### Query example:

```php
Sale::where('payment_type', 'credit')
    ->where('due_amount', '>', 0)
    ->get();
```

---

# ⏰ 6. Optional Enhancements (Highly Recommended)

### A. Overdue Tracking

```php
if ($sale->due_date < now() && $sale->due_amount > 0)
```

Add badge: **Overdue**

---

### B. Customer Ledger

Per customer:

* All sales
* All payments
* Running balance

---

### C. Credit Limit Check

Before sale:

```php
if ($customer->total_due + $new_sale > $customer->credit_limit) {
    // block or warn
}
```

---

### D. Reports

Add:

* Total receivables
* Aging report (0–30 days, 30–60, etc.)

---

# 🔐 7. Validation Rules

* Payment cannot exceed due
* Credit sale must have customer
* Due date required for credit

---

# 🧠 Architecture Tip

Do NOT:

* Store everything only in `sales`

DO:

* Separate `sales` and `payments` (clean accounting logic)

---

# 🚀 Implementation Order (Recommended)

1. Database changes
2. Update sale creation
3. Build payment module
4. Receivable listing
5. Reports & polish

---

# 🧩 Simple Mental Model

Think of it like this:

* **Sales = What customer owes**
* **Payments = What customer pays**
* **Due = Sales - Payments**

---

If you want, I can next:

* Design **API routes + controllers structure**
* Give **Blade UI layout**
* Or convert this into a **step-by-step Laravel code implementation**

/////////////////========================================================
Good—you already have a solid base. The key is **not rebuilding everything**, but layering credit logic cleanly on top of your existing `orders`, `order_items`, and `customers`.

I’ll walk you through a **step-by-step Laravel implementation** tailored to your current schema.

---

# ⚠️ First: What Needs Fixing in Your Current Schema

You already have:

* `paid_amount`
* `due_amount`
* `due_date`
* `payment_method`

👉 But you’re missing **payment history tracking**.
Right now, you can’t support:

* partial payments properly
* audit trail
* multiple payments per order

So we add a **payments table**.

---

# 🧱 STEP 1: Create Payments Table

### Migration

```bash
php artisan make:migration create_order_payments_table
```

```php
Schema::create('order_payments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained()->cascadeOnDelete();
    $table->foreignId('customer_id')->nullable();
    $table->double('amount');
    $table->string('method')->nullable(); // cash, bank, upi
    $table->timestamp('payment_date');
    $table->text('note')->nullable();
    $table->timestamps();
});
```

```bash
php artisan migrate
```

---

# 🧱 STEP 2: Update Orders Table (Small Changes)

You already have most fields. Just standardize status.

### Suggested `status` values:

```php
0 = unpaid
1 = paid
2 = partial
```

If needed, add a column:

```bash
php artisan make:migration update_orders_status
```

---

# 🧩 STEP 3: Eloquent Relationships

## Order Model

```php
public function payments()
{
    return $this->hasMany(OrderPayment::class);
}

public function customer()
{
    return $this->belongsTo(Customer::class);
}
```

---

## OrderPayment Model

```php
class OrderPayment extends Model
{
    protected $fillable = [
        'order_id',
        'customer_id',
        'amount',
        'method',
        'payment_date',
        'note'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
```

---

# 🛒 STEP 4: Update Order Creation (Credit Support)

## Controller: Store Order

### Key logic:

```php
$order = new Order();

$order->customer_id = $request->customer_id;
$order->customer_name = $request->customer_name;
$order->total_price = $request->total_price;

$order->payment_method = $request->payment_method; // cash or credit

if ($request->payment_method == 'credit') {

    $paid = $request->paid_amount ?? 0;

    $order->paid_amount = $paid;
    $order->due_amount = $order->total_price - $paid;
    $order->due_date = $request->due_date;

    if ($paid == 0) {
        $order->status = 0; // unpaid
    } elseif ($order->due_amount > 0) {
        $order->status = 2; // partial
    } else {
        $order->status = 1; // paid
    }

} else {
    // CASH SALE
    $order->paid_amount = $order->total_price;
    $order->due_amount = 0;
    $order->status = 1;
}

$order->save();
```

---

## Optional: Save Initial Payment

```php
if ($order->paid_amount > 0) {
    OrderPayment::create([
        'order_id' => $order->id,
        'customer_id' => $order->customer_id,
        'amount' => $order->paid_amount,
        'method' => $request->payment_method,
        'payment_date' => now()
    ]);
}
```

---

# 💰 STEP 5: Receive Payment Module

## Route

```php
Route::post('/orders/{id}/pay', [OrderController::class, 'receivePayment']);
```

---

## Controller Method

```php
public function receivePayment(Request $request, $id)
{
    $order = Order::findOrFail($id);

    $amount = $request->amount;

    if ($amount > $order->due_amount) {
        return back()->with('error', 'Amount exceeds due');
    }

    // Save payment
    OrderPayment::create([
        'order_id' => $order->id,
        'customer_id' => $order->customer_id,
        'amount' => $amount,
        'method' => $request->method,
        'payment_date' => now()
    ]);

    // Update order
    $order->paid_amount += $amount;
    $order->due_amount -= $amount;
    $order->last_payment_at = now();

    if ($order->due_amount == 0) {
        $order->status = 1; // paid
    } else {
        $order->status = 2; // partial
    }

    $order->save();

    return back()->with('success', 'Payment added');
}
```

---

# 📊 STEP 6: Credit / Receivables List

## Controller

```php
public function receivables()
{
    $orders = Order::where('payment_method', 'credit')
        ->where('due_amount', '>', 0)
        ->with('customer')
        ->latest()
        ->get();

    return view('orders.receivables', compact('orders'));
}
```

---

## Blade Example

```php
@foreach($orders as $order)
<tr>
    <td>{{ $order->id }}</td>
    <td>{{ $order->customer_name }}</td>
    <td>{{ $order->total_price }}</td>
    <td>{{ $order->paid_amount }}</td>
    <td>{{ $order->due_amount }}</td>
    <td>{{ $order->due_date }}</td>

    <td>
        @if($order->due_date && $order->due_date < now())
            <span class="text-danger">Overdue</span>
        @endif
    </td>
</tr>
@endforeach
```

---

# 👤 STEP 7: Customer Ledger (Important)

## Controller

```php
public function customerLedger($customer_id)
{
    $customer = Customer::findOrFail($customer_id);

    $orders = Order::where('customer_id', $customer_id)->get();
    $payments = OrderPayment::where('customer_id', $customer_id)->get();

    return view('customers.ledger', compact('customer', 'orders', 'payments'));
}
```

---

# 🧠 STEP 8: Business Logic Rules

Implement these checks:

### ✅ Must have customer for credit

```php
if ($request->payment_method == 'credit' && !$request->customer_id) {
    return back()->with('error', 'Customer required for credit');
}
```

---

### ✅ Prevent overpayment

Already handled ✔

---

### ✅ Optional: Credit Limit

Add column in customers:

```php
$table->double('credit_limit')->nullable();
```

---

# 📈 STEP 9: Quick Reports

### Total Receivable

```php
$total_due = Order::where('due_amount', '>', 0)->sum('due_amount');
```

---

### Overdue

```php
$overdue = Order::where('due_date', '<', now())
    ->where('due_amount', '>', 0)
    ->get();
```

---

# 🧩 FINAL FLOW (What You Built)

```text
Create Order
   ↓
Select Payment Type
   ↓
If Credit → Save Due
   ↓
Track in Receivables
   ↓
Receive Payments
   ↓
Update Status (unpaid → partial → paid)
```

---

# 🚀 If You Want Next Level

I can help you with:

* Full **Blade UI (POS screen changes)**
* **Vue/React POS frontend logic**
* **Invoice PDF with credit info**
* **Aging report (0–30, 30–60 days)**

Just tell me 👍

/////////////////========================================================
Great—now we’ll turn your backend into a **complete usable POS experience** with:

1. 🖥️ Blade UI (POS screen with credit option)
2. 🧾 Invoice PDF (with due + payment history)
3. 📊 Aging Report (0–30, 30–60, 60+ days)

I’ll keep it **practical + copy-paste ready**, not theoretical.

---

# 🖥️ 1. POS BLADE UI (Credit Sale Support)

## 👉 Add Payment Section in POS Screen

In your POS Blade (e.g. `orders/create.blade.php`):

```html
<div class="card mt-3">
    <div class="card-body">

        <!-- Payment Type -->
        <div class="form-group">
            <label>Payment Type</label>
            <select name="payment_method" id="payment_method" class="form-control">
                <option value="cash">Cash</option>
                <option value="credit">Credit</option>
            </select>
        </div>

        <!-- Paid Amount -->
        <div class="form-group">
            <label>Paid Amount</label>
            <input type="number" name="paid_amount" id="paid_amount" class="form-control" value="0">
        </div>

        <!-- Due Date (only for credit) -->
        <div class="form-group d-none" id="due_date_section">
            <label>Due Date</label>
            <input type="date" name="due_date" class="form-control">
        </div>

        <!-- Summary -->
        <div class="mt-3">
            <p>Total: <span id="total_amount">{{ $total ?? 0 }}</span></p>
            <p>Due: <span id="due_amount">0</span></p>
        </div>

    </div>
</div>
```

---

## 👉 Add JS Logic

```html
<script>
document.getElementById('payment_method').addEventListener('change', function () {
    let isCredit = this.value === 'credit';

    document.getElementById('due_date_section')
        .classList.toggle('d-none', !isCredit);
});

document.getElementById('paid_amount').addEventListener('input', function () {
    let total = parseFloat(document.getElementById('total_amount').innerText) || 0;
    let paid = parseFloat(this.value) || 0;

    let due = total - paid;
    document.getElementById('due_amount').innerText = due.toFixed(2);
});
</script>
```

---

## 👉 Customer Required for Credit

```html
<select name="customer_id" id="customer_id" class="form-control" required>
    <option value="">Select Customer</option>
    @foreach($customers as $customer)
        <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
    @endforeach
</select>
```

---

# 🧾 2. INVOICE PDF (WITH CREDIT INFO)

Use package:

```bash
composer require barryvdh/laravel-dompdf
```

---

## 👉 Controller

```php
use Barryvdh\DomPDF\Facade\Pdf;

public function invoice($id)
{
    $order = Order::with('payments', 'customer', 'items')->findOrFail($id);

    $pdf = Pdf::loadView('pdf.invoice', compact('order'));

    return $pdf->download('invoice-'.$order->id.'.pdf');
}
```

---

## 👉 Blade: `resources/views/pdf/invoice.blade.php`

```html
<h2>Invoice #{{ $order->id }}</h2>

<p><strong>Customer:</strong> {{ $order->customer_name }}</p>
<p><strong>Date:</strong> {{ $order->created_at }}</p>

<hr>

<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <th>Product</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->items as $item)
        <tr>
            <td>{{ $item->product_name }}</td>
            <td>{{ $item->qty }}</td>
            <td>{{ $item->price }}</td>
            <td>{{ $item->qty * $item->price }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<hr>

<p><strong>Total:</strong> {{ $order->total_price }}</p>
<p><strong>Paid:</strong> {{ $order->paid_amount }}</p>
<p><strong>Due:</strong> {{ $order->due_amount }}</p>

@if($order->payment_method == 'credit')
    <p><strong>Due Date:</strong> {{ $order->due_date }}</p>
@endif

<hr>

<h4>Payment History</h4>

<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <tr>
        <th>Date</th>
        <th>Amount</th>
        <th>Method</th>
    </tr>

    @foreach($order->payments as $payment)
    <tr>
        <td>{{ $payment->payment_date }}</td>
        <td>{{ $payment->amount }}</td>
        <td>{{ $payment->method }}</td>
    </tr>
    @endforeach
</table>
```

---

# 📊 3. AGING REPORT (VERY IMPORTANT FEATURE)

This is what businesses really want.

---

## 👉 Controller

```php
public function agingReport()
{
    $orders = Order::where('due_amount', '>', 0)->get();

    $aging = [
        '0_30' => 0,
        '31_60' => 0,
        '61_90' => 0,
        '90_plus' => 0,
    ];

    foreach ($orders as $order) {

        if (!$order->due_date) continue;

        $days = now()->diffInDays($order->due_date, false);

        if ($days >= 0 && $days <= 30) {
            $aging['0_30'] += $order->due_amount;
        } elseif ($days >= 31 && $days <= 60) {
            $aging['31_60'] += $order->due_amount;
        } elseif ($days >= 61 && $days <= 90) {
            $aging['61_90'] += $order->due_amount;
        } else {
            $aging['90_plus'] += $order->due_amount;
        }
    }

    return view('reports.aging', compact('aging', 'orders'));
}
```

---

## 👉 Blade: `reports/aging.blade.php`

```html
<h3>Aging Report</h3>

<table class="table table-bordered">
    <tr>
        <th>0 - 30 Days</th>
        <th>31 - 60 Days</th>
        <th>61 - 90 Days</th>
        <th>90+ Days</th>
    </tr>
    <tr>
        <td>{{ $aging['0_30'] }}</td>
        <td>{{ $aging['31_60'] }}</td>
        <td>{{ $aging['61_90'] }}</td>
        <td>{{ $aging['90_plus'] }}</td>
    </tr>
</table>

<hr>

<h4>Detailed List</h4>

<table class="table">
    <tr>
        <th>Order</th>
        <th>Customer</th>
        <th>Due</th>
        <th>Due Date</th>
    </tr>

    @foreach($orders as $order)
    <tr>
        <td>#{{ $order->id }}</td>
        <td>{{ $order->customer_name }}</td>
        <td>{{ $order->due_amount }}</td>
        <td>{{ $order->due_date }}</td>
    </tr>
    @endforeach
</table>
```

---

# 🔥 Final Result (What You Achieved)

You now have:

✅ POS supports **credit sales**
✅ Tracks **partial + multiple payments**
✅ Generates **professional invoices with due tracking**
✅ Shows **real business metric (aging report)**

---

# 🚀 If You Want to Go Further

Next logical upgrades:

* Auto reminders (WhatsApp/email before due date)
* Customer credit limit blocking
* Dashboard: “Total Receivable Today”

Just tell me and I’ll extend this into a **production-grade POS system**.

/////////////////========================================================
These are exactly the right questions—this is where a simple “credit option” turns into real accounting behavior. Let’s clarify each part properly so your system behaves like a real business tool.

---

# 📅 1. What is “Due Date”?

**Due date = the deadline by which the customer is expected to fully pay the remaining amount.**

Example:

```text
Order Total: ₹10,000
Paid Today: ₹2,000
Due: ₹8,000
Due Date: 30 April

👉 Customer should clear ₹8,000 by 30 April
```

---

# ❗ 2. What if customer does NOT pay by due date?

Nothing “automatic” happens unless you define rules.

But in real systems:

### You should mark it as:

* **Overdue**

### Logic:

```php
if ($order->due_date < now() && $order->due_amount > 0) {
    // overdue
}
```

### UI:

Show:

```html
<span class="badge bg-danger">Overdue</span>
```

---

# 🔁 3. Can Due Date be changed?

👉 Yes — and you SHOULD allow it.

Because in real business:

* Customer asks for extension
* Payment delayed

---

## Add Update Due Date Feature

### Route

```php
Route::post('/orders/{id}/update-due-date', [OrderController::class, 'updateDueDate']);
```

---

### Controller

```php
public function updateDueDate(Request $request, $id)
{
    $order = Order::findOrFail($id);

    $order->due_date = $request->due_date;
    $order->save();

    return back()->with('success', 'Due date updated');
}
```

---

# 💸 4. If Customer Pays in 10 Installments?

This is EXACTLY why we created the `order_payments` table.

👉 Each payment = one row

---

## Example

| Payment # | Amount | Date   |
| --------- | ------ | ------ |
| 1         | 1000   | Jan 1  |
| 2         | 500    | Jan 5  |
| 3         | 2000   | Jan 10 |
| ...       | ...    | ...    |

---

## How It Is Stored

Each time:

```php
OrderPayment::create([
    'order_id' => $order->id,
    'customer_id' => $order->customer_id,
    'amount' => $request->amount,
    'method' => $request->method,
    'payment_date' => now()
]);
```

---

## Important:

You DO NOT overwrite payments.

👉 You APPEND them.

That’s your full history.

---

# 🧠 5. How System Tracks Everything Automatically

Every time payment is added:

```php
$order->paid_amount += $amount;
$order->due_amount -= $amount;
$order->last_payment_at = now();
```

---

# 📊 6. Example Full Scenario

```text
Order = ₹10,000
Due Date = 30 April
```

### Payments:

* ₹2,000 → April 1
* ₹3,000 → April 10
* ₹2,000 → May 5 (late)
* ₹3,000 → May 20

---

### System Result:

* Paid: ₹10,000
* Due: ₹0
* Status: Paid
* BUT:

  * Was overdue between April 30 → May 20

---

# 🔥 7. Best Practice (Important Upgrade)

Add **payment log view** in UI:

```html
<h5>Payment History</h5>
<table>
<tr>
    <th>Date</th>
    <th>Amount</th>
</tr>

@foreach($order->payments as $payment)
<tr>
    <td>{{ $payment->payment_date }}</td>
    <td>{{ $payment->amount }}</td>
</tr>
@endforeach
</table>
```

---

# ⚠️ 8. Business Rules You Should Decide

You should confirm with client:

### 1. After due date:

* Allow payment? ✅ (usually yes)
* Add penalty? ❓

### 2. Allow editing due date?

* Yes / No

### 3. Minimum payment required?

* Optional

---

# 🧩 Final Understanding

👉 Due Date = **expected deadline**
👉 Payments = **multiple entries (installments)**
👉 Overdue = **due date passed + still unpaid**
👉 System = **keeps full history, never overwrite**

---

# 🚀 If You Want Next Level

I can help you add:

* Automatic **late fee calculation**
* WhatsApp reminders before due date
* Customer-wise credit summary dashboard
* “Block new sale if old dues exist”

Just tell me 👍

/////////////////========================================================
/////////////////========================================================
/////////////////========================================================
/////////////////========================================================
/////////////////========================================================
/////////////////========================================================


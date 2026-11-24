<?php

/**
 * Default GL account codes (must exist in gl_accounts — seeded by migration).
 * Map operational events to chart-of-accounts codes from stockmgnt.sql.
 */
return [
    'accounts' => [
        'cash' => '1000',
        'accounts_receivable' => '1100',
        'inventory' => '1200',
        'accounts_payable' => '2000',
        'taxation_payable' => '2200',
        'sales_revenue' => '4000',
        'cogs' => '5000',
    ],

    /** Skip automatic GL for estimate orders (invoice status 3). */
    'skip_estimate_orders' => true,
];

<?php

namespace App\Services;

use App\Models\Order;
use App\Models\SupplierPurchaseInvoice;

class DraftReferenceService
{
    public function assignForOrder(Order $order): string
    {
        $ref = 'DRAFT-S' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT);
        $order->draft_reference = $ref;
        $order->invoice_number = null;

        return $ref;
    }

    public function assignForPurchaseInvoice(SupplierPurchaseInvoice $invoice): string
    {
        $ref = 'DRAFT-P' . str_pad((string) $invoice->id, 6, '0', STR_PAD_LEFT);
        $invoice->draft_reference = $ref;
        $invoice->internal_invoice_number = null;
        // Old: cleared supplier reference on hold too.
        // $invoice->invoice_number = null;

        return $ref;
    }

    public function assignForBatchPurchase(\App\Models\BatchPurchase $batch): string
    {
        $ref = 'DRAFT-B' . str_pad((string) $batch->id, 6, '0', STR_PAD_LEFT);
        $batch->draft_reference = $ref;

        return $ref;
    }
}

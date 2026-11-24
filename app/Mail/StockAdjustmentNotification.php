<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StockAdjustmentNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $adjustment;
    public $store;
    public $stock;
    public $performedBy;

    public function __construct($adjustment, $store, $stock, $performedBy)
    {
        $this->adjustment = $adjustment;
        $this->store = $store;
        $this->stock = $stock;
        $this->performedBy = $performedBy;
    }

    public function build()
    {
        return $this->subject('Stock Adjustment Notification')
                    ->view('emails.stock_adjustment_notification');
    }
}

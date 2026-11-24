<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StockTransferStatusNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $transfer;
    public $items;
    public $storeFrom;
    public $storeTo;
    public $statusText;

    public function __construct($transfer, $items, $storeFrom, $storeTo, $statusText)
    {
        $this->transfer = $transfer;
        $this->items = $items;
        $this->storeFrom = $storeFrom;
        $this->storeTo = $storeTo;
        $this->statusText = $statusText;
    }

    public function build()
    {
        return $this->subject('Stock Transfer Status Updated')
                    ->view('emails.stock_transfer_status_notification');
    }
}

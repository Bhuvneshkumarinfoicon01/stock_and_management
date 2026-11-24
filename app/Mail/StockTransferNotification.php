<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StockTransferNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $transfer;
    public $items;
    public $store_from;
    public $store_to;

    public function __construct($transfer, $items, $storeFrom, $storeTo)
    {
        $this->transfer = $transfer;
        $this->items = $items;
        $this->store_from = $storeFrom;
        $this->store_to = $storeTo;
    }

    public function build()
    {
        return $this
            ->subject('New Stock Transfer Notification')
            ->view('emails.stock_transfer_notification');
    }
}

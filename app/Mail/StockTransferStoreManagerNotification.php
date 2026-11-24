<?php

namespace App\Mail;

use App\Models\StockTransfer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StockTransferStoreManagerNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $transfer,
        $items,
        $storeFrom,
        $storeTo,
        $direction;

    public function __construct($transfer, $items, $storeFrom, $storeTo, $direction)
    {
        $this->transfer = $transfer;
        $this->items = $items;
        $this->storeFrom = $storeFrom;
        $this->storeTo = $storeTo;
        $this->direction = $direction;  // 'incoming' or 'outgoing'
    }

    public function build()
    {
        $subject = $this->direction === 'incoming'
            ? 'Incoming Stock Transfer Notification'
            : 'Outgoing Stock Transfer Confirmation';

        return $this
            ->subject($subject)
            ->view('emails.store-manager-stock-transfer');
    }
}

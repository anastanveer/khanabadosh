<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class OrderPlacedCustomer extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
        $this->order->loadMissing('items');
    }

    public function build(): self
    {
        return $this->subject('Order ' . $this->order->order_number . ' confirmed')
            ->view('emails.orders.placed-customer')
            ->with([
                'title' => 'Order confirmed',
                'subtitle' => 'Thanks for shopping Khanabadosh Fashion.',
                'preheader' => 'We received your order ' . $this->order->order_number . '.',
                'statusLabel' => Str::headline($this->order->status),
            ]);
    }
}

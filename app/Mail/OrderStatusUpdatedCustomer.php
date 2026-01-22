<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class OrderStatusUpdatedCustomer extends Mailable
{
    use Queueable, SerializesModels;

    public string $statusLabel;
    public string $previousStatusLabel;

    public function __construct(public Order $order, public string $previousStatus)
    {
        $this->order->loadMissing('items');
        $this->statusLabel = Str::headline($order->status);
        $this->previousStatusLabel = Str::headline($previousStatus);
    }

    public function build(): self
    {
        $subject = 'Order ' . $this->order->order_number . ' is now ' . $this->statusLabel;

        return $this->subject($subject)
            ->view('emails.orders.status-customer')
            ->with([
                'title' => 'Order update',
                'subtitle' => 'Your order status has changed to ' . $this->statusLabel . '.',
                'preheader' => $subject,
                'statusLabel' => $this->statusLabel,
                'previousStatusLabel' => $this->previousStatusLabel,
            ]);
    }
}

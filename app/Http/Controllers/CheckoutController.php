<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Mail\OrderPlacedCustomer;
use App\Support\CurrencyFormatter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function place(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_name' => 'required|string|max:120',
            'email' => 'required|email|max:120',
            'phone' => 'required|string|max:40',
            'city' => 'required|string|max:80',
            'address' => 'required|string|max:200',
            'postal_code' => 'nullable|string|max:20',
            'delivery_method' => 'required|string|in:standard,express',
            'payment_method' => 'required|string|in:cod,card,bank',
            'notes' => 'nullable|string|max:500',
            'cart_payload' => 'required|string',
            'card_name' => 'nullable|string|max:120',
            'card_number' => 'nullable|string|max:30',
            'card_expiry' => 'nullable|string|max:10',
            'card_cvc' => 'nullable|string|max:6',
            'bank_name' => 'nullable|string|max:120',
            'bank_account' => 'nullable|string|max:60',
            'bank_reference' => 'nullable|string|max:80',
            'payment_proof' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        if ($data['payment_method'] === 'card') {
            $request->validate([
                'card_name' => 'required|string|max:120',
                'card_number' => 'required|string|max:30',
                'card_expiry' => 'required|string|max:10',
                'card_cvc' => 'required|string|max:6',
            ]);
        }

        if ($data['payment_method'] === 'bank') {
            $request->validate([
                'bank_name' => 'required|string|max:120',
                'bank_account' => 'required|string|max:60',
                'bank_reference' => 'required|string|max:80',
                'payment_proof' => 'required|file|mimes:jpg,jpeg,png,webp|max:4096',
            ]);
        }

        $payload = json_decode($data['cart_payload'], true);
        if (!is_array($payload)) {
            return back()
                ->withErrors(['cart_payload' => 'Cart data is missing or invalid.'])
                ->withInput();
        }

        $counts = collect($payload)
            ->mapWithKeys(function ($qty, $handle) {
                $cleanHandle = trim((string) $handle);
                $quantity = (int) $qty;
                if ($cleanHandle === '' || $quantity <= 0) {
                    return [];
                }
                return [$cleanHandle => $quantity];
            });

        if ($counts->isEmpty()) {
            return back()
                ->withErrors(['cart_payload' => 'Your cart is empty.'])
                ->withInput();
        }

        $products = Product::query()
            ->whereIn('handle', $counts->keys()->all())
            ->get()
            ->keyBy('handle');

        if ($products->isEmpty()) {
            return back()
                ->withErrors(['cart_payload' => 'No products found for this order.'])
                ->withInput();
        }

        $paymentDetails = $this->buildPaymentDetails($data, $data['payment_method']);
        $paymentProofPath = null;
        if ($data['payment_method'] === 'bank' && $request->hasFile('payment_proof')) {
            $paymentProofPath = $request->file('payment_proof')->store('payment-proofs', 'public');
        }

        $order = DB::transaction(function () use ($data, $counts, $products, $paymentDetails, $paymentProofPath) {
            $subtotal = 0;
            $itemsCount = 0;
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'status' => 'pending',
                'customer_name' => $data['customer_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'city' => $data['city'],
                'address' => $data['address'],
                'postal_code' => $data['postal_code'] ?? null,
                'delivery_method' => $data['delivery_method'],
                'payment_method' => $data['payment_method'],
                'payment_details' => $paymentDetails,
                'payment_proof_path' => $paymentProofPath,
                'currency' => CurrencyFormatter::currency(),
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($counts as $handle => $qty) {
                $product = $products->get($handle);
                if (!$product) {
                    continue;
                }
                $price = $product->effectivePrice() ?? 0;
                $lineTotal = round($price * $qty, 2);
                $subtotal += $lineTotal;
                $itemsCount += $qty;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_handle' => $handle,
                    'title' => $product->title,
                    'price' => $price,
                    'quantity' => $qty,
                    'line_total' => $lineTotal,
                ]);
            }

            if ($itemsCount === 0) {
                throw new \RuntimeException('Order contains no items.');
            }

            $order->update([
                'subtotal' => $subtotal,
                'total' => $subtotal,
                'items_count' => $itemsCount,
            ]);

            return $order;
        });

        $notificationEmails = array_values(array_filter(
            (array) config('khanabadosh.order_notification_emails', [])
        ));

        $mailToCustomer = Mail::to($order->email);
        if ($notificationEmails) {
            $mailToCustomer->bcc($notificationEmails);
        }
        $mailToCustomer->send(new OrderPlacedCustomer($order));

        return redirect()->route('checkout.success', ['orderNumber' => $order->order_number]);
    }

    public function success(string $orderNumber): View
    {
        $order = Order::query()
            ->with('items')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('checkout-success', [
            'pageTitle' => 'Order Confirmed',
            'order' => $order,
        ]);
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = 'KB' . now()->format('ymd') . strtoupper(Str::random(4));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }

    private function buildPaymentDetails(array $data, string $method): array
    {
        if ($method === 'card') {
            $digits = preg_replace('/\D+/', '', (string) ($data['card_number'] ?? ''));
            $last4 = $digits ? substr($digits, -4) : null;

            return array_filter([
                'card_name' => $data['card_name'] ?? null,
                'card_last4' => $last4,
                'card_expiry' => $data['card_expiry'] ?? null,
                'card_brand' => $this->cardBrand($digits),
            ]);
        }

        if ($method === 'bank') {
            return array_filter([
                'bank_name' => $data['bank_name'] ?? null,
                'bank_account' => $data['bank_account'] ?? null,
                'bank_reference' => $data['bank_reference'] ?? null,
            ]);
        }

        return [];
    }

    private function cardBrand(string $digits): ?string
    {
        if ($digits === '') {
            return null;
        }
        if (str_starts_with($digits, '4')) {
            return 'Visa';
        }
        if (preg_match('/^(5[1-5])/', $digits)) {
            return 'Mastercard';
        }
        if (preg_match('/^(34|37)/', $digits)) {
            return 'Amex';
        }
        return 'Card';
    }
}

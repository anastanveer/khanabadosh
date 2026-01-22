<?php

namespace App\Http\Controllers;

use App\Mail\OrderStatusUpdatedCustomer;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class AdminOrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::query()
            ->latest()
            ->take(100)
            ->get();

        return view('admin.orders.index', [
            'pageTitle' => 'Orders',
            'orders' => $orders,
        ]);
    }

    public function show(Order $order): View
    {
        $order->load('items.product.images');

        return view('admin.orders.show', [
            'pageTitle' => 'Order ' . $order->order_number,
            'order' => $order,
        ]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => 'required|string|in:pending,approved,shipped,delivered,cancelled,refunded',
        ]);

        $previousStatus = $order->status;
        $order->update([
            'status' => $data['status'],
        ]);

        if ($previousStatus !== $data['status']) {
            $notificationEmails = array_values(array_filter(
                (array) config('khanabadosh.order_notification_emails', [])
            ));

            $mailToCustomer = Mail::to($order->email);
            if ($notificationEmails) {
                $mailToCustomer->bcc($notificationEmails);
            }
            $mailToCustomer->send(new OrderStatusUpdatedCustomer($order, $previousStatus));
        }

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('status', 'Order status updated.');
    }

    public function destroy(Order $order): RedirectResponse
    {
        if ($order->payment_proof_path) {
            Storage::disk('public')->delete($order->payment_proof_path);
        }

        $order->delete();

        return redirect()
            ->route('admin.orders.index')
            ->with('status', 'Order deleted.');
    }
}

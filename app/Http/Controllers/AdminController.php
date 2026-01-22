<?php

namespace App\Http\Controllers;

use App\Services\KhanabadoshSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $products = \App\Models\Product::with(['images', 'variants', 'collections'])->get();
        $collections = \App\Models\Collection::query()
            ->with(['products.variants'])
            ->withCount('products')
            ->orderBy('title')
            ->get();
        $variants = $products->pluck('variants')->flatten();

        $totalStock = $variants->sum(fn ($variant) => (int) ($variant->inventory_quantity ?? 0));
        $inStockProducts = $products->filter(function ($product) {
            $stock = $product->variants->sum('inventory_quantity');
            return $stock > 0 || $product->available;
        })->count();
        $outOfStockProducts = max($products->count() - $inStockProducts, 0);
        $discountedProducts = $products->filter->hasActiveDiscount()->count();
        $lowStockProducts = $products->filter(function ($product) {
            $stock = $product->variants->sum('inventory_quantity');
            return $stock > 0 && $stock <= 5;
        })->count();
        $potentialRevenue = $variants->sum(function ($variant) {
            $price = $variant->price ?? $variant->product?->price ?? 0;
            return (float) $price * (int) ($variant->inventory_quantity ?? 0);
        });

        $collectionStats = $collections->map(function ($collection) {
            $stock = $collection->products->sum(function ($product) {
                return (int) $product->variants->sum('inventory_quantity');
            });

            return [
                'title' => $collection->title,
                'count' => $collection->products_count,
                'stock' => $stock,
            ];
        });

        $topCollections = $collectionStats->sortByDesc('count')->take(8)->values();
        $topStockCollections = $collectionStats->sortByDesc('stock')->take(8)->values();

        $topProducts = $products->sortByDesc(function ($product) {
            return (int) $product->variants->sum('inventory_quantity');
        })->take(5)->values();

        $ordersCount = \App\Models\Order::query()->count();
        $recentOrders = \App\Models\Order::query()
            ->latest()
            ->take(5)
            ->get();
        $ordersRevenue = (float) \App\Models\Order::query()->sum('total');
        $ordersToday = \App\Models\Order::query()
            ->whereDate('created_at', now()->toDateString())
            ->count();
        $ordersByStatus = \App\Models\Order::query()
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $currency = \App\Models\Setting::getValue('currency', 'PKR');
        $cadRate = \App\Support\CurrencyFormatter::normalizedRate();
        $liveCadRate = $cadRate;
        try {
            $response = Http::timeout(6)
                ->retry(1, 200)
                ->get('https://api.exchangerate.host/latest', [
                    'base' => 'PKR',
                    'symbols' => 'CAD',
                ])
                ->throw();
            $rate = (float) ($response->json('rates.CAD') ?? 0);
            if ($rate > 0) {
                $liveCadRate = $rate;
                $cadRate = $rate;
                \App\Models\Setting::setValue('cad_rate', $rate);
            }
        } catch (\Throwable $exception) {
        }
        $bankName = \App\Models\Setting::getValue('bank_name', 'Khanabadosh Bank');
        $bankTitle = \App\Models\Setting::getValue('bank_account_title', 'Khanabadosh Fashion');
        $bankAccount = \App\Models\Setting::getValue('bank_account_number', '0001-2233-4455');
        $bankIban = \App\Models\Setting::getValue('bank_iban', 'PK00KB0000000000000001');
        $bankNote = \App\Models\Setting::getValue('bank_note', 'Send payment to the bank account and upload the transfer screenshot.');

        return view('admin.index', [
            'pageTitle' => 'Admin Dashboard',
            'summary' => session('summary'),
            'status' => session('status'),
            'products' => $products,
            'collections' => $collections,
            'totalProducts' => $products->count(),
            'totalCollections' => $collections->count(),
            'totalVariants' => $variants->count(),
            'totalStock' => $totalStock,
            'inStockProducts' => $inStockProducts,
            'outOfStockProducts' => $outOfStockProducts,
            'discountedProducts' => $discountedProducts,
            'lowStockProducts' => $lowStockProducts,
            'potentialRevenue' => $potentialRevenue,
            'topCollections' => $topCollections,
            'topStockCollections' => $topStockCollections,
            'topProducts' => $topProducts,
            'ordersCount' => $ordersCount,
            'recentOrders' => $recentOrders,
            'ordersRevenue' => $ordersRevenue,
            'ordersToday' => $ordersToday,
            'ordersByStatus' => $ordersByStatus,
            'currency' => $currency,
            'cadRate' => $cadRate,
            'liveCadRate' => $liveCadRate,
            'bankName' => $bankName,
            'bankTitle' => $bankTitle,
            'bankAccount' => $bankAccount,
            'bankIban' => $bankIban,
            'bankNote' => $bankNote,
        ]);
    }

    public function sync(KhanabadoshSyncService $service): RedirectResponse
    {
        $summary = $service->sync();

        return redirect()
            ->route('admin.index')
            ->with('status', 'Data sync completed successfully.')
            ->with('summary', $summary);
    }

    public function updatePopular(Request $request): RedirectResponse
    {
        $ids = $request->input('popular', []);

        \App\Models\Product::query()->update(['is_popular' => false]);
        if (!empty($ids)) {
            \App\Models\Product::whereIn('id', $ids)->update(['is_popular' => true]);
        }

        return redirect()
            ->route('admin.index')
            ->with('status', 'Popular products updated.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Support\CurrencyFormatter;

class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->with(['images', 'variants', 'collections']);
        $search = $request->query('q');
        if ($search) {
            $query->where('title', 'like', '%' . $search . '%')
                ->orWhere('handle', 'like', '%' . $search . '%')
                ->orWhere('tags', 'like', '%' . $search . '%');
        }

        return view('admin.products.index', [
            'pageTitle' => 'Manage Products',
            'products' => $query->orderBy('title')->get(),
            'collections' => Collection::query()->orderBy('title')->get(),
            'search' => $search,
        ]);
    }

    public function create()
    {
        return view('admin.products.create', [
            'pageTitle' => 'Add Product',
            'collections' => Collection::query()->orderBy('title')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateProduct($request);
        $handle = $data['handle'] ?: Str::slug($data['title']);
        $discountType = $data['discount_type'] ?: null;
        $discountValue = $discountType ? $data['discount_value'] : null;
        $discountStarts = $discountType ? $data['discount_starts_at'] : null;
        $discountEnds = $discountType ? $data['discount_ends_at'] : null;
        $priceBase = CurrencyFormatter::toBase($data['price']);
        $compareBase = CurrencyFormatter::toBase($data['compare_at_price']);
        $discountValueBase = $discountType === 'fixed'
            ? CurrencyFormatter::toBase($discountValue)
            : $discountValue;

        $product = Product::create([
            'shopify_id' => $this->generateUniqueId(Product::class, 'shopify_id'),
            'title' => $data['title'],
            'handle' => $handle,
            'body_html' => $data['body_html'],
            'product_type' => $data['product_type'],
            'vendor' => $data['vendor'],
            'tags' => $data['tags'],
            'price' => $priceBase,
            'compare_at_price' => $compareBase,
            'discount_type' => $discountType,
            'discount_value' => $discountValueBase,
            'discount_starts_at' => $discountStarts,
            'discount_ends_at' => $discountEnds,
            'available' => (bool) ($data['available'] ?? false),
            'source_created_at' => now(),
            'source_updated_at' => now(),
        ]);

        $product->variants()->create([
            'shopify_id' => $this->generateUniqueId(ProductVariant::class, 'shopify_id'),
            'title' => 'Default',
            'sku' => $data['sku'],
            'price' => $priceBase,
            'compare_at_price' => $compareBase,
            'inventory_quantity' => $data['inventory_quantity'],
            'position' => 1,
            'available' => (bool) ($data['available'] ?? false),
        ]);

        if (!empty($data['image_url'])) {
            $product->images()->create([
                'shopify_id' => $this->generateUniqueId(ProductImage::class, 'shopify_id'),
                'src' => $data['image_url'],
                'position' => 1,
            ]);
        }

        if (!empty($data['collections'])) {
            $product->collections()->sync($data['collections']);
        }

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('status', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $product->load(['images', 'variants', 'collections']);

        return view('admin.products.edit', [
            'pageTitle' => 'Edit Product',
            'product' => $product,
            'collections' => Collection::query()->orderBy('title')->get(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validateProduct($request, $product->id);
        $discountType = $data['discount_type'] ?: null;
        $discountValue = $discountType ? $data['discount_value'] : null;
        $discountStarts = $discountType ? $data['discount_starts_at'] : null;
        $discountEnds = $discountType ? $data['discount_ends_at'] : null;
        $priceBase = CurrencyFormatter::toBase($data['price']);
        $compareBase = CurrencyFormatter::toBase($data['compare_at_price']);
        $discountValueBase = $discountType === 'fixed'
            ? CurrencyFormatter::toBase($discountValue)
            : $discountValue;

        $product->update([
            'title' => $data['title'],
            'handle' => $data['handle'] ?: $product->handle,
            'body_html' => $data['body_html'],
            'product_type' => $data['product_type'],
            'vendor' => $data['vendor'],
            'tags' => $data['tags'],
            'price' => $priceBase,
            'compare_at_price' => $compareBase,
            'discount_type' => $discountType,
            'discount_value' => $discountValueBase,
            'discount_starts_at' => $discountStarts,
            'discount_ends_at' => $discountEnds,
            'available' => (bool) ($data['available'] ?? false),
        ]);

        if (!empty($data['collections'])) {
            $product->collections()->sync($data['collections']);
        } else {
            $product->collections()->sync([]);
        }

        if (!empty($data['image_url'])) {
            $image = $product->images()->orderBy('position')->first();
            if ($image) {
                $image->update(['src' => $data['image_url']]);
            } else {
                $product->images()->create([
                    'shopify_id' => $this->generateUniqueId(ProductImage::class, 'shopify_id'),
                    'src' => $data['image_url'],
                    'position' => 1,
                ]);
            }
        }

        $defaultVariant = $product->variants()->orderBy('position')->first();
        if ($defaultVariant) {
            $defaultVariant->update([
                'price' => $priceBase,
                'compare_at_price' => $compareBase,
                'inventory_quantity' => $data['inventory_quantity'],
                'available' => (bool) ($data['available'] ?? false),
            ]);
        }

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('status', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Product deleted successfully.');
    }

    public function storeVariant(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'inventory_quantity' => 'nullable|integer|min:0',
            'available' => 'nullable|boolean',
        ]);

        $product->variants()->create([
            'shopify_id' => $this->generateUniqueId(ProductVariant::class, 'shopify_id'),
            'title' => $data['title'],
            'sku' => $data['sku'] ?? null,
            'price' => CurrencyFormatter::toBase($data['price'] ?? null),
            'compare_at_price' => CurrencyFormatter::toBase($data['compare_at_price'] ?? null),
            'inventory_quantity' => $data['inventory_quantity'] ?? null,
            'position' => $product->variants()->count() + 1,
            'available' => (bool) ($data['available'] ?? false),
        ]);

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('status', 'Variant added successfully.');
    }

    public function updateVariant(Request $request, ProductVariant $variant): RedirectResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'inventory_quantity' => 'nullable|integer|min:0',
            'available' => 'nullable|boolean',
        ]);

        $variant->update([
            'title' => $data['title'],
            'sku' => $data['sku'] ?? null,
            'price' => CurrencyFormatter::toBase($data['price'] ?? null),
            'compare_at_price' => CurrencyFormatter::toBase($data['compare_at_price'] ?? null),
            'inventory_quantity' => $data['inventory_quantity'] ?? null,
            'available' => (bool) ($data['available'] ?? false),
        ]);

        return redirect()
            ->route('admin.products.edit', $variant->product_id)
            ->with('status', 'Variant updated successfully.');
    }

    public function destroyVariant(ProductVariant $variant): RedirectResponse
    {
        $productId = $variant->product_id;
        $variant->delete();

        return redirect()
            ->route('admin.products.edit', $productId)
            ->with('status', 'Variant deleted successfully.');
    }

    private function validateProduct(Request $request, ?int $productId = null): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'handle' => 'nullable|string|max:255|unique:products,handle,' . ($productId ?? 'NULL') . ',id',
            'body_html' => 'nullable|string',
            'product_type' => 'nullable|string|max:255',
            'vendor' => 'nullable|string|max:255',
            'tags' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|in:percent,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'discount_starts_at' => 'nullable|date',
            'discount_ends_at' => 'nullable|date|after_or_equal:discount_starts_at',
            'available' => 'nullable|boolean',
            'inventory_quantity' => 'nullable|integer|min:0',
            'sku' => 'nullable|string|max:255',
            'image_url' => 'nullable|url',
            'collections' => 'nullable|array',
            'collections.*' => 'integer|exists:collections,id',
        ]);
    }

    private function generateUniqueId(string $modelClass, string $column): int
    {
        do {
            $id = random_int(9000000000000, 9999999999999);
        } while ($modelClass::query()->where($column, $id)->exists());

        return $id;
    }
}

<?php

namespace App\Support;

use App\Models\Product;

class ProductBadge
{
    public static function resolve($product, $compareValue = null): ?array
    {
        $tags = self::normalizeTags(self::valueFrom($product, ['tags']));

        if (self::isSale($product, $compareValue, $tags)) {
            return [
                'label' => 'Sale',
                'class' => 'kb-badge kb-badge--sale',
            ];
        }

        if (self::isNew($tags)) {
            return [
                'label' => 'New',
                'class' => 'kb-badge kb-badge--new',
            ];
        }

        return null;
    }

    private static function isSale($product, $compareValue, array $tags): bool
    {
        if ($compareValue !== null) {
            return true;
        }

        if ($product instanceof Product) {
            if ($product->hasActiveDiscount()) {
                return true;
            }

            if ($product->compare_at_price && $product->price && $product->compare_at_price > $product->price) {
                return true;
            }
        } else {
            $price = self::numberFrom($product, ['price', 'price_value']);
            $compare = self::numberFrom($product, ['compare_at_price', 'compareAtPrice']);

            if ($compare !== null && $price !== null && $compare > $price) {
                return true;
            }

            $discountType = self::valueFrom($product, ['discount_type', 'discountType']);
            $discountValue = self::numberFrom($product, ['discount_value', 'discountValue']);
            if ($discountType && $discountValue !== null && $discountValue > 0) {
                return true;
            }
        }

        return in_array('sale', $tags, true);
    }

    private static function isNew(array $tags): bool
    {
        return in_array('new', $tags, true)
            || in_array('new arrival', $tags, true)
            || in_array('new arrivals', $tags, true)
            || in_array('new-arrival', $tags, true)
            || in_array('new-arrivals', $tags, true);
    }

    private static function normalizeTags($rawTags): array
    {
        if (is_array($rawTags)) {
            $rawTags = implode(',', $rawTags);
        }

        return collect(explode(',', (string) $rawTags))
            ->map(fn ($tag) => strtolower(trim($tag)))
            ->filter()
            ->values()
            ->all();
    }

    private static function valueFrom($product, array $keys)
    {
        foreach ($keys as $key) {
            $value = data_get($product, $key);
            if ($value !== null) {
                return $value;
            }
        }

        return null;
    }

    private static function numberFrom($product, array $keys): ?float
    {
        $value = self::valueFrom($product, $keys);
        if ($value === null) {
            return null;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        if (is_string($value)) {
            $cleaned = preg_replace('/[^\d.]/', '', $value);
            return $cleaned === '' ? null : (float) $cleaned;
        }

        return null;
    }
}

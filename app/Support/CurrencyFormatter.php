<?php

namespace App\Support;

use App\Models\Setting;

class CurrencyFormatter
{
    public static function currency(): string
    {
        $currency = Setting::getValue('currency', 'PKR');

        return strtoupper($currency) === 'CAD' ? 'CAD' : 'PKR';
    }

    public static function rate(): float
    {
        return self::normalizedRate();
    }

    public static function convert(?float $amount): ?float
    {
        if ($amount === null) {
            return null;
        }

        if (self::currency() === 'CAD') {
            return round($amount * self::normalizedRate(), 2);
        }

        return round($amount, 2);
    }

    public static function toBase(?float $amount): ?float
    {
        if ($amount === null) {
            return null;
        }

        if (self::currency() === 'CAD') {
            $rate = self::normalizedRate();
            if ($rate <= 0) {
                return round($amount, 2);
            }

            return round($amount / $rate, 2);
        }

        return round($amount, 2);
    }

    public static function normalizedRate(): float
    {
        $rate = (float) Setting::getValue('cad_rate', 0);
        if ($rate <= 0) {
            return 1.0;
        }

        if ($rate > 10) {
            return round(1 / $rate, 6);
        }

        return $rate;
    }

    public static function symbol(): string
    {
        return self::currency() === 'CAD' ? 'C$' : 'Rs.';
    }

    public static function format(?float $amount): string
    {
        $value = self::convert($amount);
        $value = $value === null ? 0 : $value;

        return self::symbol() . ' ' . number_format($value, 2);
    }
}

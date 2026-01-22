<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AdminSettingsController extends Controller
{
    public function updateCurrency(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'currency' => 'required|in:PKR,CAD',
            'cad_rate' => 'nullable|numeric|min:0',
        ]);

        Setting::setValue('currency', $data['currency']);
        if ($data['currency'] === 'CAD') {
            $rate = $data['cad_rate'];
            if ($rate === null) {
                $rate = Setting::getValue('cad_rate', 1);
            }
            Setting::setValue('cad_rate', $rate);
        }

        return redirect()
            ->route('admin.index')
            ->with('status', 'Currency settings updated.');
    }

    public function fetchLiveRate(): RedirectResponse
    {
        try {
            $response = Http::timeout(8)
                ->retry(2, 200)
                ->get('https://api.exchangerate.host/latest', [
                    'base' => 'PKR',
                    'symbols' => 'CAD',
                ])
                ->throw();

            $rate = (float) ($response->json('rates.CAD') ?? 0);
            if ($rate <= 0) {
                Setting::setValue('currency', 'CAD');
                return redirect()
                    ->route('admin.index')
                    ->with('status', 'Live rate unavailable. Currency switched to CAD using saved rate.');
            }

            Setting::setValue('cad_rate', $rate);
            Setting::setValue('currency', 'CAD');

            return redirect()
                ->route('admin.index')
                ->with('status', 'Live CAD rate updated and currency switched to CAD.');
        } catch (\Throwable $exception) {
            Setting::setValue('currency', 'CAD');
            return redirect()
                ->route('admin.index')
                ->with('status', 'Live rate fetch failed. Currency switched to CAD using saved rate.');
        }
    }

    public function liveRatePreview(): JsonResponse
    {
        try {
            $response = Http::timeout(6)
                ->retry(1, 200)
                ->get('https://api.exchangerate.host/latest', [
                    'base' => 'PKR',
                    'symbols' => 'CAD',
                ])
                ->throw();

            $rate = (float) ($response->json('rates.CAD') ?? 0);
            if ($rate <= 0) {
                return response()->json(['ok' => false]);
            }

            return response()->json([
                'ok' => true,
                'rate' => $rate,
            ]);
        } catch (\Throwable $exception) {
            return response()->json(['ok' => false]);
        }
    }

    public function updateBankDetails(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'bank_name' => 'required|string|max:120',
            'bank_account_title' => 'required|string|max:120',
            'bank_account_number' => 'required|string|max:80',
            'bank_iban' => 'required|string|max:80',
            'bank_note' => 'nullable|string|max:255',
        ]);

        Setting::setValue('bank_name', $data['bank_name']);
        Setting::setValue('bank_account_title', $data['bank_account_title']);
        Setting::setValue('bank_account_number', $data['bank_account_number']);
        Setting::setValue('bank_iban', $data['bank_iban']);
        Setting::setValue('bank_note', $data['bank_note'] ?? '');

        return redirect()
            ->route('admin.index')
            ->with('status', 'Bank details updated.');
    }
}

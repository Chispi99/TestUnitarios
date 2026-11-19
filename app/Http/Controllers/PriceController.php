<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class PriceController extends Controller
{
    public const DEFAULT_EUR_TO_USD_RATE = 1.08;
    public const PRIMOGEMS_PER_WISH = 160;

    public const DEFAULT_RATES = [
        'EUR' => 1.0,
        'USD' => 1.08,
        'JPY' => 165.0,
        'INR' => 90.0,
        'PRIMO' => 80.0,
    ];

    public function index()
    {
        return view('prices', [
            'currencies' => self::DEFAULT_RATES,
        ]);
    }

    public function getSupportedCurrencies(): array
    {
        return self::DEFAULT_RATES;
    }

    public function convertEuroToDollar(float $amount, ?float $rate = null): float
    {
        return $this->convertCurrency($amount, 'EUR', 'USD', $rate);
    }

    public function convertDollarToEuro(float $amount, ?float $rate = null): float
    {
        return $this->convertCurrency($amount, 'USD', 'EUR', $rate);
    }

    public function convertEuroToYen(float $amount, ?float $rate = null): float
    {
        return $this->convertCurrency($amount, 'EUR', 'JPY', $rate);
    }

    public function convertYenToEuro(float $amount, ?float $rate = null): float
    {
        return $this->convertCurrency($amount, 'JPY', 'EUR', $rate);
    }

    public function convertEuroToRupees(float $amount, ?float $rate = null): float
    {
        return $this->convertCurrency($amount, 'EUR', 'INR', $rate);
    }

    public function convertRupeesToEuro(float $amount, ?float $rate = null): float
    {
        return $this->convertCurrency($amount, 'INR', 'EUR', $rate);
    }

    public function convertEuroToPrimogems(float $amount, ?float $rate = null): float
    {
        return $this->convertCurrency($amount, 'EUR', 'PRIMO', $rate);
    }

    public function convertPrimogemsToEuro(float $amount, ?float $rate = null): float
    {
        return $this->convertCurrency($amount, 'PRIMO', 'EUR', $rate);
    }

    public function calculateWishes(float $primogems): int
    {
        if ($primogems < 0) {
            throw new InvalidArgumentException('Las protogemas no pueden ser negativas.');
        }

        return (int) floor($primogems / self::PRIMOGEMS_PER_WISH);
    }

    public function convertCurrency(float $amount, string $from, string $to, ?float $rate = null): float
    {
        if ($amount < 0) {
            throw new InvalidArgumentException('El monto no puede ser negativo.');
        }

        $from = strtoupper(trim($from));
        $to = strtoupper(trim($to));

        $validCurrencies = array_keys(self::DEFAULT_RATES);
        if (!in_array($from, $validCurrencies, true) || !in_array($to, $validCurrencies, true)) {
            throw new InvalidArgumentException("Divisa no soportada. Solo se admiten EUR, USD, JPY, INR y PRIMO.");
        }

        if ($from === $to) {
            return round($amount, 2);
        }

        if ($rate !== null) {
            if ($rate <= 0) {
                throw new InvalidArgumentException('La tasa de cambio debe ser mayor a cero.');
            }

            if ($from === 'USD' && $to === 'EUR') {
                return round($amount / $rate, 2);
            }

            return round($amount * $rate, 2);
        }

        $amountInEur = $amount / self::DEFAULT_RATES[$from];
        $converted = $amountInEur * self::DEFAULT_RATES[$to];

        return round($converted, 2);
    }

    public function applyDiscount(float $price, float $discountPercentage): float
    {
        $this->validateDiscountParameters($price, $discountPercentage);

        $discountAmount = $price * ($discountPercentage / 100);

        return round($price - $discountAmount, 2);
    }

    public function calculateDiscountAmount(float $price, float $discountPercentage): float
    {
        $this->validateDiscountParameters($price, $discountPercentage);

        return round($price * ($discountPercentage / 100), 2);
    }

    public function calculateDiscount(float $price, float $discountPercentage): float
    {
        return $this->applyDiscount($price, $discountPercentage);
    }

    private function validateDiscountParameters(float $price, float $discountPercentage): void
    {
        if ($price < 0) {
            throw new InvalidArgumentException('El precio no puede ser negativo.');
        }

        if ($discountPercentage < 0 || $discountPercentage > 100) {
            throw new InvalidArgumentException('El porcentaje de descuento debe estar entre 0 y 100.');
        }
    }

    public function convert(Request $request): JsonResponse
    {
        $validList = implode(',', array_merge(
            array_keys(self::DEFAULT_RATES),
            array_map('strtolower', array_keys(self::DEFAULT_RATES))
        ));

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'from' => "required|string|in:{$validList}",
            'to' => "required|string|in:{$validList}",
            'rate' => 'nullable|numeric|gt:0',
        ]);

        $converted = $this->convertCurrency(
            (float) $validated['amount'],
            $validated['from'],
            $validated['to'],
            isset($validated['rate']) ? (float) $validated['rate'] : null
        );

        $toUpper = strtoupper($validated['to']);
        $extra = [];

        if ($toUpper === 'PRIMO') {
            $extra['wishes'] = $this->calculateWishes($converted);
        }

        return response()->json(array_merge([
            'original_amount' => (float) $validated['amount'],
            'from' => strtoupper($validated['from']),
            'to' => $toUpper,
            'converted_amount' => $converted,
        ], $extra));
    }

    public function discount(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'price' => 'required|numeric|min:0',
            'discount_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $finalPrice = $this->applyDiscount(
            (float) $validated['price'],
            (float) $validated['discount_percentage']
        );

        $savedAmount = $this->calculateDiscountAmount(
            (float) $validated['price'],
            (float) $validated['discount_percentage']
        );

        return response()->json([
            'original_price' => (float) $validated['price'],
            'discount_percentage' => (float) $validated['discount_percentage'],
            'discount_amount' => $savedAmount,
            'final_price' => $finalPrice,
        ]);
    }
}

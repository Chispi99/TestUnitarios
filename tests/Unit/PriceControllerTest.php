<?php

namespace Tests\Unit;

use App\Http\Controllers\PriceController;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Tests\TestCase;

class PriceControllerTest extends TestCase
{
    private PriceController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new PriceController();
    }

    public function test_converts_euros_to_dollars_using_default_rate(): void
    {
        $result = $this->controller->convertEuroToDollar(100.0);

        $this->assertEquals(108.00, $result);
    }

    public function test_converts_euros_to_dollars_using_custom_rate(): void
    {
        $result = $this->controller->convertEuroToDollar(100.0, 1.15);

        $this->assertEquals(115.00, $result);
    }

    public function test_converts_dollars_to_euros_using_default_rate(): void
    {
        $result = $this->controller->convertDollarToEuro(108.0);

        $this->assertEquals(100.00, $result);
    }

    public function test_converts_dollars_to_euros_using_custom_rate(): void
    {
        $result = $this->controller->convertDollarToEuro(115.0, 1.15);

        $this->assertEquals(100.00, $result);
    }

    public function test_converts_euros_to_yen_using_default_rate(): void
    {
        $result = $this->controller->convertEuroToYen(10.0);

        $this->assertEquals(1650.00, $result);
    }

    public function test_converts_yen_to_euros_using_default_rate(): void
    {
        $result = $this->controller->convertYenToEuro(1650.0);

        $this->assertEquals(10.00, $result);
    }

    public function test_converts_euros_to_rupees_using_default_rate(): void
    {
        $result = $this->controller->convertEuroToRupees(10.0);

        $this->assertEquals(900.00, $result);
    }

    public function test_converts_rupees_to_euros_using_default_rate(): void
    {
        $result = $this->controller->convertRupeesToEuro(900.0);

        $this->assertEquals(10.00, $result);
    }

    public function test_converts_euros_to_primogems_using_default_rate(): void
    {
        $result = $this->controller->convertEuroToPrimogems(2.0);

        $this->assertEquals(160.00, $result);
    }

    public function test_converts_primogems_to_euros_using_default_rate(): void
    {
        $result = $this->controller->convertPrimogemsToEuro(1600.0);

        $this->assertEquals(20.00, $result);
    }

    public function test_calculates_wishes_from_primogems_correctly(): void
    {
        $this->assertEquals(1, $this->controller->calculateWishes(160.0));
        $this->assertEquals(2, $this->controller->calculateWishes(350.0));
        $this->assertEquals(10, $this->controller->calculateWishes(1600.0));
        $this->assertEquals(0, $this->controller->calculateWishes(0.0));
    }

    public function test_wishes_calculation_throws_exception_for_negative_amount(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Las protogemas no pueden ser negativas.');

        $this->controller->calculateWishes(-10.0);
    }

    public function test_converts_between_non_euro_currencies_usd_to_yen(): void
    {
        $result = $this->controller->convertCurrency(108.0, 'USD', 'JPY');

        $this->assertEquals(16500.00, $result);
    }

    public function test_converts_between_non_euro_currencies_yen_to_rupees(): void
    {
        $result = $this->controller->convertCurrency(165.0, 'JPY', 'INR');

        $this->assertEquals(90.00, $result);
    }

    public function test_converts_dollars_to_primogems(): void
    {
        $result = $this->controller->convertCurrency(108.0, 'USD', 'PRIMO');

        $this->assertEquals(8000.00, $result);
    }

    public function test_conversion_between_same_currency_returns_same_amount(): void
    {
        $resultEur = $this->controller->convertCurrency(50.0, 'EUR', 'EUR');
        $resultPrimo = $this->controller->convertCurrency(1600.0, 'PRIMO', 'PRIMO');

        $this->assertEquals(50.0, $resultEur);
        $this->assertEquals(1600.0, $resultPrimo);
    }

    public function test_conversion_is_case_insensitive_for_currency_codes(): void
    {
        $resultPrimo = $this->controller->convertCurrency(2.0, 'eur', 'primo');

        $this->assertEquals(160.00, $resultPrimo);
    }

    public function test_conversion_with_decimal_values_rounds_properly(): void
    {
        $result = $this->controller->convertEuroToDollar(49.99);

        $this->assertEquals(53.99, $result);
    }

    public function test_conversion_throws_exception_for_negative_amount(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('El monto no puede ser negativo.');

        $this->controller->convertCurrency(-10.0, 'EUR', 'USD');
    }

    public function test_conversion_throws_exception_for_unsupported_currency(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Divisa no soportada. Solo se admiten EUR, USD, JPY, INR y PRIMO.');

        $this->controller->convertCurrency(100.0, 'EUR', 'GBP');
    }

    public function test_conversion_throws_exception_for_zero_or_negative_exchange_rate(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('La tasa de cambio debe ser mayor a cero.');

        $this->controller->convertCurrency(100.0, 'EUR', 'PRIMO', 0.0);
    }

    public function test_applies_standard_discount_correctly(): void
    {
        $finalPrice = $this->controller->applyDiscount(100.0, 20.0);

        $this->assertEquals(80.00, $finalPrice);
    }

    public function test_applies_zero_percent_discount(): void
    {
        $finalPrice = $this->controller->applyDiscount(150.0, 0.0);

        $this->assertEquals(150.00, $finalPrice);
    }

    public function test_applies_one_hundred_percent_discount(): void
    {
        $finalPrice = $this->controller->applyDiscount(200.0, 100.0);

        $this->assertEquals(0.00, $finalPrice);
    }

    public function test_calculates_discount_amount_correctly(): void
    {
        $discountAmount = $this->controller->calculateDiscountAmount(200.0, 15.0);

        $this->assertEquals(30.00, $discountAmount);
    }

    public function test_calculate_discount_alias_returns_final_discounted_price(): void
    {
        $result = $this->controller->calculateDiscount(50.0, 10.0);

        $this->assertEquals(45.00, $result);
    }

    public function test_discount_with_decimals_rounds_to_two_decimal_places(): void
    {
        $finalPrice = $this->controller->applyDiscount(99.99, 33.0);
        $discountAmount = $this->controller->calculateDiscountAmount(99.99, 33.0);

        $this->assertEquals(66.99, $finalPrice);
        $this->assertEquals(33.00, $discountAmount);
    }

    public function test_discount_throws_exception_for_negative_price(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('El precio no puede ser negativo.');

        $this->controller->applyDiscount(-50.0, 10.0);
    }

    public function test_discount_throws_exception_for_negative_discount_percentage(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('El porcentaje de descuento debe estar entre 0 y 100.');

        $this->controller->applyDiscount(100.0, -5.0);
    }

    public function test_discount_throws_exception_for_percentage_greater_than_100(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('El porcentaje de descuento debe estar entre 0 y 100.');

        $this->controller->applyDiscount(100.0, 105.0);
    }

    public function test_index_returns_view(): void
    {
        $response = $this->controller->index();

        $this->assertEquals('prices', $response->name());
    }

    public function test_convert_http_endpoint_returns_json_response_with_jpy(): void
    {
        $request = Request::create('/prices/convert', 'GET', [
            'amount' => 10,
            'from' => 'EUR',
            'to' => 'JPY',
        ]);

        $response = $this->controller->convert($request);
        $data = $response->getData(true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(10.0, $data['original_amount']);
        $this->assertEquals('EUR', $data['from']);
        $this->assertEquals('JPY', $data['to']);
        $this->assertEquals(1650.00, $data['converted_amount']);
    }

    public function test_convert_http_endpoint_returns_json_response_with_inr(): void
    {
        $request = Request::create('/prices/convert', 'GET', [
            'amount' => 10,
            'from' => 'EUR',
            'to' => 'INR',
        ]);

        $response = $this->controller->convert($request);
        $data = $response->getData(true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(10.0, $data['original_amount']);
        $this->assertEquals('EUR', $data['from']);
        $this->assertEquals('INR', $data['to']);
        $this->assertEquals(900.00, $data['converted_amount']);
    }

    public function test_convert_http_endpoint_returns_json_response_with_primogems_and_wishes(): void
    {
        $request = Request::create('/prices/convert', 'GET', [
            'amount' => 20,
            'from' => 'EUR',
            'to' => 'PRIMO',
        ]);

        $response = $this->controller->convert($request);
        $data = $response->getData(true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(20.0, $data['original_amount']);
        $this->assertEquals('EUR', $data['from']);
        $this->assertEquals('PRIMO', $data['to']);
        $this->assertEquals(1600.00, $data['converted_amount']);
        $this->assertEquals(10, $data['wishes']);
    }

    public function test_discount_http_endpoint_returns_json_response(): void
    {
        $request = Request::create('/prices/discount', 'POST', [
            'price' => 100,
            'discount_percentage' => 25,
        ]);

        $response = $this->controller->discount($request);
        $data = $response->getData(true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(100.0, $data['original_price']);
        $this->assertEquals(25.0, $data['discount_percentage']);
        $this->assertEquals(25.00, $data['discount_amount']);
        $this->assertEquals(75.00, $data['final_price']);
    }
}

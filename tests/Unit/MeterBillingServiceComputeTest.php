<?php

namespace Tests\Unit;

use App\Models\Meter;
use App\Models\MeterReading;
use App\Models\Room;
use App\Services\MeterBillingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MeterBillingServiceComputeTest extends TestCase
{
    use RefreshDatabase;

    private MeterBillingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(MeterBillingService::class);
    }

    private function makeMeter(float $rate, float $taxRate): Meter
    {
        $room = Room::create([
            'room_number' => 'CALC-'.uniqid(),
            'room_type' => 'Single',
            'price_per_month' => 1500,
            'capacity' => 1,
            'status' => 'occupied',
        ]);

        return Meter::create([
            'room_id' => $room->id,
            'type' => 'electric',
            'meter_number' => 'CALC-METER-'.uniqid(),
            'unit' => 'kWh',
            'installed_at' => now()->toDateString(),
            'is_active' => true,
            'rate_per_unit' => $rate,
            'tax_rate' => $taxRate,
        ]);
    }

    public function test_compute_calculates_usage_as_difference_between_readings(): void
    {
        $meter = $this->makeMeter(rate: 7.0, taxRate: 0);

        $previous = new MeterReading(['reading_value' => 100]);
        $current = new MeterReading(['reading_value' => 150]);

        $result = $this->service->compute($meter, $previous, $current);

        $this->assertSame(50.0, $result['usage']);
    }

    public function test_compute_calculates_base_cost_as_usage_times_rate(): void
    {
        $meter = $this->makeMeter(rate: 7.5, taxRate: 0);

        $previous = new MeterReading(['reading_value' => 0]);
        $current = new MeterReading(['reading_value' => 100]);

        $result = $this->service->compute($meter, $previous, $current);

        $this->assertSame(750.0, $result['base']);
    }

    public function test_compute_calculates_tax_as_percentage_of_base(): void
    {
        $meter = $this->makeMeter(rate: 10.0, taxRate: 7);

        $previous = new MeterReading(['reading_value' => 0]);
        $current = new MeterReading(['reading_value' => 100]);

        $result = $this->service->compute($meter, $previous, $current);

        $this->assertSame(1000.0, $result['base']);
        $this->assertSame(70.0, $result['tax']);
        $this->assertSame(1070.0, $result['total']);
    }

    public function test_compute_never_returns_negative_usage_when_current_is_less_than_previous(): void
    {
        $meter = $this->makeMeter(rate: 5.0, taxRate: 0);

        $previous = new MeterReading(['reading_value' => 500]);
        $current = new MeterReading(['reading_value' => 100]);

        $result = $this->service->compute($meter, $previous, $current);

        $this->assertSame(0.0, $result['usage']);
        $this->assertSame(0.0, $result['base']);
    }

    public function test_compute_handles_null_previous_reading_as_zero(): void
    {
        $meter = $this->makeMeter(rate: 5.0, taxRate: 0);

        $current = new MeterReading(['reading_value' => 80]);

        $result = $this->service->compute($meter, null, $current);

        $this->assertSame(80.0, $result['usage']);
        $this->assertSame(400.0, $result['base']);
    }
}

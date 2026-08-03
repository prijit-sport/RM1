<?php

namespace Tests\Unit;

use App\Services\InvoiceService;
use Carbon\Carbon;
use Tests\TestCase;

class InvoiceServiceCalculationTest extends TestCase
{
    private InvoiceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(InvoiceService::class);
    }

    public function test_calculate_total_adds_amount_and_tax(): void
    {
        $this->assertSame(1070.0, $this->service->calculateTotal(1000, 70));
    }

    public function test_calculate_total_rounds_to_two_decimal_places(): void
    {
        $this->assertSame(100.13, $this->service->calculateTotal(100.125, 0.005));
    }

    public function test_calculate_total_handles_zero_tax(): void
    {
        $this->assertSame(500.0, $this->service->calculateTotal(500, 0));
    }

    public function test_calculate_due_date_adds_default_fifteen_days(): void
    {
        $issueDate = Carbon::parse('2026-01-01');
        $dueDate = $this->service->calculateDueDate($issueDate);

        $this->assertTrue($dueDate->isSameDay(Carbon::parse('2026-01-16')));
    }

    public function test_calculate_due_date_respects_custom_days_parameter(): void
    {
        $dueDate = $this->service->calculateDueDate('2026-03-01', 30);

        $this->assertTrue($dueDate->isSameDay(Carbon::parse('2026-03-31')));
    }

    public function test_calculate_due_date_accepts_string_date(): void
    {
        $dueDate = $this->service->calculateDueDate('2026-06-15', 7);

        $this->assertTrue($dueDate->isSameDay(Carbon::parse('2026-06-22')));
    }
}

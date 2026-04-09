<?php

namespace Tests\Unit;

use App\Models\Holiday;
use App\Services\BookQueueDeadlineCalculator;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BookQueueDeadlineCalculatorTest extends TestCase
{
    protected BookQueueDeadlineCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropAllTables();
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->string('description');
            $table->timestamps();
        });

        $this->calculator = app(BookQueueDeadlineCalculator::class);
    }

    public function test_it_carries_remaining_hours_to_the_next_working_day(): void
    {
        $deadline = $this->calculator->calculate(Carbon::parse('2026-04-08 14:00:00'));

        $this->assertSame('2026-04-09 13:00:00', $deadline->format('Y-m-d H:i:s'));
    }

    public function test_it_skips_sunday_when_calculating_deadline(): void
    {
        $deadline = $this->calculator->calculate(Carbon::parse('2026-04-11 15:00:00'));

        $this->assertSame('2026-04-13 14:00:00', $deadline->format('Y-m-d H:i:s'));
    }

    public function test_it_skips_configured_holidays_when_calculating_deadline(): void
    {
        Holiday::query()->create([
            'date' => '2026-04-09',
            'description' => 'Libur sekolah',
        ]);

        $deadline = $this->calculator->calculate(Carbon::parse('2026-04-08 14:00:00'));

        $this->assertSame('2026-04-10 13:00:00', $deadline->format('Y-m-d H:i:s'));
    }

    public function test_it_moves_after_hours_start_to_next_working_day_opening_time(): void
    {
        $deadline = $this->calculator->calculate(Carbon::parse('2026-04-08 17:00:00'));

        $this->assertSame('2026-04-09 15:00:00', $deadline->format('Y-m-d H:i:s'));
    }
}

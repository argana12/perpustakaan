<?php

namespace App\Services;

use App\Models\Holiday;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

class BookQueueDeadlineCalculator
{
    public function calculate(CarbonInterface $start): Carbon
    {
        $cursor = Carbon::instance($start instanceof Carbon ? $start->copy() : Carbon::parse($start));
        $remainingMinutes = (int) config('library_queue.pickup_duration_hours', 8) * 60;

        while ($remainingMinutes > 0) {
            $cursor = $this->moveToWorkingSlot($cursor);

            $endOfWorkDay = $cursor->copy()->setTime(
                (int) config('library_queue.close_hour', 16),
                0,
            );

            $availableMinutes = $cursor->diffInMinutes($endOfWorkDay);

            if ($remainingMinutes <= $availableMinutes) {
                return $cursor->addMinutes($remainingMinutes);
            }

            $remainingMinutes -= $availableMinutes;
            $cursor = $this->nextWorkingDayStart($cursor);
        }

        return $cursor;
    }

    public function moveToWorkingSlot(CarbonInterface $dateTime): Carbon
    {
        $cursor = Carbon::instance($dateTime instanceof Carbon ? $dateTime->copy() : Carbon::parse($dateTime));

        while (true) {
            if ($this->isNonWorkingDay($cursor)) {
                $cursor = $this->nextWorkingDayStart($cursor);
                continue;
            }

            $openHour = (int) config('library_queue.open_hour', 7);
            $closeHour = (int) config('library_queue.close_hour', 16);

            if ($cursor->hour < $openHour) {
                return $cursor->setTime($openHour, 0, 0);
            }

            if ($cursor->hour >= $closeHour) {
                $cursor = $this->nextWorkingDayStart($cursor);
                continue;
            }

            return $cursor;
        }
    }

    public function nextWorkingDayStart(CarbonInterface $dateTime): Carbon
    {
        $cursor = Carbon::instance($dateTime instanceof Carbon ? $dateTime->copy() : Carbon::parse($dateTime))
            ->addDay()
            ->setTime((int) config('library_queue.open_hour', 7), 0, 0);

        while ($this->isNonWorkingDay($cursor)) {
            $cursor->addDay()->setTime((int) config('library_queue.open_hour', 7), 0, 0);
        }

        return $cursor;
    }

    public function isNonWorkingDay(CarbonInterface $dateTime): bool
    {
        $cursor = Carbon::instance($dateTime instanceof Carbon ? $dateTime->copy() : Carbon::parse($dateTime));

        return in_array($cursor->dayOfWeek, config('library_queue.non_working_days', [0]), true)
            || Holiday::isHoliday($cursor);
    }
}

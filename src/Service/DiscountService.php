<?php

namespace App\Service;

use DateTimeImmutable;
use InvalidArgumentException;

class DiscountService
{
    const ROUND_PRECISION = 2;
    const MAX_UNDERAGE_DISCOUNT_AMOUNT = 4500;
    const MAX_EARLY_BOOKING_DISCOUNT_AMOUNT = 1500;

    public function __construct(
        private readonly DateService $dateService,
    ) {}

    public function getUnderageDiscountRate(int $yearOld): float
    {
        return match (true) {
            $yearOld < 3 || $yearOld >= 18 => 0,
            $yearOld >= 12 => 0.1,
            $yearOld >= 6 => 0.3,
            $yearOld >= 3 => 0.8,
        };
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getEarlyBookingDiscountRate(DateTimeImmutable $startDate, DateTimeImmutable $paymentDate): float
    {
        if ($startDate < $paymentDate) {
            throw new InvalidArgumentException('Start date must not be later than pay date.');
        }

        $today = new DateTimeImmutable('today');
        $discountRates = [];

        $firstApril = $today->modify('first day of April next year');
        $thirtySeptember = $today->modify('last day of September next year');
        $isBetween = $this->dateService->isBetweenDates($startDate, $firstApril, $thirtySeptember);
        if ($isBetween) {
            $lastNovember = $today->modify('last day of November');
            $lastDecember = $today->modify('last day of December');
            $lastJanuary = $today->modify('last day of January next year');

            $discountRates[] = match (true) {
                $paymentDate <= $lastNovember => 0.07,
                $paymentDate <= $lastDecember => 0.05,
                $paymentDate <= $lastJanuary => 0.03,
                default => 0
            };
        }

        $firstOctober = $today->modify('first day of October');
        $fourteenthJanuary = $today
            ->modify('first day of January next year')
            ->modify('+13 days');
        $isBetween = $this->dateService->isBetweenDates($startDate, $firstOctober, $fourteenthJanuary);
        if ($isBetween) {
            $lastMarch = $today->modify('last day of March');
            $lastApril = $today->modify('last day of April');
            $lastMay = $today->modify('last day of May');

            $discountRates[] = match (true) {
                $paymentDate <= $lastMarch => 0.07,
                $paymentDate <= $lastApril => 0.05,
                $paymentDate <= $lastMay => 0.03,
                default => 0
            };
        }

        $fifteenthJanuary = $today
            ->modify('first day of January next year')
            ->modify('+14 days');
        if ($fifteenthJanuary <= $startDate) {
            $lastAugust = $today->modify('last day of August');
            $lastSeptember = $today->modify('last day of September');
            $lastOctober = $today->modify('last day of October');

            $discountRates[] = match (true) {
                $paymentDate <= $lastAugust => 0.07,
                $paymentDate <= $lastSeptember => 0.05,
                $paymentDate <= $lastOctober => 0.03,
                default => 0
            };
        }

        return max($discountRates);
    }

    public function getDiscountAmount(float $price, float $discountRate): float
    {
        return round($price * $discountRate, self::ROUND_PRECISION);
    }

    public function getMaxUnderageDiscountAmount(float $discountAmount, int $yearOld): float
    {
        if ($yearOld >= 6 && $yearOld < 12) {
            $discountAmount = min($discountAmount, self::MAX_UNDERAGE_DISCOUNT_AMOUNT);
        }

        return $discountAmount;
    }

    public function getMaxEarlyBookingDiscountAmount(float $discountAmount): float
    {
        return min($discountAmount, self::MAX_EARLY_BOOKING_DISCOUNT_AMOUNT);
    }

}
<?php

namespace App\Service;

use DateTimeImmutable;
use InvalidArgumentException;

class DateService
{
    /**
     * @throws InvalidArgumentException
     */
    public function getAgeAt(DateTimeImmutable $birthdayDate, DateTimeImmutable $dateAt = new DateTimeImmutable('now')): int
    {
        if ($birthdayDate > $dateAt) {
            throw new InvalidArgumentException('Birthday date must be earlier than provided date.');
        }
        return $birthdayDate->diff($dateAt)->y;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function isBetweenDates(DateTimeImmutable $targetDate, DateTimeImmutable $startDate, DateTimeImmutable $endDate): bool
    {
        if ($startDate > $endDate) {
            throw new InvalidArgumentException('Start date must be earlier than end date.');
        }
        return $startDate <= $targetDate && $endDate >= $targetDate;
    }
}
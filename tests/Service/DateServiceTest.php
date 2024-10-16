<?php

namespace App\Tests\Service;

use App\Service\DateService;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DateServiceTest extends TestCase
{
    public function testIsBetweenDatesSuccess(): void
    {
        $dateService = new DateService();
        $targetDate = new DateTimeImmutable('today');
        $startDate = new DateTimeImmutable('-1 day');
        $endDate = new DateTimeImmutable('+1 day');

        $this->assertTrue($dateService->isBetweenDates($targetDate, $startDate, $endDate));
        $this->assertTrue($dateService->isBetweenDates($targetDate, $targetDate, $targetDate));

        $targetDate = new DateTimeImmutable('2020-01-01');
        $startDate = $targetDate->modify('first day of October');
        $endDate = $targetDate
            ->modify('first day of January next year')
            ->modify('+13 days');
        $this->assertFalse($dateService->isBetweenDates($targetDate, $startDate, $endDate));
    }

    public function testIsBetweenDatesException(): void
    {
        $dateService = new DateService();
        $targetDate = new DateTimeImmutable('today');
        $startDate = new DateTimeImmutable('-1 day');
        $endDate = new DateTimeImmutable('+2 day');

        $this->expectException(InvalidArgumentException::class);
        $dateService->isBetweenDates($targetDate, $endDate, $startDate);
    }

    public function testAgeAtSuccess(): void
    {
        $dateService = new DateService();

        $birthdayDate = new DateTimeImmutable('2000-01-01');

        $age = $dateService->getAgeAt($birthdayDate);
        $this->assertEquals(24, $age);

        $age = $dateService->getAgeAt($birthdayDate, new DateTimeImmutable('2004-01-01'));
        $this->assertEquals(4, $age);

        $age = $dateService->getAgeAt($birthdayDate, new DateTimeImmutable('2004-01-01'));
        $this->assertEquals(4, $age);
    }

    public function testAgeAtException(): void
    {
        $dateService = new DateService();

        $birthdayDate = new DateTimeImmutable('2000-01-01');

        $this->expectException(InvalidArgumentException::class);
        $dateService->getAgeAt($birthdayDate, $birthdayDate->modify('-1 day'));
    }
}

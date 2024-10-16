<?php

namespace App\Tests\Service;

use App\Service\DateService;
use App\Service\DiscountService;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DiscountServiceTest extends TestCase
{

    public function testGetUnderageDiscountSuccess()
    {
        $discountService = new DiscountService(new DateService());

        $discountRate = $discountService->getUnderageDiscountRate(0);
        $this->assertEquals(0, $discountRate);

        $discountRate = $discountService->getUnderageDiscountRate(2);
        $this->assertEquals(0, $discountRate);

        $discountRate = $discountService->getUnderageDiscountRate(3);
        $this->assertEquals(0.8, $discountRate);

        $discountRate = $discountService->getUnderageDiscountRate(4);
        $this->assertEquals(0.8, $discountRate);

        $discountRate = $discountService->getUnderageDiscountRate(5);
        $this->assertEquals(0.8, $discountRate);

        $discountRate = $discountService->getUnderageDiscountRate(6);
        $this->assertEquals(0.3, $discountRate);

        $discountRate = $discountService->getUnderageDiscountRate(7);
        $this->assertEquals(0.3, $discountRate);

        $discountRate = $discountService->getUnderageDiscountRate(11);
        $this->assertEquals(0.3, $discountRate);

        $discountRate = $discountService->getUnderageDiscountRate(12);
        $this->assertEquals(0.1, $discountRate);

        $discountRate = $discountService->getUnderageDiscountRate(13);
        $this->assertEquals(0.1, $discountRate);

        $discountRate = $discountService->getUnderageDiscountRate(17);
        $this->assertEquals(0.1, $discountRate);

        $discountRate = $discountService->getUnderageDiscountRate(18);
        $this->assertEquals(0, $discountRate);

        $discountRate = $discountService->getUnderageDiscountRate(123);
        $this->assertEquals(0, $discountRate);
    }

    public function testGetEarlyBookingDiscountSuccess()
    {
        $discountService = new DiscountService(new DateService());

        $startDate = new DateTimeImmutable('today');
        $paymentDate = new DateTimeImmutable('today');

        $discountRate = $discountService->getEarlyBookingDiscountRate(
            $startDate->modify('first day of april next year'),
            $paymentDate
        );
        $this->assertEquals(0.07, $discountRate);

        $discountRate = $discountService->getEarlyBookingDiscountRate(
            $startDate->modify('first day of april next year'),
            $paymentDate->modify('last day of december')
        );
        $this->assertEquals(0.05, $discountRate);

        $discountRate = $discountService->getEarlyBookingDiscountRate(
            $startDate->modify('first day of april next year'),
            $paymentDate->modify('last day of january next year')
        );
        $this->assertEquals(0.03, $discountRate);


        $discountRate = $discountService->getEarlyBookingDiscountRate(
            $startDate->modify('first day of january next year')->modify('+13 days'),
            $paymentDate->modify('first day of march')->modify('+13 days')
        );
        $this->assertEquals(0.07, $discountRate);

        $discountRate = $discountService->getEarlyBookingDiscountRate(
            $startDate->modify('first day of january next year')->modify('+13 days'),
            $paymentDate->modify('first day of april')->modify('+13 days')
        );
        $this->assertEquals(0.05, $discountRate);

        $discountRate = $discountService->getEarlyBookingDiscountRate(
            $startDate->modify('first day of january next year')->modify('+13 days'),
            $paymentDate->modify('last day of may')
        );
        $this->assertEquals(0.03, $discountRate);

        $discountRate = $discountService->getEarlyBookingDiscountRate(
            $startDate->modify('first day of october'),
            $paymentDate->modify('last day of may')
        );
        $this->assertEquals(0.03, $discountRate);


        $discountRate = $discountService->getEarlyBookingDiscountRate(
            $startDate->modify('first day of january next year')->modify('+14 days'),
            $startDate->modify('first day of january')->modify('+14 days'),
        );
        $this->assertEquals(0.07, $discountRate);

        $discountRate = $discountService->getEarlyBookingDiscountRate(
            $startDate->modify('first day of january next year')->modify('+14 days'),
            $startDate->modify('last day of august'),
        );
        $this->assertEquals(0.07, $discountRate);

        $discountRate = $discountService->getEarlyBookingDiscountRate(
            $startDate->modify('first day of january next year')->modify('+14 days'),
            $startDate->modify('last day of september'),
        );
        $this->assertEquals(0.05, $discountRate);

        $discountRate = $discountService->getEarlyBookingDiscountRate(
            $startDate->modify('first day of january next year')->modify('+14 days'),
            $startDate->modify('last day of october'),
        );
        $this->assertEquals(0.03, $discountRate);

        $discountRate = $discountService->getEarlyBookingDiscountRate(
            $startDate->modify('first day of april next year')->modify('+14 days'),
            $startDate->modify('last day of october'),
        );
        $this->assertEquals(0.07, $discountRate);
    }

    public function testGetEarlyBookingDiscountException()
    {
        $discountService = new DiscountService(new DateService());

        $startDate = new DateTimeImmutable('2020-01-01');
        $paymentDate = new DateTimeImmutable('2021-01-01');

        $this->expectException(InvalidArgumentException::class);
        $discountService->getEarlyBookingDiscountRate($startDate, $paymentDate);
    }

    public function testGetDiscountAmountSuccess()
    {
        $discountService = new DiscountService(new DateService());

        $discountAmount = $discountService->getDiscountAmount(100, 0.5);
        $this->assertEquals(50, $discountAmount);

        $discountAmount = $discountService->getDiscountAmount(100, 0.05);
        $this->assertEquals(5, $discountAmount);
    }

    public function testGetMaxUnderageDiscountAmountSuccess()
    {
        $discountService = new DiscountService(new DateService());

        $discountAmount = $discountService->getMaxUnderageDiscountAmount(10000, 18);
        $this->assertEquals(10000, $discountAmount);

        $discountAmount = $discountService->getMaxUnderageDiscountAmount(10000, 10);
        $this->assertEquals(DiscountService::MAX_UNDERAGE_DISCOUNT_AMOUNT, $discountAmount);

    }

    public function testMaxEarlyBookingDiscountAmountSuccess()
    {
        $discountService = new DiscountService(new DateService());

        $discountAmount = $discountService->getMaxEarlyBookingDiscountAmount(100);
        $this->assertEquals(100, $discountAmount);

        $discountAmount = $discountService->getMaxEarlyBookingDiscountAmount(10000);
        $this->assertEquals(DiscountService::MAX_EARLY_BOOKING_DISCOUNT_AMOUNT, $discountAmount);
    }
}

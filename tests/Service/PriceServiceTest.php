<?php

namespace App\Tests\Service;

use App\DTO\PriceTravelDTO;
use App\Service\DateService;
use App\Service\DiscountService;
use App\Service\PriceService;
use PHPUnit\Framework\TestCase;

class PriceServiceTest extends TestCase
{
    public function testGetEarlyBookingDiscountedPriceSuccess()
    {
        $priceService = new PriceService(new DiscountService(new DateService()), new DateService());
        $priceTravelDTO = new PriceTravelDTO(
            new \DateTimeImmutable('2024-11-30'),
            new \DateTimeImmutable('2025-05-27'),
            new \DateTimeImmutable('2025-06-05'),
            new \DateTimeImmutable('2000-01-01'),
            1000,
        );
        $price = $priceService->getEarlyBookingDiscountedPrice($priceTravelDTO);
        $this->assertEquals(930, $price);

        $priceTravelDTO = new PriceTravelDTO(
            new \DateTimeImmutable('2024-08-30'),
            new \DateTimeImmutable('2025-01-15'),
            new \DateTimeImmutable('2025-08-30'),
            new \DateTimeImmutable('2020-01-01'),
            1000,
        );

        $price = $priceService->getEarlyBookingDiscountedPrice($priceTravelDTO);
        $this->assertEquals(930, $price);
    }

    public function testGetEarlyBookingDiscountedPriceException()
    {
        $priceService = new PriceService(new DiscountService(new DateService()), new DateService());
        $priceTravelDTO = new PriceTravelDTO(
            new \DateTimeImmutable('2025-01-01'),
            new \DateTimeImmutable('2025-04-15'),
            new \DateTimeImmutable('2026-05-05'),
            new \DateTimeImmutable('2020-01-01'),
            -1000,
        );
        $this->expectException(\Exception::class);
        $priceService->getUnderageDiscountedPrice($priceTravelDTO);
    }

    public function testGetUnderageDiscountedPriceSuccess()
    {
        $priceService = new PriceService(new DiscountService(new DateService()), new DateService());
        $priceTravelDTO = new PriceTravelDTO(
            new \DateTimeImmutable('2024-11-30'),
            new \DateTimeImmutable('2025-05-27'),
            new \DateTimeImmutable('2025-06-05'),
            new \DateTimeImmutable('2000-01-01'),
            1000,
        );
        $price = $priceService->getUnderageDiscountedPrice($priceTravelDTO);
        $this->assertEquals(1000, $price);

        $priceTravelDTO = new PriceTravelDTO(
            new \DateTimeImmutable('2024-08-30'),
            new \DateTimeImmutable('2025-01-15'),
            new \DateTimeImmutable('2025-08-30'),
            new \DateTimeImmutable('2020-01-01'),
            1000,
        );

        $price = $priceService->getUnderageDiscountedPrice($priceTravelDTO);
        $this->assertEquals(200, $price);
    }

    public function testGetUnderageDiscountedPriceException()
    {
        $priceService = new PriceService(new DiscountService(new DateService()), new DateService());
        $priceTravelDTO = new PriceTravelDTO(
            new \DateTimeImmutable('2024-11-30'),
            new \DateTimeImmutable('2025-05-27'),
            new \DateTimeImmutable('2025-06-05'),
            new \DateTimeImmutable('2000-01-01'),
            -1000,
        );
        $this->expectException(\Exception::class);
        $priceService->getUnderageDiscountedPrice($priceTravelDTO);
    }

}

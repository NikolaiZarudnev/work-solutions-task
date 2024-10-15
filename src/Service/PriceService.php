<?php

namespace App\Service;

use App\DTO\PriceTravelDTO;
use Exception;

class PriceService
{

    public function __construct(
        private readonly DiscountService $discountService,
        private readonly DateService $dateService,
    ) {}

    /**
     * @throws Exception
     */
    public function getUnderageDiscountedPrice(PriceTravelDTO $costTravelDTO): float
    {
        $yearOld = $this->dateService->getAgeAt(
            $costTravelDTO->getBirthdayDate(),
            $costTravelDTO->getStartDate()
        );
        $discountRate = $this->discountService->getUnderageDiscountRate($yearOld);
        $discountAmount = $this->discountService->getDiscountAmount(
            $costTravelDTO->getOriginPrice(),
            $discountRate
        );
        $discountAmount = $this->discountService->getMaxUnderageDiscountAmount(
            $discountAmount,
            $yearOld
        );

        $result = $costTravelDTO->getOriginPrice() - $discountAmount;
        if ($result < 0) {
            throw new Exception('Discounted price cannot be negative');
        }
        return $result;
    }

    /**
     * @throws Exception
     */
    public function getEarlyBookingDiscountedPrice(PriceTravelDTO $costTravelDTO): float
    {
        $discount = $this->discountService->getEarlyBookingDiscountRate(
            $costTravelDTO->getStartDate(),
            $costTravelDTO->getPaymentDate()
        );

        $discountAmount = $this->discountService->getDiscountAmount(
            $costTravelDTO->getOriginPrice(),
            $discount
        );
        $discountAmount = $this->discountService->getMaxEarlyBookingDiscountAmount($discountAmount);

        $result = $costTravelDTO->getOriginPrice() - $discountAmount;
        if ($result < 0) {
            throw new Exception('Discounted price cannot be negative');
        }
        return $result;
    }
}
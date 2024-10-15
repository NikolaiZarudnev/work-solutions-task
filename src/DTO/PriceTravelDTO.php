<?php

namespace App\DTO;

use DateTimeImmutable;

class PriceTravelDTO
{
    private ?DateTimeImmutable $paymentDate = null;
    private ?DateTimeImmutable $startDate;
    private ?DateTimeImmutable $endDate;
    private ?DateTimeImmutable $birthdayDate;
    private ?int $originPrice;

    public function __construct(
        ?DateTimeImmutable $paymentDate,
        ?DateTimeImmutable $startDate,
        ?DateTimeImmutable $endDate,
        ?DateTimeImmutable $birthdayDate,
        ?int $originPrice,
    )
    {
        $this->paymentDate = $paymentDate ?? new DateTimeImmutable('now');
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->birthdayDate = $birthdayDate;
        $this->originPrice = $originPrice;
    }

    public function getPaymentDate(): ?DateTimeImmutable
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(DateTimeImmutable $paymentDate): void
    {
        $this->paymentDate = $paymentDate;
    }

    public function getStartDate(): ?DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(DateTimeImmutable $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getEndDate(): ?DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(DateTimeImmutable $endDate): void
    {
        $this->endDate = $endDate;
    }

    public function getBirthdayDate(): ?DateTimeImmutable
    {
        return $this->birthdayDate;
    }

    public function setBirthdayDate(DateTimeImmutable $birthdayDate): void
    {
        $this->birthdayDate = $birthdayDate;
    }

    public function getOriginPrice(): ?int
    {
        return $this->originPrice;
    }

    public function setOriginPrice(int $originPrice): void
    {
        $this->originPrice = $originPrice;
    }

}
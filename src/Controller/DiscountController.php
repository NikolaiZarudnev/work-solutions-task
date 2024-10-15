<?php

namespace App\Controller;

use App\DTO\PriceTravelDTO;
use App\Service\PriceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DiscountController extends AbstractController
{
    public function __construct(
        private readonly PriceService $priceService,
        private readonly ValidatorInterface $validator,
        private readonly SerializerInterface $serializer
    ) {}

    #[Route('/get-discounted-price', name: 'get_discounted_price', methods: ['POST'])]
    public function getDiscountedPrice(Request $request): JsonResponse
    {
        $data = $request->request->all();
        try {
            $costTravelDTO = $this->serializer->denormalize(
                $data,
                PriceTravelDTO::class,
                'array',
                [AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true]
            );
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }

        $errors = $this->validator->validate($costTravelDTO);
        if (count($errors) > 0) {
            return $this->json(['error' => $errors[0]->getMessage()], 400);
        }

        try {
            $discountedPrice = $this->priceService->getUnderageDiscountedPrice($costTravelDTO);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
        $costTravelDTO->setOriginPrice($discountedPrice);
        try {
            $discountedPrice = $this->priceService->getEarlyBookingDiscountedPrice($costTravelDTO);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }

        return $this->json($discountedPrice);
    }
}
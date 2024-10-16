<?php

namespace App\Tests\Controller;

use App\Service\PriceService;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DiscountControllerTest extends WebTestCase
{
    private readonly KernelBrowser $client;
    private readonly PriceService $priceService;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        $this->priceService = $this->createMock(PriceService::class);
        self::getContainer()->set(PriceService::class, $this->priceService);
    }

    public function testGetDiscountedPriceException(): void
    {
        $payload = [
            'startDate' => '01.05.qwe',
            'endDate' => '07.06.2025',
            'paymentDate' => '28.11.2024',
            'birthdayDate' => '16.12.2000',
            'originPrice' => '10000',
        ];
        $this->client->request(
            Request::METHOD_POST,
            '/get-discounted-price',
            $payload,
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertStringContainsString('Failed to parse time string', $this->client->getResponse()->getContent());

        $payload = [
            'startDate' => '07.07.2025',
            'endDate' => '07.06.2025',
            'paymentDate' => '28.11.2024',
            'birthdayDate' => '16.12.2000',
            'originPrice' => '10000',
        ];
        $this->client->request(
            Request::METHOD_POST,
            '/get-discounted-price',
            $payload,
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertStringContainsString('Start Date date should be greater than or equal to', $this->client->getResponse()->getContent());

        $payload = [
            'startDate' => '07.05.2025',
            'endDate' => '07.06.2025',
            'paymentDate' => '05.07.2024',
            'birthdayDate' => '16.12.2000',
            'originPrice' => '10000',
        ];
        $this->client->request(
            Request::METHOD_POST,
            '/get-discounted-price',
            $payload,
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertStringContainsString('Payment date should be greater than or equal to', $this->client->getResponse()->getContent());

        $payload = [
            'startDate' => '07.05.2025',
            'endDate' => '07.06.2025',
            'paymentDate' => '28.11.2024',
            'birthdayDate' => '16.12.2025',
            'originPrice' => '10000',
        ];
        $this->client->request(
            Request::METHOD_POST,
            '/get-discounted-price',
            $payload,
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertStringContainsString('Birthday date should be less than', $this->client->getResponse()->getContent());

        $payload = [
            'startDate' => '07.05.2025',
            'endDate' => '07.06.2025',
            'paymentDate' => '28.11.2024',
            'birthdayDate' => '16.12.2010',
            'originPrice' => '-10000',
        ];
        $this->client->request(
            Request::METHOD_POST,
            '/get-discounted-price',
            $payload,
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertStringContainsString('Price should be positive or equal to 0.', $this->client->getResponse()->getContent());
    }

    public function testGetDiscountedPriceSuccess(): void
    {
        $payload = [
            'startDate' => '01.05.2025',
            'endDate' => '07.06.2025',
            'paymentDate' => '28.11.2024',
            'birthdayDate' => '16.12.2000',
            'originPrice' => '10000',
        ];
        $this->priceService
            ->expects($this->once())
            ->method('getUnderageDiscountedPrice')
            ->willReturn(10000.0);
        $this->priceService
            ->expects($this->once())
            ->method('getEarlyBookingDiscountedPrice')
            ->willReturn(9300.0);
        $this->client->request(
            Request::METHOD_POST,
            '/get-discounted-price',
            $payload,
            [],
            [],
            json_encode($payload)
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('9300', $this->client->getResponse()->getContent());
    }

    public function testGetDiscountedPriceSuccessUnderage(): void
    {
        $payload = [
            'startDate' => '01.05.2025',
            'endDate' => '07.06.2025',
            'paymentDate' => '28.11.2024',
            'birthdayDate' => '16.12.2020',
            'originPrice' => '10000',
        ];
        $this->priceService
            ->expects($this->once())
            ->method('getUnderageDiscountedPrice')
            ->willReturn(2000.0);
        $this->priceService
            ->expects($this->once())
            ->method('getEarlyBookingDiscountedPrice')
            ->willReturn(1860.0);
        $this->client->request(
            Request::METHOD_POST,
            '/get-discounted-price',
            $payload,
            [],
            [],
            json_encode($payload)
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('1860', $this->client->getResponse()->getContent());
    }
}

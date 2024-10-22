<?php

namespace App\Service;

use App\Entity\Material;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BrevoService
{

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly string $apiKey,
        private readonly string $baseUrl,
        private readonly string $to,
        private readonly string $brevoMailingUrl = 'https://api.brevo.com/v3/smtp/email',
    ) { }

    public function sendOutOfStockAlertEmail(Material $material): void
    {

        $data = [
            'to' => [
                [
                    'email' => $this->to
                ]
            ],
            'templateId' => 6,
            'params' => [
                'url' => $this->baseUrl . '/material/' . $material->getId() . '/edit',
                'id' => $material->getId(),
                'name' => $material->getName(),
                'priceTaxFree' => $material->getPriceTaxFree(),
                'priceTaxIncluded' => $material->getPriceTaxIncluded(),
            ],
        ];

        try {
            $response = $this->client->request('POST', $this->brevoMailingUrl, [
                'json' => $data,
                'headers' => [
                    'api-key' => $this->apiKey,
                    'Content-Type' => 'application/json'
                ]
            ]);

            if ($response->getStatusCode() !== 201) {
                throw new HttpException($response->getStatusCode(), 'Failed to send email: ' . $response->getContent(false));
            }
        } catch (\Exception $e) {
            throw new HttpException(500, 'Failed to send email: ' . $e->getMessage());
        }
    }
}

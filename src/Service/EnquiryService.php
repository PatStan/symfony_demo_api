<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class EnquiryService
{
    public function __construct(
        protected string $baseUrl,
        protected string $accessToken,
        private readonly HttpClientInterface $httpClient,
    ){}

    public function create(string $subscriberId, string $message)
    {
        $response = $this->httpClient->request('POST', $this->baseUrl . '/api/subscriber/' . $subscriberId . '/enquiry', [
            'headers' => [
                'Authorization' => 'Bearer '. $this->accessToken,
                'Accept' => 'application/json',
            ],
            'json' => [
                'message' => $message,
            ]
        ]);

        $data = $response->toArray();

        if ($response->getStatusCode() !== 200) {
            return new JsonResponse([
                'error' => 'Failed to create enquiry: ' . $data['error'],
                'message' => $data['message'],
            ], $response->getStatusCode());
        }

        return $data;
    }
}

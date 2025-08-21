<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Subscriber;
use App\Request\CreateOrUpdateSubscriberRequest;
use GuzzleHttp\Promise\Create;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SubscriberService
{
    public function __construct(
        protected string                     $baseUrl,
        protected string                     $accessToken,
        private readonly HttpClientInterface $httpClient,
        private readonly ListService         $listService,
    )
    {

    }

    public function subscribe(
        string $emailAddress,
        ?string $firstName,
        ?string $lastName,
        ?string $dateOfBirth,
        ?bool $marketingConsent,
        ?array $lists,
    )
    {
        if ($marketingConsent === false) {
            $lists = [];
        }

        $response = $this->httpClient->request('PUT', $this->baseUrl . '/api/subscriber', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Accept' => 'application/json',
            ],
            'json' => [
                'emailAddress' => $emailAddress,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'dateOfBirth' => $dateOfBirth,
                'marketingConsent' => $marketingConsent,
                'lists' => $lists,
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            return new JsonResponse(['error' => 'Failed to create or update subscriber'], $response->getStatusCode());
        }

        return $response;
    }

    public function get(string $subscriberId)
    {
        $response = $this->httpClient->request('GET', $this->baseUrl . 'api/subscriber/' . $subscriberId, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Accept' => 'application/json',
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            return new JsonResponse(['error' => 'Failed to retrieve subscriber'], $response->getStatusCode());
        }

        $data = $response->toArray();

        return $data['subscriber'];
    }
}

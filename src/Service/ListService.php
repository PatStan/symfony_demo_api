<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class ListService
{
    public function __construct(
        protected string $baseUrl,
        protected string $accessToken,
        private HttpClientInterface $httpClient,
        private CacheInterface $cache,
        )
    {
    }

    public function getLists(): array
    {
        return $this->cache->get('lists', function (ItemInterface $item) {
            $item->expiresAfter(300);

            if (!$this->accessToken) {
                throw new \RuntimeException('HTTP token missing');
            }

            $response = $this->httpClient->request('GET', $this->baseUrl . '/api/lists', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Accept' => 'application/json',
                ],
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new \RuntimeException('Failed to fetch lists');
            }

            $data = $response->toArray();

            if (empty($data['lists'])) {
                return []; //TODO: throw exception instead?
            }

            // Extract id => name array
            $listMap = [];
            foreach ($data['lists'] as $list) {
                $listMap[$list['id']] = $list['name'];
            }

            return $listMap;
        });
    }
}

<?php

declare(strict_types=1);

namespace Zoho\Subscription\Client;

use Doctrine\Common\Cache\Cache;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Psr\Http\Message\ResponseInterface;

class Client
{
    protected $token;

    protected $organizationId;

    protected $cache;

    protected $client;

    protected $ttl;

    protected $messageFactory;

    public function __construct(string $token, int $organizationId, Cache $cache, int $ttl = 7200)
    {
        $this->token          = $token;
        $this->organizationId = $organizationId;
        $this->ttl            = $ttl;
        $this->cache          = $cache;
        $this->client         = HttpClientDiscovery::find();
        $this->messageFactory = MessageFactoryDiscovery::find();
    }

    public function getFromCache(string $key)
    {
        // If the results are already cached
        if ($this->cache->contains($key)) {
            return unserialize($this->cache->fetch($key));
        }

        return false;
    }

    public function saveToCache(string $key, $values): bool
    {
        return $this->cache->save($key, serialize($values), $this->ttl);
    }

    public function deleteCacheByKey(string $key)
    {
        $this->cache->delete($key);
    }

    protected function processResponse(ResponseInterface $response): array
    {
        $data = json_decode($response->getBody()->getContents(), true);

        if (0 != $data['code']) {
            throw new \Exception('Zoho Api subscription error : '.$data['message']);
        }

        return $data;
    }

    protected function sendRequest(string $method, string $uri, array $headers = [], string $body = null)
    {
        $baseUri = 'https://subscriptions.zoho.com/api/v1/';
        $request = $this->messageFactory->createRequest($method, $baseUri.$uri, $this->getRequestHeaders($headers), $body);

        return $this->client->sendRequest($request);
    }

    protected function getRequestHeaders(array $headers = [])
    {
        $defaultHeaders = [
            'Authorization'                           => 'Zoho-authtoken '.$this->token,
            'X-com-zoho-subscriptions-organizationid' => $this->organizationId,
        ];

        return array_merge($defaultHeaders, $headers);
    }
}

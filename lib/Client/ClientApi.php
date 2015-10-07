<?php

namespace ZohoSubscriptionApi\Client;

use Predis\Client;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response;

class ClientApi
{
    /**
     * @var bool
     */
    protected $cache;

    /**
     * @var Client
     */
    protected $guzzleClient;

    /**
     * @var Client
     */
    protected $redis;

    /**
     * @var int
     */
    protected $ttl;

    public function __construct($token, $organizationId, $cache = false, Client $redis = null, $ttl = 3600)
    {
        $this->cache        = $cache;
        $this->guzzleClient = new GuzzleClient(['headers' => ['Authorization' => 'Zoho-authtoken ' . $token, 'X-com-zoho-subscriptions-organizationid' => $organizationId]]);
        $this->redis        = $redis;
        $this->ttl          = $ttl;
    }

    /**
     * @param Response $response
     *
     * @throws \Exception
     *
     * @return array
     */
    protected function processResponse(Response $response)
    {
        $data = json_decode($response->getBody(), true);

        if ($data['code'] != 0) {
            throw new \Exception('Zoho Api subscription error : ' . $data['message']);
        }

        return $data;
    }
}

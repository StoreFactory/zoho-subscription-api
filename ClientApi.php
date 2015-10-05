<?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class ClientApi
{
    /**
     * @var Client
     */
    protected $guzzleClient;

    public function __construct($token, $organizationId)
    {
        $this->guzzleClient   = new Client(['headers' => ['Authorization' => 'Zoho-authtoken ' . $token, 'X-com-zoho-subscriptions-organizationid' => $organizationId]]);
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

<?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

use AppBundle\ZohoSubscription\ClientApi;

class AddonApi extends ClientApi
{
    /**
     * @param array $filters associative array of filters
     *
     * @return array
     */
    public function listAllAddons($filters = [])
    {
        $response = $this->guzzleClient->request('GET', 'https://subscriptions.zoho.com/api/v1/addons');

        $addons = $this->processResponse($response);

        foreach ($filters as $key => $filter) {
            if (array_key_exists($key, $addons['addons'])) {
                $addons['addons'] = array_filter($addons['addons'] , function ($element) use ($key, $filter) {
                    return $element[$key] == $filter;
                });
            }
        }

        return $addons;
    }

    /**
     * @param int $planCode
     *
     * @return array
     */
    public function getAddon($addonCode)
    {
        $response = $this->guzzleClient->request('GET', 'https://subscriptions.zoho.com/api/v1/addons/'.$addonCode);

        return $this->processResponse($response);
    }
}

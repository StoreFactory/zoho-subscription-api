<?php

use AppBundle\ZohoSubscription\ClientApi;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class PlanApi extends ClientApi
{
    /**
     * @param array $filters associative array of filters
     *
     * @return array
     */
    public function listAllPlans($filters = [])
    {
        $response = $this->guzzleClient->request('GET', 'https://subscriptions.zoho.com/api/v1/plans');

        $plans = $this->processResponse($response);

        foreach ($filters as $key => $filter) {
            if (array_key_exists($key, $plans['plans'])) {
                $plans['plans'] = array_filter($plans['plans'] , function ($element) use ($key, $filter) {
                    return $element[$key] == $filter;
                });
            }
        }

        return $plans;
    }

    /**
     * @param int $planCode
     *
     * @return array
     */
    public function getPlan($planCode)
    {
        $response = $this->guzzleClient->request('GET', 'https://subscriptions.zoho.com/api/v1/addons/'.$addonCode);

        return $this->processResponse($response);
    }
}

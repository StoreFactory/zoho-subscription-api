<?php

namespace ZohoSubscriptionApi\Plan;

use ZohoSubscriptionApi\Client\ClientApi;

class PlanApi extends ClientApi
{
    /**
     * @param array $filters associative array of filters
     *
     * @return array
     */
    private function listAllPlans($filters = [])
    {
        $response = $this->guzzleClient->request('GET', 'https://subscriptions.zoho.com/api/v1/plans');

        $plans = $this->processResponse($response);

        foreach ($filters as $key => $filter) {
            if (array_key_exists($key, $plans['plans'])) {
                $plans['plans'] = array_filter($plans['plans'], function ($element) use ($key, $filter) {
                    return $element[$key] == $filter;
                });
            }
        }

        return $plans;
    }

    /**
     * @throws \Exception
     *
     * @return Response
     */
    public function getAllPlans($filters = [])
    {
        // If the cache is activated
        if ($this->redis && true === $this->cache) {
            // If the results are already cached
            if ($this->redis->exists('plans')) {
                return unserialize($this->redis->get('plans'));
            } else {
                // Otherwise we store them
                $plans = $this->listAllPlans($filters);

                $this->redis->setex('plans', $this->ttl, serialize($plans));

                return $plans;
            }
        }

        return $this->getAllPlans();
    }

    /**
     * @param int $planCode
     *
     * @return array
     */
    public function getPlan($planCode)
    {
        $response = $this->guzzleClient->request('GET', 'https://subscriptions.zoho.com/api/v1/plans/'.$planCode);

        return $this->processResponse($response);
    }
}

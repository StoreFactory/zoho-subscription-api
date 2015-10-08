<?php

namespace ZohoSubscriptionApi\Addon;

use ZohoSubscriptionApi\Client\ClientApi;

class AddonApi extends ClientApi
{
    /**
     * @param array $filters associative array of filters
     *
     * @return array
     */
    private function listAllAddons($filters = [])
    {
        $response = $this->guzzleClient->request('GET', 'https://subscriptions.zoho.com/api/v1/addons');

        $addons = $this->processResponse($response);

        foreach ($filters as $key => $filter) {
            if (array_key_exists($key, $addons['addons'])) {
                $addons['addons'] = array_filter($addons['addons'], function ($element) use ($key, $filter) {
                    return $element[$key] == $filter;
                });
            }
        }

        return $addons;
    }

    /**
     * @throws \Exception
     *
     * @return Response
     */
    public function getAllAddons($filters = [])
    {
        // If the cache is activated
        if ($this->redis && true === $this->cache) {
            // If the results are already cached
            if ($this->redis->exists('addons')) {
                return unserialize($this->redis->get('addons'));
            } else {
                // Otherwise we store them
                $addons = $this->listAllAddons($filters);

                $this->redis->setex('addons', $this->ttl, serialize($addons));

                return $addons;
            }
        }

        return $this->getAllAddons();
    }

    /**
     * @param int $addonCode
     *
     * @return array
     */
    public function getAddon($addonCode)
    {
        $response = $this->guzzleClient->request('GET', 'https://subscriptions.zoho.com/api/v1/addons/'.$addonCode);

        return $this->processResponse($response);
    }
}

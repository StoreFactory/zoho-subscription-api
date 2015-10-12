<?php

namespace Zoho\Subscription\Api;

use Zoho\Subscription\Client\Client;


/**
 * Plan
 *
 * @author Tristan Perchec <tristan.perchec@yproximite.com>
 * @author Tristan Bessoussa <tristan.bessoussa@gmail.com>
 *
 * @link https://www.zoho.com/subscriptions/api/v1/#plans
 */
class Plan extends Client
{
    static $addonTypes = [
        "recurring",
        "one_time"
    ];

    /**
     * Returns all plans
     *
     * @param array $filters associative array of filters
     * @param null  $cacheKey
     *
     * @throws \Exception
     *
     * @return array
     */
    public function listPlans($filters = [], $cacheKey = null, $withAddons = true, $addonType = null)
    {
        $hit = $this->getFromCache($cacheKey);

        if (false === $hit) {
            $response = $this->client->request('GET', 'plans');

            $plans = $this->processResponse($response);

            foreach ($filters as $key => $filter) {
                if (array_key_exists($key, $plans['plans'])) {
                    $plans['plans'] = array_filter($plans['plans'], function ($element) use ($key, $filter) {
                        return $element[$key] == $filter;
                    });
                }
            }

            if ($withAddons) {
                $addonApi = new Addon($this->token, $this->organizationId, $this->enableCache, $this->ttl, $this->cache);

                foreach ($plans['plans'] as &$plan) {
                    $addons = [];

                    foreach ($plan['addons'] as $planAddon) {
                        $addon = $addonApi->getAddon($planAddon['addon_code'])['addon'];

                        if ((null !== $addonType) && ($addon['type'] == $addonType) && (in_array($addonType, self::$addonTypes))) {
                            $addons[] = $addon;
                        }
                    }

                    $plan['addons'] = $addons;
                }
            }

            $this->saveToCache($cacheKey, $plans['plans']);

            return $plans['plans'];
        }

        return $hit;
    }


    /**
     * Returns a Plan by its identifier
     *
     * @param int  $planCode
     * @param null $cacheKey
     *
     * @throws \Exception
     *
     * @return array
     */
    public function getPlan($planCode, $cacheKey = null)
    {
        $hit = $this->getFromCache($cacheKey);

        if (false === $hit) {
            $response = $this->client->request('GET', sprintf('/plans/%s', $planCode));

            $plan = $this->processResponse($response);

            $this->saveToCache($cacheKey, $plan);

            return $plan;
        }

        return $hit;
    }

}

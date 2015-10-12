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
     *
     * @throws \Exception
     *
     * @return array
     */
    public function listPlans($filters = [], $withAddons = true, $addonType = null)
    {
        $hit = $this->getFromCache('plans');

        if (false === $hit) {
            $response = $this->client->request('GET', 'plans');

            $plans = $this->processResponse($response);
            $plans = $this->filterPlans($plans, $filters);

            if ($withAddons) {
                $plans = $this->getAddonsForPlan($plans, $addonType);
            }

            $this->saveToCache('plans', $plans['plans']);

            return $plans['plans'];
        }

        return $hit;
    }


    /**
     * Returns a Plan by its identifier
     *
     * @param int  $planCode
     *
     * @throws \Exception
     *
     * @return array
     */
    public function getPlan($planCode)
    {
        $hit = $this->getFromCache('plan_'.$planCode);

        if (false === $hit) {
            $response = $this->client->request('GET', sprintf('/plans/%s', $planCode));

            $plan = $this->processResponse($response);

            $this->saveToCache('plan_'.$planCode, $plan);

            return $plan;
        }

        return $hit;
    }

    public function getAddonsForPlan($plans, $addonType)
    {
        $addonApi = new Addon($this->token, $this->organizationId, $this->cache, $this->ttl);

        foreach ($plans['plans'] as &$plan) {
            $addons = [];

            foreach ($plan['addons'] as $planAddon) {
                $addon = $addonApi->getAddon($planAddon['addon_code'])['addon'];

                if (null !== $addonType) {
                    if (($addon['type'] == $addonType) && (in_array($addonType, self::$addonTypes))) {
                        $addons[] = $addon;
                    }
                } else {
                    $addons[] = $addon;
                }
            }

            $plan['addons'] = $addons;
        }

        return $plans;
    }

    public function filterPlans($plans, $filters)
    {
        foreach ($filters as $key => $filter) {
            if (array_key_exists($key, current($plans['plans']))) {
                $plans['plans'] = array_filter($plans['plans'], function ($element) use ($key, $filter) {
                    return $element[$key] == $filter;
                });
            }
        }

        return $plans;
    }
}

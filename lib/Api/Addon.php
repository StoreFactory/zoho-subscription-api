<?php

declare(strict_types=1);

namespace Zoho\Subscription\Api;

use Zoho\Subscription\Client\Client;

/**
 * Addon.
 *
 * @author Tristan Perchec <tristan.perchec@yproximite.com>
 * @author Tristan Bessoussa <tristan.bessoussa@gmail.com>
 *
 * @see https://www.zoho.com/subscriptions/api/v1/#addons
 */
class Addon extends Client
{
    /**
     * @param array $filters associative array of filters
     *
     * @throws \Exception
     */
    public function listAddons(array $filters = []): array
    {
        $cacheKey = 'addons';
        $hit      = $this->getFromCache($cacheKey);

        if (false === $hit) {
            $response = $this->sendRequest('GET', $cacheKey);

            $addons = $this->processResponse($response);
            $hit    = $addons['addons'];

            $this->saveToCache($cacheKey, $hit);
        }

        foreach ($filters as $key => $filter) {
            if (array_key_exists($key, current($hit))) {
                $hit = array_filter($hit, function ($element) use ($key, $filter) {
                    return $element[$key] == $filter;
                });
            }
        }

        return $hit;
    }

    /**
     * @throws \Exception
     */
    public function getAddon(string $addonCode): array
    {
        $cacheKey = sprintf('addon_%s', $addonCode);
        $hit      = $this->getFromCache($cacheKey);

        if (false === $hit) {
            $response = $this->sendRequest('GET', sprintf('addons/%s', $addonCode));

            $data  = $this->processResponse($response);
            $addon = $data['addon'];

            $this->saveToCache($cacheKey, $addon);

            return $addon;
        }

        return $hit;
    }
}

<?php

namespace Zoho\Subscription\Api;

use Zoho\Subscription\Client\Client;

/**
 * Addon
 *
 * @author Tristan Perchec <tristan.perchec@yproximite.com>
 * @author Tristan Bessoussa <tristan.bessoussa@gmail.com>
 *
 * @link https://www.zoho.com/subscriptions/api/v1/#addons
 */
class Addon extends Client
{

    /**
     * @param array $filters associative array of filters
     * @param null  $cacheKey
     *
     * @throws \Exception
     *
     * @return array
     */
    public function listAddons($filters = [], $cacheKey = null)
    {
        $hit = $this->getFromCache($cacheKey);

        if (false === $hit) {
            $response = $this->client->request('GET', 'addons');

            $addons = $this->processResponse($response);

            foreach ($filters as $key => $filter) {
                if (array_key_exists($key, $addons['addons'])) {
                    $addons['addons'] = array_filter($addons['addons'], function ($element) use ($key, $filter) {
                        return $element[$key] == $filter;
                    });
                }
            }

            $this->saveToCache($cacheKey, $addons);

            return $addons;
        }

        return $hit;

    }

    /**
     * @param int  $code
     * @param null $cacheKey
     *
     * @throws \Exception
     *
     * @return array
     */
    public function getAddon($code, $cacheKey = null)
    {
        $hit = $this->getFromCache($cacheKey);

        if (false === $hit) {
            $response = $this->client->request('GET', sprintf('addons/%s', $code));

            $addon = $this->processResponse($response);

            $this->saveToCache($cacheKey, $addon);

            return $addon;
        }

        return $hit;
    }

}

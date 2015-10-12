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
     *
     * @throws \Exception
     *
     * @return array
     */
    public function listAddons($filters = [])
    {
        $hit = $this->getFromCache('addons');

        if (false === $hit) {
            $response = $this->client->request('GET', 'addons');

            $addons = $this->processResponse($response);

            foreach ($filters as $key => $filter) {
                if (array_key_exists($key, current($addons['addons']))) {
                    $addons['addons'] = array_filter($addons['addons'], function ($element) use ($key, $filter) {
                        return $element[$key] == $filter;
                    });
                }
            }

            $this->saveToCache('addons', $addons['addons']);

            return $addons['addons'];
        }

        return $hit;
    }

    /**
     * @param int  $addonCode
     *
     * @throws \Exception
     *
     * @return array
     */
    public function getAddon($addonCode)
    {
        $hit = $this->getFromCache('addon_'.$addonCode);

        if (false === $hit) {
            $response = $this->client->request('GET', sprintf('addons/%s', $addonCode));

            $addon = $this->processResponse($response);

            $this->saveToCache('addon_'.$addonCode, $addon);

            return $addon;
        }

        return $hit;
    }

}

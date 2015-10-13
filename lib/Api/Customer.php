<?php

namespace Zoho\Subscription\Api;

use Zoho\Subscription\Client\Client;

/**
 * @author Hang Pham <thi@yproximite.com>
 * @author Tristan Bessoussa <tristan.bessoussa@gmail.com>
 *
 * @link https://www.zoho.com/subscriptions/api/v1/#customers
 */
class Customer extends Client
{
    /**
     * @param string $customerEmail The customer's email
     *
     * @throws \Exception
     *
     * @return array
     */
    public function getCustomerByEmail($customerEmail)
    {
        $cacheKey = sprintf('zoho_customer_%s', md5($customerEmail));
        $hit      = $this->getFromCache($cacheKey);

        if (false === $hit) {
            $response = $this->client->request('GET', 'customers', [
                'query' => ['email' => $customerEmail]
            ]);

            $result = $this->processResponse($response);

            $customer = $result['customers'];

            $this->saveToCache($cacheKey, $customer);

            return $customer;
        }

        return $hit;
    }

    /**
     * @param string $customerId The customer's id
     *
     * @throws \Exception
     *
     * @return array
     */
    public function getCustomerById($customerId)
    {
        $cacheKey = sprintf('zoho_customer_%s', $customerId);
        $hit      = $this->getFromCache($cacheKey);

        if (false === $hit) {
            $response = $this->client->request('GET', sprintf('customers/%s', $customerId));
            $result = $this->processResponse($response);

            $customer = $result['customer'];

            $this->saveToCache($cacheKey, $customer);

            return $customer;
        }

        return $hit;
    }
}
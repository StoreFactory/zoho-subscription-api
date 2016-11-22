<?php
declare(strict_types=1);

namespace Zoho\Subscription\Api;

use Zoho\Subscription\Client\Client;

/**
 * @author Hang Pham <thi@yproximite.com>
 * @author Tristan Bessoussa <tristan.bessoussa@gmail.com>
 *
 * @link   https://www.zoho.com/subscriptions/api/v1/#customers
 */
class Customer extends Client
{
    /**
     * @param string $customerEmail
     *
     * @return array
     */
    public function getListCustomersByEmail(string $customerEmail): array
    {
        $cacheKey = sprintf('zoho_customer_%s', md5($customerEmail));
        $hit = $this->getFromCache($cacheKey);

        if (false === $hit) {
            $response = $this->sendRequest('GET', sprintf('customers?email=%s', $customerEmail));

            $result = $this->processResponse($response);

            $customers = $result['customers'];

            $this->saveToCache($cacheKey, $customers);

            return $customers;
        }

        return $hit;
    }

    /**
     * @param string $customerEmail
     *
     * @return array
     */
    public function getCustomerByEmail(string $customerEmail): array
    {
        $customers = $this->getListCustomersByEmail($customerEmail);

        return $this->getCustomerById($customers[0]['customer_id']);
    }

    /**
     * @param string $customerId The customer's id
     *
     * @throws \Exception
     *
     * @return array
     */
    public function getCustomerById(string $customerId): array
    {
        $cacheKey = sprintf('zoho_customer_%s', $customerId);
        $hit = $this->getFromCache($cacheKey);

        if (false === $hit) {
            $response = $this->sendRequest('GET', sprintf('customers/%s', $customerId));
            $result = $this->processResponse($response);

            $customer = $result['customer'];

            $this->saveToCache($cacheKey, $customer);

            return $customer;
        }

        return $hit;
    }

    /**
     * @param string $customerId The customer's id
     * @param array  $data
     *
     * @throws \Exception
     *
     * @return array|bool
     */
    public function updateCustomer(string $customerId, array $data)
    {
        $response = $this->sendRequest('PUT', sprintf('customers/%s', $customerId), ['content-type' => 'application/json'], json_encode($data));

        $result = $this->processResponse($response);

        if ($result['code'] == '0') {
            $customer = $result['customer'];

            $this->deleteCustomerCache($customer);

            return $customer;
        } else {
            return false;
        }
    }

    /**
     * @param array $customer
     */
    private function deleteCustomerCache(array $customer)
    {
        $cacheKey = sprintf('zoho_customer_%s', $customer['customer_id']);
        $this->deleteCacheByKey($cacheKey);

        $cacheKey = sprintf('zoho_customer_%s', md5($customer['email']));
        $this->deleteCacheByKey($cacheKey);
    }

    /**
     * @param array $data
     *
     * @throws \Exception
     *
     * @return array|bool
     */
    public function createCustomer(array $data)
    {
        $response = $this->sendRequest('POST', 'customers', [
            'content-type' => 'application/json',
            'body' => json_encode($data),
        ]);

        $result = $this->processResponse($response);

        if ($result['code'] == '0') {
            $customer = $result['customer'];

            return $customer;
        } else {
            return false;
        }
    }
}

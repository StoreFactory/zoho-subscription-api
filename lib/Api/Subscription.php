<?php

declare(strict_types=1);

namespace Zoho\Subscription\Api;

use Zoho\Subscription\Client\Client;

/**
 * Subscription.
 *
 * @author Elodie Nazaret <elodie@yproximite.com>
 *
 * @see https://www.zoho.com/subscriptions/api/v1/#subscriptions
 */
class Subscription extends Client
{
    const STATUS_UNPAID = 'unpaid';

    /**
     * @throws \Exception
     *
     * @return string
     */
    public function createSubscription(array $data)
    {
        $response = $this->sendRequest('POST', 'subscriptions', ['content-type' => 'application/json'], json_encode($data));

        return $this->processResponse($response);
    }

    /**
     * @param string $subscriptionId The subscription's id
     *
     * @throws \Exception
     *
     * @return string
     */
    public function buyOneTimeAddonForASubscription(string $subscriptionId, array $data)
    {
        $response = $this->sendRequest('POST', sprintf('subscriptions/%s/buyonetimeaddon', $subscriptionId), [], json_encode($data));

        return $this->processResponse($response);
    }

    /**
     * @param string $subscriptionId The subscription's id
     * @param string $couponCode     The coupon's code
     *
     * @throws \Exception
     *
     * @return array
     */
    public function associateCouponToASubscription(string $subscriptionId, string $couponCode)
    {
        $response = $this->sendRequest('POST', sprintf('subscriptions/%s/coupons/%s', $subscriptionId, $couponCode));

        return $this->processResponse($response);
    }

    /**
     * @param string $subscriptionId The subscription's id
     *
     * @throws \Exception
     *
     * @return string
     */
    public function reactivateSubscription(string $subscriptionId)
    {
        $response = $this->sendRequest('POST', sprintf('subscriptions/%s/reactivate', $subscriptionId));

        return $this->processResponse($response);
    }

    /**
     * @param string $subscriptionId The subscription's id
     *
     * @throws \Exception
     *
     * @return array
     */
    public function getSubscription(string $subscriptionId)
    {
        $cacheKey = sprintf('zoho_subscription_%s', $subscriptionId);
        $hit      = $this->getFromCache($cacheKey);

        if (false === $hit) {
            $response = $this->sendRequest('GET', sprintf('subscriptions/%s', $subscriptionId));

            $result = $this->processResponse($response);

            $subscription = $result['subscription'];

            $this->saveToCache($cacheKey, $subscription);

            return $subscription;
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
    public function listSubscriptionsByCustomer(string $customerId)
    {
        $cacheKey = sprintf('zoho_subscriptions_%s', $customerId);
        $hit      = $this->getFromCache($cacheKey);

        if (false === $hit) {
            $response = $this->sendRequest('GET', sprintf('subscriptions?customer_id=%s', $customerId));

            $result = $this->processResponse($response);

            $invoices = $result['subscriptions'];

            $this->saveToCache($cacheKey, $invoices);

            return $invoices;
        }

        return $hit;
    }
}

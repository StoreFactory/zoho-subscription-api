<?php

namespace Zoho\Subscription\Api;

use Zoho\Subscription\Client\Client;

/**
 * Invoice
 *
 * @author Tristan Perchec <tristan.perchec@yproximite.com>
 * @author Tristan Bessoussa <tristan.bessoussa@gmail.com>
 *
 * @link https://www.zoho.com/subscriptions/api/v1/#addons
 */
class Invoice extends Client
{

    /**
     * @param string $customerId The customer's id
     * @param null   $cacheKey
     *
     * @throws \Exception
     *
     * @return array
     */
    public function listInvoicesByCustomer($customerId, $cacheKey = null)
    {
        $hit = $this->getFromCache($cacheKey);

        if (false === $hit) {
            $response = $this->client->request('GET', 'invoices', [
                'query' => ['customer_id' => $customerId]
            ]);

            $result = $this->processResponse($response);

            $invoices = $result['invoices'];

            $this->saveToCache($cacheKey, $invoices);

            return $invoices;
        }

        return $hit;

    }

    /**
     * @param string $invoiceId
     * @param null   $cacheKey
     *
     * @throws \Exception
     *
     * @return array
     */
    public function getInvoice($invoiceId, $cacheKey = null)
    {
        $hit = $this->getFromCache($cacheKey);

        if (false === $hit) {
            $response = $this->client->request('GET', sprintf('invoices/%s', $invoiceId));

            $result = $this->processResponse($response);

            $invoice = $result['invoice'];

            $this->saveToCache($cacheKey, $invoice);

            return $invoice;
        }

        return $hit;
    }

}

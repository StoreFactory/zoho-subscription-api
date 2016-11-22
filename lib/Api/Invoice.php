<?php
declare(strict_types=1);

namespace Zoho\Subscription\Api;

use Zoho\Subscription\Client\Client;

/**
 * @author Hang Pham <thi@yproximite.com>
 * @author Tristan Bessoussa <tristan.bessoussa@gmail.com>
 *
 * @link https://www.zoho.com/subscriptions/api/v1/#invoices
 */
class Invoice extends Client
{
    /**
     * @param string $customerId The customer's id
     *
     * @throws \Exception
     *
     * @return array
     */
    public function listInvoicesByCustomer(string $customerId): array
    {
        $cacheKey = sprintf('zoho_invoices_%s', $customerId);
        $hit = $this->getFromCache($cacheKey);

        if (false === $hit) {
            $response = $this->sendRequest('GET', sprintf('invoices?customer_id=%s', $customerId));

            $result = $this->processResponse($response);

            $invoices = $result['invoices'];

            $this->saveToCache($cacheKey, $invoices);

            return $invoices;
        }

        return $hit;
    }

    /**
     * @param string $invoiceId The invoice's id
     *
     * @throws \Exception
     *
     * @return array
     */
    public function getInvoice($invoiceId)
    {
        $cacheKey = sprintf('zoho_invoice_%s', $invoiceId);
        $hit = $this->getFromCache($cacheKey);

        if (false === $hit) {
            $response = $this->sendRequest('GET', sprintf('invoices/%s', $invoiceId));

            $result = $this->processResponse($response);

            $invoice = $result['invoice'];

            $this->saveToCache($cacheKey, $invoice);

            return $invoice;
        }

        return $hit;
    }

    /**
     * @param string $invoiceId The invoice's id
     *
     * @throws \Exception
     *
     * @return array
     */
    public function getInvoicePdf($invoiceId)
    {
        $response = $this->sendRequest('GET', sprintf('invoices/%s', $invoiceId), [
            'query' => ['accept' => 'pdf'],
        ]);

        return $response;
    }
}

<?php
declare(strict_types=1);

namespace Zoho\Subscription\Api;

use Zoho\Subscription\Client\Client;

class HostedPage extends Client
{
    public function listHostedPages(): array
    {
        $response = $this->sendRequest('GET', 'hostedpages', ['content-type' => 'application/json'], json_encode($data));

        return $this->processResponse($response);
    }

    public function createSubscription(array $data): array
    {
        $response = $this->sendRequest('POST', 'hostedpages/newsubscription', ['content-type' => 'application/json'], json_encode($data));

        return $this->processResponse($response);
    }

    public function updateSubscription(array $data): array
    {
        $response = $this->sendRequest('POST', 'hostedpages/updatesubscription', ['content-type' => 'application/json'], json_encode($data));

        return $this->processResponse($response);
    }

    public function updateCard(array $data): array
    {
        $response = $this->sendRequest('POST', 'hostedpages/updatecard', ['content-type' => 'application/json'], json_encode($data));

        return $this->processResponse($response);
    }

    public function buyOneTimeAddon(array $data): array
    {
        $response = $this->sendRequest('POST', 'hostedpages/buyonetimeaddon', ['content-type' => 'application/json'], json_encode($data));

        return $this->processResponse($response);
    }

    public function retrieveHostedPageFromSubscriptionId(string $subscriptionId): array
    {
        $hostedPages = $this->listHostedPages();

        foreach ($hostedPages as $hostedPage) {
            if (!empty($hostedPage['data'])) {
                if ($hostedPage['data']['subscription']['subscription_id'] == $subscriptionId) {
                    return $hostedPage;
                }
            }
        }

        return null;
    }
}

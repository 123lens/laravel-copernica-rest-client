<?php

namespace Budgetlens\Copernica\RestClient;

use Budgetlens\Copernica\RestClient\Http\CopernicaHttpClient;
use Budgetlens\Copernica\RestClient\Resources\DatabaseResource;
use Budgetlens\Copernica\RestClient\Resources\EmailingResource;
use Budgetlens\Copernica\RestClient\Resources\WebhookResource;

class CopernicaClient
{
    private readonly CopernicaHttpClient $http;

    public function __construct(
        string $accessToken,
        string $baseUrl = 'https://api.copernica.com/v4',
        int $timeout = 30,
    ) {
        $this->http = new CopernicaHttpClient($accessToken, $baseUrl, $timeout);
    }

    // --- Account ---

    public function identity(): array
    {
        return $this->http->get('identity');
    }

    public function consumption(): array
    {
        return $this->http->get('consumption');
    }

    // --- Databases ---

    public function databases(): DatabaseResource
    {
        return new DatabaseResource($this->http);
    }

    public function database(int $id): DatabaseResource
    {
        return new DatabaseResource($this->http, $id);
    }

    // --- Emailings (HTML) ---

    public function emailings(): EmailingResource
    {
        return new EmailingResource($this->http, type: 'html');
    }

    public function emailing(int $id): EmailingResource
    {
        return new EmailingResource($this->http, emailingId: $id, type: 'html');
    }

    // --- Emailings (Drag & Drop / Marketing Suite) ---

    public function dragAndDropEmailings(): EmailingResource
    {
        return new EmailingResource($this->http, type: 'draganddrop');
    }

    public function dragAndDropEmailing(int $id): EmailingResource
    {
        return new EmailingResource($this->http, emailingId: $id, type: 'draganddrop');
    }

    // --- Webhooks ---

    public function webhooks(): WebhookResource
    {
        return new WebhookResource($this->http);
    }

    public function webhook(int $id): WebhookResource
    {
        return new WebhookResource($this->http, webhookId: $id);
    }

    // --- Raw access for uncovered endpoints ---

    public function get(string $endpoint, array $query = []): array
    {
        return $this->http->get($endpoint, $query);
    }

    public function post(string $endpoint, array $data = []): array
    {
        return $this->http->post($endpoint, $data);
    }

    public function put(string $endpoint, array $data = []): array
    {
        return $this->http->put($endpoint, $data);
    }

    public function delete(string $endpoint, array $query = []): array
    {
        return $this->http->delete($endpoint, $query);
    }
}

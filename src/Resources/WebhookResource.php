<?php

namespace Budgetlens\Copernica\RestClient\Resources;

use Budgetlens\Copernica\RestClient\DTOs\Webhook;
use Budgetlens\Copernica\RestClient\Http\CopernicaHttpClient;

class WebhookResource extends Resource
{
    public function __construct(
        CopernicaHttpClient $http,
        private readonly ?int $webhookId = null,
    ) {
        parent::__construct($http);
    }

    /**
     * @return array<Webhook>
     */
    public function list(): array
    {
        return $this->listResources('webhooks', Webhook::class);
    }

    public function get(): Webhook
    {
        $id = $this->requireId($this->webhookId, 'webhook');

        return $this->getResource("webhook/{$id}", Webhook::class);
    }

    public function create(array $data): int
    {
        return $this->createResource('webhooks', $data);
    }

    public function update(array $data): void
    {
        $id = $this->requireId($this->webhookId, 'webhook');

        $this->updateResource("webhook/{$id}", $data);
    }

    public function delete(): void
    {
        $id = $this->requireId($this->webhookId, 'webhook');

        $this->deleteResource("webhook/{$id}");
    }
}

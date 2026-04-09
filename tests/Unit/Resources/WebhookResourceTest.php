<?php

use Budgetlens\Copernica\RestClient\DTOs\Webhook;
use Budgetlens\Copernica\RestClient\Resources\WebhookResource;
use Budgetlens\Copernica\RestClient\Tests\Helpers\MocksHttpClient;

uses(MocksHttpClient::class);

it('lists all webhooks', function () {
    $http = $this->mockHttpClient([
        ['body' => ['data' => [
            ['ID' => 1, 'handler' => 'profile', 'url' => 'https://example.com/hook', 'trigger' => 'create'],
        ]]],
    ]);

    $resource = new WebhookResource($http);
    $result = $resource->list();

    expect($result)->toHaveCount(1)
        ->and($result[0])->toBeInstanceOf(Webhook::class)
        ->and($result[0]->handler)->toBe('profile');
});

it('gets a single webhook', function () {
    $http = $this->mockHttpClient([
        ['body' => ['ID' => 42, 'handler' => 'profile', 'url' => 'https://example.com/hook', 'trigger' => 'update']],
    ]);

    $resource = new WebhookResource($http, webhookId: 42);
    $result = $resource->get();

    expect($result)->toBeInstanceOf(Webhook::class)
        ->and($result->id)->toBe(42)
        ->and($result->trigger)->toBe('update');
});

it('creates a webhook', function () {
    $http = $this->mockHttpClient([
        ['status' => 201, 'headers' => ['Content-Type' => 'application/json', 'X-location' => 'https://api.copernica.com/v4/webhook/50'], 'body' => []],
    ]);

    $resource = new WebhookResource($http);
    $id = $resource->create(['handler' => 'profile', 'url' => 'https://example.com/hook', 'trigger' => 'create']);

    expect($id)->toBe(50);
});

it('updates a webhook', function () {
    $http = $this->mockHttpClient([
        ['status' => 204, 'headers' => ['Content-Type' => 'application/json']],
    ]);

    $resource = new WebhookResource($http, webhookId: 42);
    $resource->update(['url' => 'https://example.com/hook-v2']);

    expect($this->lastRequestMethod())->toBe('PUT')
        ->and($this->lastRequestBody())->toBe(['url' => 'https://example.com/hook-v2']);
});

it('deletes a webhook', function () {
    $http = $this->mockHttpClient([
        ['status' => 204, 'headers' => ['Content-Type' => 'application/json']],
    ]);

    $resource = new WebhookResource($http, webhookId: 42);
    $resource->delete();

    expect($this->lastRequestMethod())->toBe('DELETE')
        ->and($this->lastRequestUri())->toContain('webhook/42');
});

it('throws when getting without ID', function () {
    $http = $this->mockHttpClient([]);

    $resource = new WebhookResource($http);
    $resource->get();
})->throws(LogicException::class);

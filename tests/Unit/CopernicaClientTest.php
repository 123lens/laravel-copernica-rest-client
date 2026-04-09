<?php

use Budgetlens\Copernica\RestClient\CopernicaClient;
use Budgetlens\Copernica\RestClient\Resources\DatabaseResource;
use Budgetlens\Copernica\RestClient\Resources\EmailingResource;
use Budgetlens\Copernica\RestClient\Resources\WebhookResource;

it('can be instantiated with an access token', function () {
    $client = new CopernicaClient('test-token');

    expect($client)->toBeInstanceOf(CopernicaClient::class);
});

it('returns DatabaseResource for databases()', function () {
    $client = new CopernicaClient('test-token');

    expect($client->databases())->toBeInstanceOf(DatabaseResource::class);
});

it('returns DatabaseResource for database()', function () {
    $client = new CopernicaClient('test-token');

    expect($client->database(123))->toBeInstanceOf(DatabaseResource::class);
});

it('returns EmailingResource for emailings()', function () {
    $client = new CopernicaClient('test-token');

    expect($client->emailings())->toBeInstanceOf(EmailingResource::class);
});

it('returns EmailingResource for emailing()', function () {
    $client = new CopernicaClient('test-token');

    expect($client->emailing(123))->toBeInstanceOf(EmailingResource::class);
});

it('returns EmailingResource for dragAndDropEmailings()', function () {
    $client = new CopernicaClient('test-token');

    expect($client->dragAndDropEmailings())->toBeInstanceOf(EmailingResource::class);
});

it('returns EmailingResource for dragAndDropEmailing()', function () {
    $client = new CopernicaClient('test-token');

    expect($client->dragAndDropEmailing(123))->toBeInstanceOf(EmailingResource::class);
});

it('returns WebhookResource for webhooks()', function () {
    $client = new CopernicaClient('test-token');

    expect($client->webhooks())->toBeInstanceOf(WebhookResource::class);
});

it('returns WebhookResource for webhook()', function () {
    $client = new CopernicaClient('test-token');

    expect($client->webhook(123))->toBeInstanceOf(WebhookResource::class);
});

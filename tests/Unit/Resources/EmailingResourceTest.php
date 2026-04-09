<?php

use Budgetlens\Copernica\RestClient\DTOs\Emailing;
use Budgetlens\Copernica\RestClient\DTOs\EmailingDestination;
use Budgetlens\Copernica\RestClient\DTOs\EmailingStatistics;
use Budgetlens\Copernica\RestClient\Http\PaginatedResponse;
use Budgetlens\Copernica\RestClient\Resources\EmailingResource;
use Budgetlens\Copernica\RestClient\Tests\Helpers\MocksHttpClient;

uses(MocksHttpClient::class);

it('lists HTML emailings', function () {
    $http = $this->mockHttpClient([
        ['body' => ['data' => [
            ['ID' => 1, 'subject' => 'Newsletter Jan'],
            ['ID' => 2, 'subject' => 'Newsletter Feb'],
        ]]],
    ]);

    $resource = new EmailingResource($http, type: 'html');
    $result = $resource->list();

    expect($result)->toHaveCount(2)
        ->and($result[0])->toBeInstanceOf(Emailing::class)
        ->and($this->lastRequestUri())->toContain('html-emailings');
});

it('lists drag and drop emailings', function () {
    $http = $this->mockHttpClient([
        ['body' => ['data' => [['ID' => 1]]]],
    ]);

    $resource = new EmailingResource($http, type: 'draganddrop');
    $resource->list();

    expect($this->lastRequestUri())->toContain('draganddrop-emailings');
});

it('paginates emailings', function () {
    $http = $this->mockHttpClient([
        ['body' => ['data' => [['ID' => 1]], 'total' => 1]],
    ]);

    $resource = new EmailingResource($http, type: 'html');
    $result = $resource->each();

    expect($result)->toBeInstanceOf(PaginatedResponse::class);
});

it('lists scheduled emailings', function () {
    $http = $this->mockHttpClient([
        ['body' => ['data' => [['ID' => 1, 'subject' => 'Scheduled']]]],
    ]);

    $resource = new EmailingResource($http, type: 'html');
    $result = $resource->scheduled();

    expect($result)->toHaveCount(1)
        ->and($this->lastRequestUri())->toContain('html-scheduledemailings');
});

it('gets a single emailing', function () {
    $http = $this->mockHttpClient([
        ['body' => ['ID' => 300, 'subject' => 'Monthly newsletter', 'type' => 'html']],
    ]);

    $resource = new EmailingResource($http, emailingId: 300, type: 'html');
    $result = $resource->get();

    expect($result)->toBeInstanceOf(Emailing::class)
        ->and($result->subject)->toBe('Monthly newsletter')
        ->and($this->lastRequestUri())->toContain('html-emailing/300');
});

it('creates an emailing', function () {
    $http = $this->mockHttpClient([
        ['status' => 201, 'headers' => ['Content-Type' => 'application/json', 'X-location' => 'https://api.copernica.com/v4/emailing/500'], 'body' => []],
    ]);

    $resource = new EmailingResource($http, type: 'html');
    $id = $resource->create(['subject' => 'Test', 'database' => 123]);

    expect($id)->toBe(500);
});

it('gets emailing statistics', function () {
    $http = $this->mockHttpClient([
        ['body' => ['destinations' => 1000, 'deliveries' => 990, 'impressions' => 500, 'clicks' => 100, 'unsubscribes' => 5, 'abuses' => 1, 'errors' => 10]],
    ]);

    $resource = new EmailingResource($http, emailingId: 300, type: 'html');
    $stats = $resource->statistics();

    expect($stats)->toBeInstanceOf(EmailingStatistics::class)
        ->and($stats->destinations)->toBe(1000)
        ->and($stats->clicks)->toBe(100);
});

it('lists emailing destinations', function () {
    $http = $this->mockHttpClient([
        ['body' => ['data' => [
            ['ID' => 1, 'emailing' => 300, 'profile' => 456, 'timestampsent' => '2024-05-01 09:00:00'],
        ]]],
    ]);

    $resource = new EmailingResource($http, emailingId: 300, type: 'html');
    $result = $resource->destinations();

    expect($result)->toHaveCount(1)
        ->and($result[0])->toBeInstanceOf(EmailingDestination::class);
});

it('paginates emailing destinations', function () {
    $http = $this->mockHttpClient([
        ['body' => ['data' => [['ID' => 1, 'emailing' => 300, 'profile' => 456]], 'total' => 1]],
    ]);

    $resource = new EmailingResource($http, emailingId: 300, type: 'html');
    $result = $resource->eachDestination();

    expect($result)->toBeInstanceOf(PaginatedResponse::class);
});

it('gets emailing deliveries', function () {
    $http = $this->mockHttpClient([
        ['body' => ['data' => [['ID' => 1, 'timestamp' => '2024-05-01 09:01:00']]]],
    ]);

    $resource = new EmailingResource($http, emailingId: 300, type: 'html');
    $result = $resource->deliveries();

    expect($result)->toHaveCount(1)
        ->and($this->lastRequestUri())->toContain('html-emailing/300/deliveries');
});

it('gets emailing impressions', function () {
    $http = $this->mockHttpClient([
        ['body' => ['data' => [['ID' => 1]]]],
    ]);

    $resource = new EmailingResource($http, emailingId: 300, type: 'html');
    $result = $resource->impressions();

    expect($this->lastRequestUri())->toContain('html-emailing/300/impressions');
});

it('gets emailing clicks', function () {
    $http = $this->mockHttpClient([
        ['body' => ['data' => [['ID' => 1]]]],
    ]);

    $resource = new EmailingResource($http, emailingId: 300, type: 'html');
    $resource->clicks();

    expect($this->lastRequestUri())->toContain('html-emailing/300/clicks');
});

it('gets emailing errors', function () {
    $http = $this->mockHttpClient([
        ['body' => ['data' => []]],
    ]);

    $resource = new EmailingResource($http, emailingId: 300, type: 'html');
    $resource->errors();

    expect($this->lastRequestUri())->toContain('html-emailing/300/errors');
});

it('gets emailing unsubscribes', function () {
    $http = $this->mockHttpClient([
        ['body' => ['data' => []]],
    ]);

    $resource = new EmailingResource($http, emailingId: 300, type: 'html');
    $resource->unsubscribes();

    expect($this->lastRequestUri())->toContain('html-emailing/300/unsubscribes');
});

it('gets emailing abuses', function () {
    $http = $this->mockHttpClient([
        ['body' => ['data' => []]],
    ]);

    $resource = new EmailingResource($http, emailingId: 300, type: 'html');
    $resource->abuses();

    expect($this->lastRequestUri())->toContain('html-emailing/300/abuses');
});

it('gets emailing snapshot', function () {
    $http = $this->mockHttpClient([
        ['body' => ['html' => '<h1>Hello</h1>', 'subject' => 'Test']],
    ]);

    $resource = new EmailingResource($http, emailingId: 300, type: 'html');
    $result = $resource->snapshot();

    expect($result)->toHaveKey('html')
        ->and($this->lastRequestUri())->toContain('html-emailing/300/snapshot');
});

it('throws when accessing single emailing without ID', function () {
    $http = $this->mockHttpClient([]);

    $resource = new EmailingResource($http, type: 'html');
    $resource->get();
})->throws(LogicException::class);

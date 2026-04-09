<?php

use Budgetlens\Copernica\RestClient\DTOs\Profile;
use Budgetlens\Copernica\RestClient\DTOs\View;
use Budgetlens\Copernica\RestClient\Http\PaginatedResponse;
use Budgetlens\Copernica\RestClient\Resources\ProfileResource;
use Budgetlens\Copernica\RestClient\Resources\ViewResource;
use Budgetlens\Copernica\RestClient\Tests\Helpers\MocksHttpClient;

uses(MocksHttpClient::class);

it('lists views for a database', function () {
    $http = $this->mockHttpClient([
        ['body' => ['data' => [
            ['ID' => 20, 'name' => 'Active'],
            ['ID' => 21, 'name' => 'Inactive'],
        ]]],
    ]);

    $resource = new ViewResource($http, databaseId: 123);
    $result = $resource->list();

    expect($result)->toHaveCount(2)
        ->and($result[0])->toBeInstanceOf(View::class)
        ->and($result[0]->name)->toBe('Active');
});

it('gets a single view', function () {
    $http = $this->mockHttpClient([
        ['body' => ['ID' => 20, 'name' => 'Active', 'has-rules' => true]],
    ]);

    $resource = new ViewResource($http, viewId: 20);
    $result = $resource->get();

    expect($result)->toBeInstanceOf(View::class)
        ->and($result->hasRules)->toBeTrue();
});

it('creates a view', function () {
    $http = $this->mockHttpClient([
        ['status' => 201, 'headers' => ['Content-Type' => 'application/json', 'X-location' => 'https://api.copernica.com/v4/view/25'], 'body' => []],
    ]);

    $resource = new ViewResource($http, databaseId: 123);
    $id = $resource->create(['name' => 'Premium']);

    expect($id)->toBe(25);
});

it('updates a view', function () {
    $http = $this->mockHttpClient([
        ['status' => 204, 'headers' => ['Content-Type' => 'application/json']],
    ]);

    $resource = new ViewResource($http, viewId: 20);
    $resource->update(['name' => 'Renamed']);

    expect($this->lastRequestMethod())->toBe('PUT');
});

it('deletes a view', function () {
    $http = $this->mockHttpClient([
        ['status' => 204, 'headers' => ['Content-Type' => 'application/json']],
    ]);

    $resource = new ViewResource($http, viewId: 20);
    $resource->delete();

    expect($this->lastRequestMethod())->toBe('DELETE');
});

it('rebuilds a view', function () {
    $http = $this->mockHttpClient([
        ['status' => 200, 'body' => []],
    ]);

    $resource = new ViewResource($http, viewId: 20);
    $resource->rebuild();

    expect($this->lastRequestMethod())->toBe('POST')
        ->and($this->lastRequestUri())->toContain('view/20/rebuild');
});

it('returns ProfileResource for profiles()', function () {
    $http = $this->mockHttpClient([]);

    $resource = new ViewResource($http, viewId: 20);

    expect($resource->profiles())->toBeInstanceOf(ProfileResource::class);
});

it('paginates profiles in a view', function () {
    $http = $this->mockHttpClient([
        ['body' => ['data' => [['ID' => 1]], 'total' => 1]],
    ]);

    $resource = new ViewResource($http, viewId: 20);
    $result = $resource->eachProfile();

    expect($result)->toBeInstanceOf(PaginatedResponse::class)
        ->and($result->toArray()[0])->toBeInstanceOf(Profile::class);
});

it('gets profile IDs from a view', function () {
    $http = $this->mockHttpClient([
        ['body' => ['data' => [1, 2, 3, 4, 5]]],
    ]);

    $resource = new ViewResource($http, viewId: 20);
    $ids = $resource->profileIds();

    expect($ids)->toBe([1, 2, 3, 4, 5]);
});

it('gets view rules', function () {
    $http = $this->mockHttpClient([
        ['body' => ['data' => [['ID' => 1, 'name' => 'Is active']]]],
    ]);

    $resource = new ViewResource($http, viewId: 20);
    $rules = $resource->rules();

    expect($rules)->toHaveCount(1);
});

it('creates a view rule', function () {
    $http = $this->mockHttpClient([
        ['status' => 201, 'headers' => ['Content-Type' => 'application/json', 'X-location' => 'https://api.copernica.com/v4/rule/5'], 'body' => []],
    ]);

    $resource = new ViewResource($http, viewId: 20);
    $id = $resource->createRule(['name' => 'Is active']);

    expect($id)->toBe(5);
});

<?php

use Budgetlens\Copernica\RestClient\DTOs\Collection;
use Budgetlens\Copernica\RestClient\Resources\CollectionResource;
use Budgetlens\Copernica\RestClient\Resources\FieldResource;
use Budgetlens\Copernica\RestClient\Resources\SubprofileResource;
use Budgetlens\Copernica\RestClient\Tests\Helpers\MocksHttpClient;

uses(MocksHttpClient::class);

it('lists collections for a database', function () {
    $http = $this->mockHttpClient([
        ['body' => ['data' => [
            ['ID' => 10, 'name' => 'Orders', 'database' => 123],
            ['ID' => 11, 'name' => 'Addresses', 'database' => 123],
        ]]],
    ]);

    $resource = new CollectionResource($http, databaseId: 123);
    $result = $resource->list();

    expect($result)->toHaveCount(2)
        ->and($result[0])->toBeInstanceOf(Collection::class)
        ->and($result[0]->name)->toBe('Orders');
});

it('gets a single collection', function () {
    $http = $this->mockHttpClient([
        ['body' => ['ID' => 10, 'name' => 'Orders', 'database' => 123]],
    ]);

    $resource = new CollectionResource($http, collectionId: 10);
    $result = $resource->get();

    expect($result)->toBeInstanceOf(Collection::class)
        ->and($result->id)->toBe(10);
});

it('creates a collection', function () {
    $http = $this->mockHttpClient([
        ['status' => 201, 'headers' => ['Content-Type' => 'application/json', 'X-location' => 'https://api.copernica.com/v4/collection/15'], 'body' => []],
    ]);

    $resource = new CollectionResource($http, databaseId: 123);
    $id = $resource->create(['name' => 'Invoices']);

    expect($id)->toBe(15);
});

it('updates a collection', function () {
    $http = $this->mockHttpClient([
        ['status' => 204, 'headers' => ['Content-Type' => 'application/json']],
    ]);

    $resource = new CollectionResource($http, collectionId: 10);
    $resource->update(['name' => 'Purchase Orders']);

    expect($this->lastRequestMethod())->toBe('PUT');
});

it('returns FieldResource for fields()', function () {
    $http = $this->mockHttpClient([]);

    $resource = new CollectionResource($http, collectionId: 10);

    expect($resource->fields())->toBeInstanceOf(FieldResource::class);
});

it('returns SubprofileResource for subprofiles()', function () {
    $http = $this->mockHttpClient([]);

    $resource = new CollectionResource($http, collectionId: 10);

    expect($resource->subprofiles())->toBeInstanceOf(SubprofileResource::class)
        ->and($resource->subprofile(101))->toBeInstanceOf(SubprofileResource::class);
});

it('gets unsubscribe settings', function () {
    $http = $this->mockHttpClient([
        ['body' => ['behavior' => 'nothing']],
    ]);

    $resource = new CollectionResource($http, collectionId: 10);
    $result = $resource->unsubscribe();

    expect($result)->toHaveKey('behavior');
});

it('sets unsubscribe settings', function () {
    $http = $this->mockHttpClient([
        ['status' => 204, 'headers' => ['Content-Type' => 'application/json']],
    ]);

    $resource = new CollectionResource($http, collectionId: 10);
    $resource->setUnsubscribe(['behavior' => 'update', 'field' => 'newsletter']);

    expect($this->lastRequestMethod())->toBe('PUT');
});

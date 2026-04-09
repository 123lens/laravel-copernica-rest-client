<?php

use Budgetlens\Copernica\RestClient\DTOs\Database;
use Budgetlens\Copernica\RestClient\Resources\CollectionResource;
use Budgetlens\Copernica\RestClient\Resources\DatabaseResource;
use Budgetlens\Copernica\RestClient\Resources\FieldResource;
use Budgetlens\Copernica\RestClient\Resources\ProfileResource;
use Budgetlens\Copernica\RestClient\Resources\ViewResource;
use Budgetlens\Copernica\RestClient\Tests\Helpers\MocksHttpClient;

uses(MocksHttpClient::class);

it('lists all databases', function () {
    $http = $this->mockHttpClient([
        ['body' => ['data' => [
            ['ID' => 1, 'name' => 'DB1'],
            ['ID' => 2, 'name' => 'DB2'],
        ]]],
    ]);

    $resource = new DatabaseResource($http);
    $result = $resource->list();

    expect($result)->toHaveCount(2)
        ->and($result[0])->toBeInstanceOf(Database::class)
        ->and($result[0]->id)->toBe(1)
        ->and($result[1]->name)->toBe('DB2');
});

it('gets a single database', function () {
    $http = $this->mockHttpClient([
        ['body' => ['ID' => 123, 'name' => 'Newsletter', 'description' => 'Test']],
    ]);

    $resource = new DatabaseResource($http, 123);
    $result = $resource->get();

    expect($result)
        ->toBeInstanceOf(Database::class)
        ->id->toBe(123)
        ->name->toBe('Newsletter');
});

it('creates a database and returns the ID', function () {
    $http = $this->mockHttpClient([
        ['status' => 201, 'headers' => ['Content-Type' => 'application/json', 'X-location' => 'https://api.copernica.com/v4/database/456'], 'body' => []],
    ]);

    $resource = new DatabaseResource($http);
    $id = $resource->create(['name' => 'New DB']);

    expect($id)->toBe(456)
        ->and($this->lastRequestBody())->toBe(['name' => 'New DB']);
});

it('updates a database', function () {
    $http = $this->mockHttpClient([
        ['status' => 204, 'headers' => ['Content-Type' => 'application/json']],
    ]);

    $resource = new DatabaseResource($http, 123);
    $resource->update(['description' => 'Updated']);

    expect($this->lastRequestMethod())->toBe('PUT')
        ->and($this->lastRequestBody())->toBe(['description' => 'Updated']);
});

it('copies a database', function () {
    $http = $this->mockHttpClient([
        ['status' => 201, 'headers' => ['Content-Type' => 'application/json', 'X-location' => 'https://api.copernica.com/v4/database/789'], 'body' => []],
    ]);

    $resource = new DatabaseResource($http, 123);
    $newId = $resource->copy(['name' => 'Copy']);

    expect($newId)->toBe(789);
});

it('deletes a database', function () {
    $http = $this->mockHttpClient([
        ['status' => 204, 'headers' => ['Content-Type' => 'application/json']],
    ]);

    $resource = new DatabaseResource($http, 123);
    $resource->delete();

    expect($this->lastRequestMethod())->toBe('DELETE');
});

it('throws when getting without ID', function () {
    $http = $this->mockHttpClient([]);

    $resource = new DatabaseResource($http);
    $resource->get();
})->throws(LogicException::class);

it('returns ProfileResource for profiles()', function () {
    $http = $this->mockHttpClient([]);

    $resource = new DatabaseResource($http, 123);

    expect($resource->profiles())->toBeInstanceOf(ProfileResource::class)
        ->and($resource->profile(456))->toBeInstanceOf(ProfileResource::class);
});

it('returns FieldResource for fields()', function () {
    $http = $this->mockHttpClient([]);

    $resource = new DatabaseResource($http, 123);

    expect($resource->fields())->toBeInstanceOf(FieldResource::class);
});

it('returns ViewResource for views()', function () {
    $http = $this->mockHttpClient([]);

    $resource = new DatabaseResource($http, 123);

    expect($resource->views())->toBeInstanceOf(ViewResource::class)
        ->and($resource->view(456))->toBeInstanceOf(ViewResource::class);
});

it('returns CollectionResource for collections()', function () {
    $http = $this->mockHttpClient([]);

    $resource = new DatabaseResource($http, 123);

    expect($resource->collections())->toBeInstanceOf(CollectionResource::class)
        ->and($resource->collection(456))->toBeInstanceOf(CollectionResource::class);
});

it('gets unsubscribe settings', function () {
    $http = $this->mockHttpClient([
        ['body' => ['behavior' => 'update', 'field' => 'newsletter']],
    ]);

    $resource = new DatabaseResource($http, 123);
    $result = $resource->unsubscribe();

    expect($result)->toHaveKey('behavior')
        ->and($result['behavior'])->toBe('update');
});

it('sets unsubscribe settings', function () {
    $http = $this->mockHttpClient([
        ['status' => 204, 'headers' => ['Content-Type' => 'application/json']],
    ]);

    $resource = new DatabaseResource($http, 123);
    $resource->setUnsubscribe(['behavior' => 'update', 'field' => 'newsletter']);

    expect($this->lastRequestMethod())->toBe('PUT');
});

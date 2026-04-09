<?php

use Budgetlens\Copernica\RestClient\DTOs\Field;
use Budgetlens\Copernica\RestClient\Resources\FieldResource;
use Budgetlens\Copernica\RestClient\Tests\Helpers\MocksHttpClient;

uses(MocksHttpClient::class);

it('lists fields for a database', function () {
    $http = $this->mockHttpClient([
        ['body' => ['data' => [
            ['ID' => 1, 'name' => 'email', 'type' => 'email'],
            ['ID' => 2, 'name' => 'firstname', 'type' => 'text'],
        ]]],
    ]);

    $resource = new FieldResource($http, parentType: 'database', parentId: 123);
    $result = $resource->list();

    expect($result)->toHaveCount(2)
        ->and($result[0])->toBeInstanceOf(Field::class)
        ->and($result[0]->name)->toBe('email')
        ->and($this->lastRequestUri())->toContain('database/123/fields');
});

it('lists fields for a collection', function () {
    $http = $this->mockHttpClient([
        ['body' => ['data' => [['ID' => 1, 'name' => 'order_number']]]],
    ]);

    $resource = new FieldResource($http, parentType: 'collection', parentId: 456);
    $result = $resource->list();

    expect($this->lastRequestUri())->toContain('collection/456/fields');
});

it('creates a field', function () {
    $http = $this->mockHttpClient([
        ['status' => 201, 'headers' => ['Content-Type' => 'application/json', 'X-location' => 'https://api.copernica.com/v4/field/5'], 'body' => []],
    ]);

    $resource = new FieldResource($http, parentType: 'database', parentId: 123);
    $id = $resource->create(['name' => 'phone', 'type' => 'text']);

    expect($id)->toBe(5)
        ->and($this->lastRequestUri())->toContain('database/123/fields');
});

it('updates a field', function () {
    $http = $this->mockHttpClient([
        ['status' => 204, 'headers' => ['Content-Type' => 'application/json']],
    ]);

    $resource = new FieldResource($http, parentType: 'database', parentId: 123);
    $resource->update(5, ['name' => 'phone_number']);

    expect($this->lastRequestMethod())->toBe('PUT')
        ->and($this->lastRequestUri())->toContain('database/123/field/5');
});

it('deletes a field', function () {
    $http = $this->mockHttpClient([
        ['status' => 204, 'headers' => ['Content-Type' => 'application/json']],
    ]);

    $resource = new FieldResource($http, parentType: 'database', parentId: 123);
    $resource->delete(5);

    expect($this->lastRequestMethod())->toBe('DELETE')
        ->and($this->lastRequestUri())->toContain('database/123/field/5');
});

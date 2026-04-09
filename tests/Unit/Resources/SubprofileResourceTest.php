<?php

use Budgetlens\Copernica\RestClient\DTOs\Subprofile;
use Budgetlens\Copernica\RestClient\Http\PaginatedResponse;
use Budgetlens\Copernica\RestClient\Resources\SubprofileResource;
use Budgetlens\Copernica\RestClient\Tests\Helpers\MocksHttpClient;

uses(MocksHttpClient::class);

it('lists subprofiles for a profile', function () {
    $http = $this->mockHttpClient([
        ['body' => ['data' => [
            ['ID' => 1, 'profile' => 456, 'collection' => 789, 'fields' => ['order' => 'A']],
        ]]],
    ]);

    $resource = new SubprofileResource($http, profileId: 456);
    $result = $resource->list();

    expect($result)->toHaveCount(1)
        ->and($result[0])->toBeInstanceOf(Subprofile::class)
        ->and($this->lastRequestUri())->toContain('profile/456/subprofiles');
});

it('lists subprofiles for a collection', function () {
    $http = $this->mockHttpClient([
        ['body' => ['data' => [['ID' => 1]]]],
    ]);

    $resource = new SubprofileResource($http, collectionId: 789);
    $result = $resource->list();

    expect($this->lastRequestUri())->toContain('collection/789/subprofiles');
});

it('paginates subprofiles', function () {
    $http = $this->mockHttpClient([
        ['body' => ['data' => [['ID' => 1]], 'total' => 1]],
    ]);

    $resource = new SubprofileResource($http, profileId: 456);
    $result = $resource->each();

    expect($result)->toBeInstanceOf(PaginatedResponse::class)
        ->and($result->toArray())->toHaveCount(1);
});

it('gets a single subprofile', function () {
    $http = $this->mockHttpClient([
        ['body' => ['ID' => 101, 'profile' => 456, 'collection' => 789, 'fields' => ['amount' => '49.95']]],
    ]);

    $resource = new SubprofileResource($http, subprofileId: 101);
    $result = $resource->get();

    expect($result)->toBeInstanceOf(Subprofile::class)
        ->and($result->id)->toBe(101)
        ->and($result->field('amount'))->toBe('49.95');
});

it('creates a subprofile', function () {
    $http = $this->mockHttpClient([
        ['status' => 201, 'headers' => ['Content-Type' => 'application/json', 'X-location' => 'https://api.copernica.com/v4/subprofile/202'], 'body' => []],
    ]);

    $resource = new SubprofileResource($http, profileId: 456);
    $id = $resource->create(['fields' => ['order' => 'ORD-001']]);

    expect($id)->toBe(202)
        ->and($this->lastRequestUri())->toContain('profile/456/subprofiles');
});

it('updates a subprofile by ID', function () {
    $http = $this->mockHttpClient([
        ['status' => 204, 'headers' => ['Content-Type' => 'application/json']],
    ]);

    $resource = new SubprofileResource($http, subprofileId: 101);
    $resource->update(['fields' => ['amount' => '59.95']]);

    expect($this->lastRequestUri())->toContain('subprofile/101');
});

it('updates subprofiles in bulk by profile', function () {
    $http = $this->mockHttpClient([
        ['status' => 204, 'headers' => ['Content-Type' => 'application/json']],
    ]);

    $resource = new SubprofileResource($http, profileId: 456);
    $resource->update(['fields' => ['status' => 'archived']]);

    expect($this->lastRequestUri())->toContain('profile/456/subprofiles');
});

it('deletes a subprofile', function () {
    $http = $this->mockHttpClient([
        ['status' => 204, 'headers' => ['Content-Type' => 'application/json']],
    ]);

    $resource = new SubprofileResource($http, subprofileId: 101);
    $resource->delete();

    expect($this->lastRequestMethod())->toBe('DELETE')
        ->and($this->lastRequestUri())->toContain('subprofile/101');
});

it('gets subprofile fields', function () {
    $http = $this->mockHttpClient([
        ['body' => ['order' => 'ORD-001', 'amount' => '49.95']],
    ]);

    $resource = new SubprofileResource($http, subprofileId: 101);
    $fields = $resource->fields();

    expect($fields)->toHaveKey('order');
});

it('updates subprofile fields', function () {
    $http = $this->mockHttpClient([
        ['status' => 204, 'headers' => ['Content-Type' => 'application/json']],
    ]);

    $resource = new SubprofileResource($http, subprofileId: 101);
    $resource->updateFields(['amount' => '59.95']);

    expect($this->lastRequestMethod())->toBe('PUT')
        ->and($this->lastRequestUri())->toContain('subprofile/101/fields');
});

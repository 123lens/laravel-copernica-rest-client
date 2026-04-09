<?php

use Budgetlens\Copernica\RestClient\DTOs\Profile;
use Budgetlens\Copernica\RestClient\Http\PaginatedResponse;
use Budgetlens\Copernica\RestClient\Resources\ProfileResource;
use Budgetlens\Copernica\RestClient\Resources\SubprofileResource;
use Budgetlens\Copernica\RestClient\Tests\Helpers\MocksHttpClient;

uses(MocksHttpClient::class);

it('lists profiles in a database', function () {
    $http = $this->mockHttpClient([
        ['body' => ['data' => [
            ['ID' => 1, 'fields' => ['email' => 'a@test.com']],
            ['ID' => 2, 'fields' => ['email' => 'b@test.com']],
        ]]],
    ]);

    $resource = new ProfileResource($http, databaseId: 123);
    $result = $resource->list();

    expect($result)->toHaveCount(2)
        ->and($result[0])->toBeInstanceOf(Profile::class)
        ->and($result[0]->id)->toBe(1);
});

it('paginates profiles', function () {
    $http = $this->mockHttpClient([
        ['body' => ['data' => [['ID' => 1]], 'total' => 1]],
    ]);

    $resource = new ProfileResource($http, databaseId: 123);
    $result = $resource->each();

    expect($result)->toBeInstanceOf(PaginatedResponse::class)
        ->and($result->toArray())->toHaveCount(1)
        ->and($result->toArray()[0])->toBeInstanceOf(Profile::class);
});

it('filters profiles with where (single field)', function () {
    $http = $this->mockHttpClient([
        ['body' => ['data' => [['ID' => 1, 'fields' => ['email' => 'john@test.com']]]]],
    ]);

    $resource = new ProfileResource($http, databaseId: 123);
    $result = $resource->where('email', 'john@test.com');

    expect($result)->toHaveCount(1)
        ->and($this->lastRequestUri())->toContain('fields%5B');
});

it('filters profiles with where (multiple fields)', function () {
    $http = $this->mockHttpClient([
        ['body' => ['data' => []]],
    ]);

    $resource = new ProfileResource($http, databaseId: 123);
    $result = $resource->where(['city' => 'Amsterdam', 'newsletter' => 'yes']);

    expect($result)->toHaveCount(0);
});

it('gets a single profile', function () {
    $http = $this->mockHttpClient([
        ['body' => ['ID' => 456, 'database' => 123, 'fields' => ['email' => 'test@test.com']]],
    ]);

    $resource = new ProfileResource($http, profileId: 456);
    $result = $resource->get();

    expect($result)->toBeInstanceOf(Profile::class)
        ->and($result->id)->toBe(456)
        ->and($result->field('email'))->toBe('test@test.com');
});

it('creates a profile', function () {
    $http = $this->mockHttpClient([
        ['status' => 201, 'headers' => ['Content-Type' => 'application/json', 'X-location' => 'https://api.copernica.com/v4/profile/789'], 'body' => []],
    ]);

    $resource = new ProfileResource($http, databaseId: 123);
    $id = $resource->create(['fields' => ['email' => 'new@test.com']]);

    expect($id)->toBe(789)
        ->and($this->lastRequestMethod())->toBe('POST');
});

it('updates a single profile by ID', function () {
    $http = $this->mockHttpClient([
        ['status' => 204, 'headers' => ['Content-Type' => 'application/json']],
    ]);

    $resource = new ProfileResource($http, profileId: 456, databaseId: 123);
    $resource->update(['fields' => ['email' => 'updated@test.com']]);

    expect($this->lastRequestMethod())->toBe('PUT')
        ->and($this->lastRequestUri())->toContain('profile/456');
});

it('updates profiles in bulk by database', function () {
    $http = $this->mockHttpClient([
        ['status' => 204, 'headers' => ['Content-Type' => 'application/json']],
    ]);

    $resource = new ProfileResource($http, databaseId: 123);
    $resource->update(['fields' => ['newsletter' => 'no']]);

    expect($this->lastRequestUri())->toContain('database/123/profiles');
});

it('deletes a single profile', function () {
    $http = $this->mockHttpClient([
        ['status' => 204, 'headers' => ['Content-Type' => 'application/json']],
    ]);

    $resource = new ProfileResource($http, profileId: 456);
    $resource->delete();

    expect($this->lastRequestMethod())->toBe('DELETE')
        ->and($this->lastRequestUri())->toContain('profile/456');
});

it('gets profile fields', function () {
    $http = $this->mockHttpClient([
        ['body' => ['email' => 'test@test.com', 'city' => 'Amsterdam']],
    ]);

    $resource = new ProfileResource($http, profileId: 456);
    $fields = $resource->fields();

    expect($fields)->toHaveKey('email')
        ->and($fields['email'])->toBe('test@test.com');
});

it('updates profile fields', function () {
    $http = $this->mockHttpClient([
        ['status' => 204, 'headers' => ['Content-Type' => 'application/json']],
    ]);

    $resource = new ProfileResource($http, profileId: 456);
    $resource->updateFields(['city' => 'Rotterdam']);

    expect($this->lastRequestMethod())->toBe('PUT')
        ->and($this->lastRequestBody())->toBe(['city' => 'Rotterdam']);
});

it('gets profile interests', function () {
    $http = $this->mockHttpClient([
        ['body' => ['newsletter' => true, 'promotions' => false]],
    ]);

    $resource = new ProfileResource($http, profileId: 456);
    $interests = $resource->interests();

    expect($interests)->toHaveKey('newsletter');
});

it('updates profile interests', function () {
    $http = $this->mockHttpClient([
        ['status' => 204, 'headers' => ['Content-Type' => 'application/json']],
    ]);

    $resource = new ProfileResource($http, profileId: 456);
    $resource->updateInterests(['newsletter' => true]);

    expect($this->lastRequestMethod())->toBe('PUT');
});

it('returns SubprofileResource for subprofiles()', function () {
    $http = $this->mockHttpClient([]);

    $resource = new ProfileResource($http, profileId: 456);

    expect($resource->subprofiles())->toBeInstanceOf(SubprofileResource::class)
        ->and($resource->subprofile(101))->toBeInstanceOf(SubprofileResource::class);
});

it('throws when listing without database ID', function () {
    $http = $this->mockHttpClient([]);

    $resource = new ProfileResource($http);
    $resource->list();
})->throws(LogicException::class);

it('lists profiles from a view', function () {
    $http = $this->mockHttpClient([
        ['body' => ['data' => [['ID' => 1]]]],
    ]);

    $resource = new ProfileResource($http, viewId: 789);
    $result = $resource->list();

    expect($result)->toHaveCount(1)
        ->and($this->lastRequestUri())->toContain('view/789/profiles');
});

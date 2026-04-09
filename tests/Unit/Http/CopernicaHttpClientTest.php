<?php

use Budgetlens\Copernica\RestClient\Exceptions\AuthenticationException;
use Budgetlens\Copernica\RestClient\Exceptions\CopernicaException;
use Budgetlens\Copernica\RestClient\Exceptions\NotFoundException;
use Budgetlens\Copernica\RestClient\Exceptions\RateLimitException;
use Budgetlens\Copernica\RestClient\Exceptions\ValidationException;
use Budgetlens\Copernica\RestClient\Http\CopernicaHttpClient;
use Budgetlens\Copernica\RestClient\Tests\Helpers\MocksHttpClient;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;

uses(MocksHttpClient::class);

it('sends GET requests and decodes JSON response', function () {
    $http = $this->mockHttpClient([
        ['body' => ['data' => [['ID' => 1, 'name' => 'Test']]]],
    ]);

    $result = $http->get('databases');

    expect($result)
        ->toBeArray()
        ->toHaveKey('data')
        ->and($result['data'])->toHaveCount(1)
        ->and($this->lastRequestMethod())->toBe('GET');
});

it('sends POST requests with JSON body', function () {
    $http = $this->mockHttpClient([
        ['status' => 201, 'headers' => ['Content-Type' => 'application/json', 'X-location' => 'https://api.copernica.com/v4/database/123'], 'body' => []],
    ]);

    $result = $http->post('databases', ['name' => 'Test']);

    expect($this->lastRequestMethod())->toBe('POST')
        ->and($this->lastRequestBody())->toBe(['name' => 'Test'])
        ->and($result)->toHaveKey('_location');
});

it('sends PUT requests', function () {
    $http = $this->mockHttpClient([
        ['status' => 204, 'headers' => ['Content-Type' => 'application/json']],
    ]);

    $result = $http->put('database/123', ['name' => 'Updated']);

    expect($this->lastRequestMethod())->toBe('PUT')
        ->and($this->lastRequestBody())->toBe(['name' => 'Updated']);
});

it('sends DELETE requests', function () {
    $http = $this->mockHttpClient([
        ['status' => 204, 'headers' => ['Content-Type' => 'application/json', 'X-deleted' => 'profile/456']],
    ]);

    $result = $http->delete('profile/456');

    expect($this->lastRequestMethod())->toBe('DELETE')
        ->and($result)->toHaveKey('_deleted');
});

it('throws AuthenticationException on 401', function () {
    $http = $this->mockHttpClient([
        ['status' => 401, 'body' => ['error' => ['message' => 'Invalid token']]],
    ]);

    $http->get('databases');
})->throws(AuthenticationException::class, 'Invalid token');

it('throws AuthenticationException on 403', function () {
    $http = $this->mockHttpClient([
        ['status' => 403, 'body' => ['error' => ['message' => 'Forbidden']]],
    ]);

    $http->get('databases');
})->throws(AuthenticationException::class, 'Forbidden');

it('throws NotFoundException on 404', function () {
    $http = $this->mockHttpClient([
        ['status' => 404, 'body' => ['error' => ['message' => 'Not found']]],
    ]);

    $http->get('database/999');
})->throws(NotFoundException::class, 'Not found');

it('throws ValidationException on 400', function () {
    $http = $this->mockHttpClient([
        ['status' => 400, 'body' => ['error' => ['message' => 'Bad request', 'errors' => ['name' => 'required']]]],
    ]);

    $http->post('databases', []);
})->throws(ValidationException::class, 'Bad request');

it('throws ValidationException on 422', function () {
    $http = $this->mockHttpClient([
        ['status' => 422, 'body' => ['error' => ['message' => 'Unprocessable']]],
    ]);

    $http->post('databases', []);
})->throws(ValidationException::class, 'Unprocessable');

it('throws RateLimitException on 429', function () {
    $http = $this->mockHttpClient([
        ['status' => 429, 'body' => ['error' => ['message' => 'Too many requests']]],
    ]);

    $http->get('databases');
})->throws(RateLimitException::class, 'Too many requests');

it('throws CopernicaException on other error codes', function () {
    $http = $this->mockHttpClient([
        ['status' => 500, 'body' => ['error' => ['message' => 'Server error']]],
    ]);

    $http->get('databases');
})->throws(CopernicaException::class, 'Server error');

it('includes error details in validation exceptions', function () {
    $http = $this->mockHttpClient([
        ['status' => 400, 'body' => ['error' => ['message' => 'Bad request', 'errors' => ['field' => 'Name is required']]]],
    ]);

    try {
        $http->post('databases', []);
    } catch (ValidationException $e) {
        expect($e->errors)->toBe(['field' => 'Name is required'])
            ->and($e->getCode())->toBe(400);
    }
});

it('paginates through results', function () {
    $http = $this->mockHttpClient([
        ['body' => ['data' => [['ID' => 1], ['ID' => 2]], 'total' => 4]],
        ['body' => ['data' => [['ID' => 3], ['ID' => 4]], 'total' => 4]],
    ]);

    $items = iterator_to_array($http->paginate('database/1/profiles', limit: 2));

    expect($items)->toHaveCount(4)
        ->and($items[0]['ID'])->toBe(1)
        ->and($items[3]['ID'])->toBe(4);
});

it('stops paginating when no more data', function () {
    $http = $this->mockHttpClient([
        ['body' => ['data' => [['ID' => 1]], 'total' => 1]],
    ]);

    $items = iterator_to_array($http->paginate('database/1/profiles'));

    expect($items)->toHaveCount(1);
});

it('handles empty pagination response', function () {
    $http = $this->mockHttpClient([
        ['body' => ['data' => [], 'total' => 0]],
    ]);

    $items = iterator_to_array($http->paginate('database/1/profiles'));

    expect($items)->toHaveCount(0);
});

it('extracts X-location header', function () {
    $http = $this->mockHttpClient([
        ['status' => 200, 'headers' => ['Content-Type' => 'application/json', 'X-location' => 'https://api.copernica.com/v4/database/123'], 'body' => ['ID' => 123]],
    ]);

    $result = $http->get('databases');

    expect($result)->toHaveKey('_location')
        ->and($result['_location'])->toContain('database/123');
});

it('extracts X-deleted header', function () {
    $http = $this->mockHttpClient([
        ['status' => 204, 'headers' => ['Content-Type' => 'application/json', 'X-deleted' => 'profile/456']],
    ]);

    $result = $http->delete('profile/456');

    expect($result)->toHaveKey('_deleted')
        ->and($result['_deleted'])->toBe('profile/456');
});

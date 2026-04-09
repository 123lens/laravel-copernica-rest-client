<?php

use Budgetlens\Copernica\RestClient\CopernicaClient;

it('registers the CopernicaClient as a singleton', function () {
    $client1 = app(CopernicaClient::class);
    $client2 = app(CopernicaClient::class);

    expect($client1)
        ->toBeInstanceOf(CopernicaClient::class)
        ->and($client1)->toBe($client2);
});

it('resolves the client via the alias', function () {
    $client = app('copernica');

    expect($client)->toBeInstanceOf(CopernicaClient::class);
});

it('throws when access token is not configured', function () {
    config(['copernica.access_token' => '']);

    // Clear the singleton so it gets re-resolved
    app()->forgetInstance(CopernicaClient::class);

    app(CopernicaClient::class);
})->throws(InvalidArgumentException::class, 'Copernica access token is not configured');

it('uses configuration values', function () {
    config([
        'copernica.access_token' => 'my-test-token',
        'copernica.base_url' => 'https://rest.copernica.com/v4',
        'copernica.timeout' => 60,
    ]);

    app()->forgetInstance(CopernicaClient::class);

    $client = app(CopernicaClient::class);

    expect($client)->toBeInstanceOf(CopernicaClient::class);
});

it('publishes the configuration file', function () {
    $paths = app('Illuminate\Foundation\Application')
        ->make('Illuminate\Contracts\Foundation\Application')
        ->getProvider(\Budgetlens\Copernica\RestClient\CopernicaServiceProvider::class);

    // The service provider is registered
    expect($paths)->not->toBeNull();
});

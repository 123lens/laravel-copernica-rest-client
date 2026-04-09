<?php

namespace Budgetlens\Copernica\RestClient\Tests\Helpers;

use Budgetlens\Copernica\RestClient\Http\CopernicaHttpClient;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;

trait MocksHttpClient
{
    protected array $requestHistory = [];

    protected function mockHttpClient(array $responses): CopernicaHttpClient
    {
        $mock = new MockHandler(
            array_map(fn (array $r) => new Response(
                $r['status'] ?? 200,
                $r['headers'] ?? ['Content-Type' => 'application/json'],
                isset($r['body']) ? json_encode($r['body']) : null,
            ), $responses)
        );

        $history = Middleware::history($this->requestHistory);
        $stack = HandlerStack::create($mock);
        $stack->push($history);

        $guzzle = new Client([
            'handler' => $stack,
            'base_uri' => 'https://api.copernica.com/v4/',
            'headers' => [
                'Authorization' => 'Bearer test-token',
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);

        // Create instance without calling the constructor (avoids readonly assignment)
        $ref = new \ReflectionClass(CopernicaHttpClient::class);
        $http = $ref->newInstanceWithoutConstructor();

        // Set the readonly properties before they're "initialized"
        $clientProp = $ref->getProperty('client');
        $clientProp->setValue($http, $guzzle);

        $tokenProp = $ref->getProperty('accessToken');
        $tokenProp->setValue($http, 'test-token');

        return $http;
    }

    protected function lastRequestUri(): string
    {
        $request = end($this->requestHistory)['request'];

        return (string) $request->getUri();
    }

    protected function lastRequestMethod(): string
    {
        return end($this->requestHistory)['request']->getMethod();
    }

    protected function lastRequestBody(): array
    {
        $body = end($this->requestHistory)['request']->getBody()->getContents();

        return json_decode($body, true) ?? [];
    }
}

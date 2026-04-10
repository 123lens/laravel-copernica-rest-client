<?php

namespace Budgetlens\Copernica\RestClient\Http;

use Budgetlens\Copernica\RestClient\Exceptions\AuthenticationException;
use Budgetlens\Copernica\RestClient\Exceptions\CopernicaException;
use Budgetlens\Copernica\RestClient\Exceptions\NotFoundException;
use Budgetlens\Copernica\RestClient\Exceptions\RateLimitException;
use Budgetlens\Copernica\RestClient\Exceptions\ValidationException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

class CopernicaHttpClient
{
    private ?Client $client = null;
    private ?JSONWebToken $jwt = null;

    public function __construct(
        private readonly string $accessToken,
        private readonly string $baseUrl = 'https://api.copernica.com/v4',
        private readonly int $timeout = 30,
        private readonly string $authUrl = 'https://authenticate.copernica.com',
    ) {}

    private function refreshJwt(): void
    {
        $this->jwt = JSONWebToken::fromServer($this->accessToken, $this->authUrl);

        if ($this->jwt === null) {
            throw new CopernicaException('Failed to obtain JWT from Copernica authentication server.');
        }
    }

    private function createClient(): Client
    {
        return new Client([
            'base_uri' => rtrim($this->baseUrl, '/') . '/',
            'timeout' => $this->timeout,
            'headers' => [
                'Authorization' => "Bearer {$this->jwt->raw()}",
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    private function ensureAuthenticated(): void
    {
        if ($this->jwt !== null && !$this->jwt->expired()) {
            return;
        }

        $this->refreshJwt();
        $this->client = $this->createClient();
    }

    public function get(string $endpoint, array $query = []): array
    {
        return $this->request('GET', $endpoint, ['query' => $query]);
    }

    public function post(string $endpoint, array $data = []): array
    {
        return $this->request('POST', $endpoint, ['json' => $data]);
    }

    public function put(string $endpoint, array $data = []): array
    {
        return $this->request('PUT', $endpoint, ['json' => $data]);
    }

    public function delete(string $endpoint, array $query = []): array
    {
        return $this->request('DELETE', $endpoint, ['query' => $query]);
    }

    /**
     * @return \Generator<int, array>
     */
    public function paginate(string $endpoint, array $query = [], int $limit = 100): \Generator
    {
        $start = 0;

        do {
            $response = $this->get($endpoint, array_merge($query, [
                'start' => $start,
                'limit' => $limit,
            ]));

            $data = $response['data'] ?? [];
            $total = (int) ($response['total'] ?? 0);

            foreach ($data as $item) {
                yield $item;
            }

            $start += count($data);
        } while ($start < $total && count($data) > 0);
    }

    private function request(string $method, string $endpoint, array $options = []): array
    {
        $this->ensureAuthenticated();

        try {
            $response = $this->client->request($method, ltrim($endpoint, '/'), $options);
            $statusCode = $response->getStatusCode();

            if ($statusCode === 204) {
                return $this->extractHeaders($response);
            }

            $body = $response->getBody()->getContents();
            $decoded = json_decode($body, true) ?? [];

            return array_merge($decoded, $this->extractHeaders($response));
        } catch (ClientException $e) {
            $this->handleClientException($e);
        } catch (GuzzleException $e) {
            throw new CopernicaException(
                message: "HTTP request failed: {$e->getMessage()}",
                code: $e->getCode(),
                previous: $e,
            );
        }

        throw new \LogicException('Unreachable');
    }

    private function extractHeaders(\Psr\Http\Message\ResponseInterface $response): array
    {
        $headers = [];

        if ($response->hasHeader('X-location')) {
            $headers['_location'] = $response->getHeader('X-location')[0];
        }

        if ($response->hasHeader('X-deleted')) {
            $headers['_deleted'] = $response->getHeader('X-deleted')[0];
        }

        if ($response->hasHeader('X-Created')) {
            $headers['_created'] = (int) $response->getHeader('X-Created')[0];
        }

        return $headers;
    }

    private function handleClientException(ClientException $e): never
    {
        $response = $e->getResponse();
        $statusCode = $response->getStatusCode();
        $body = json_decode($response->getBody()->getContents(), true) ?? [];
        $message = $body['error']['message'] ?? $e->getMessage();
        $errors = $body['error']['errors'] ?? [];

        throw match ($statusCode) {
            401, 403 => new AuthenticationException($message, $statusCode, $errors, $e),
            404 => new NotFoundException($message, $statusCode, $errors, $e),
            400, 422 => new ValidationException($message, $statusCode, $errors, $e),
            429 => new RateLimitException($message, $statusCode, $errors, $e),
            default => new CopernicaException($message, $statusCode, $errors, $e),
        };
    }
}

<?php

namespace Budgetlens\Copernica\RestClient\Resources;

use Budgetlens\Copernica\RestClient\DTOs\Contracts\FromArray;
use Budgetlens\Copernica\RestClient\Exceptions\CopernicaException;
use Budgetlens\Copernica\RestClient\Http\CopernicaHttpClient;
use Budgetlens\Copernica\RestClient\Http\PaginatedResponse;

abstract class Resource
{
    public function __construct(
        protected readonly CopernicaHttpClient $http,
    ) {}

    protected function requireId(?int $id, string $resource): int
    {
        if ($id === null) {
            throw new \LogicException("A {$resource} ID is required for this operation.");
        }

        return $id;
    }

    /**
     * @template T of FromArray
     * @param class-string<T> $dtoClass
     * @return array<T>
     */
    protected function listResources(string $endpoint, string $dtoClass, array $query = []): array
    {
        $response = $this->http->get($endpoint, $query);
        $data = $response['data'] ?? [];

        return array_map(fn (array $item) => $dtoClass::fromArray($item), $data);
    }

    /**
     * @template T of FromArray
     * @param class-string<T> $dtoClass
     * @return PaginatedResponse<T>
     */
    protected function paginateResources(string $endpoint, string $dtoClass, array $query = []): PaginatedResponse
    {
        return new PaginatedResponse(
            $dtoClass,
            $this->http->paginate($endpoint, $query),
        );
    }

    /**
     * @template T of FromArray
     * @param class-string<T> $dtoClass
     * @return T
     */
    protected function getResource(string $endpoint, string $dtoClass): object
    {
        return $dtoClass::fromArray($this->http->get($endpoint));
    }

    protected function createResource(string $endpoint, array $data): int
    {
        $response = $this->http->post($endpoint, $data);

        if (isset($response['_location'])) {
            $id = (int) basename($response['_location']);
            if ($id > 0) {
                return $id;
            }
        }

        if (isset($response['ID'])) {
            return (int) $response['ID'];
        }

        throw new CopernicaException('Create succeeded but no resource ID was returned.');
    }

    protected function updateResource(string $endpoint, array $data): void
    {
        $this->http->put($endpoint, $data);
    }

    protected function deleteResource(string $endpoint): void
    {
        $this->http->delete($endpoint);
    }
}

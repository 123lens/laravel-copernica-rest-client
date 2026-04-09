<?php

namespace Budgetlens\Copernica\RestClient\Resources;

use Budgetlens\Copernica\RestClient\DTOs\Database;
use Budgetlens\Copernica\RestClient\Http\CopernicaHttpClient;

class DatabaseResource extends Resource
{
    public function __construct(
        CopernicaHttpClient $http,
        private readonly ?int $databaseId = null,
    ) {
        parent::__construct($http);
    }

    /**
     * @return array<Database>
     */
    public function list(): array
    {
        return $this->listResources('databases', Database::class);
    }

    public function get(): Database
    {
        $id = $this->requireId($this->databaseId, 'database');

        return $this->getResource("database/{$id}", Database::class);
    }

    public function create(array $data): int
    {
        return $this->createResource('databases', $data);
    }

    public function update(array $data): void
    {
        $id = $this->requireId($this->databaseId, 'database');

        $this->updateResource("database/{$id}", $data);
    }

    public function copy(array $options = []): int
    {
        $id = $this->requireId($this->databaseId, 'database');

        return $this->createResource("database/{$id}/copy", $options);
    }

    public function delete(): void
    {
        $id = $this->requireId($this->databaseId, 'database');

        $this->deleteResource("database/{$id}");
    }

    // --- Sub-resources ---

    public function profiles(): ProfileResource
    {
        $id = $this->requireId($this->databaseId, 'database');

        return new ProfileResource($this->http, databaseId: $id);
    }

    public function profile(int $profileId): ProfileResource
    {
        return new ProfileResource($this->http, profileId: $profileId, databaseId: $this->databaseId);
    }

    public function fields(): FieldResource
    {
        $id = $this->requireId($this->databaseId, 'database');

        return new FieldResource($this->http, parentType: 'database', parentId: $id);
    }

    public function views(): ViewResource
    {
        $id = $this->requireId($this->databaseId, 'database');

        return new ViewResource($this->http, databaseId: $id);
    }

    public function view(int $viewId): ViewResource
    {
        return new ViewResource($this->http, viewId: $viewId, databaseId: $this->databaseId);
    }

    public function collections(): CollectionResource
    {
        $id = $this->requireId($this->databaseId, 'database');

        return new CollectionResource($this->http, databaseId: $id);
    }

    public function collection(int $collectionId): CollectionResource
    {
        return new CollectionResource($this->http, collectionId: $collectionId, databaseId: $this->databaseId);
    }

    public function unsubscribe(): array
    {
        $id = $this->requireId($this->databaseId, 'database');

        return $this->http->get("database/{$id}/unsubscribe");
    }

    public function setUnsubscribe(array $data): void
    {
        $id = $this->requireId($this->databaseId, 'database');

        $this->updateResource("database/{$id}/unsubscribe", $data);
    }
}

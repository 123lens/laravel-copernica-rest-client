<?php

namespace Budgetlens\Copernica\RestClient\Resources;

use Budgetlens\Copernica\RestClient\DTOs\Collection;
use Budgetlens\Copernica\RestClient\Http\CopernicaHttpClient;

class CollectionResource extends Resource
{
    public function __construct(
        CopernicaHttpClient $http,
        private readonly ?int $collectionId = null,
        private readonly ?int $databaseId = null,
    ) {
        parent::__construct($http);
    }

    /**
     * @return array<Collection>
     */
    public function list(): array
    {
        $dbId = $this->requireId($this->databaseId, 'database');

        return $this->listResources("database/{$dbId}/collections", Collection::class);
    }

    public function get(): Collection
    {
        $id = $this->requireId($this->collectionId, 'collection');

        return $this->getResource("collection/{$id}", Collection::class);
    }

    public function create(array $data): int
    {
        $dbId = $this->requireId($this->databaseId, 'database');

        return $this->createResource("database/{$dbId}/collections", $data);
    }

    public function update(array $data): void
    {
        $id = $this->requireId($this->collectionId, 'collection');

        $this->updateResource("collection/{$id}", $data);
    }

    public function fields(): FieldResource
    {
        $id = $this->requireId($this->collectionId, 'collection');

        return new FieldResource($this->http, parentType: 'collection', parentId: $id);
    }

    public function subprofiles(): SubprofileResource
    {
        $id = $this->requireId($this->collectionId, 'collection');

        return new SubprofileResource($this->http, collectionId: $id);
    }

    public function subprofile(int $subprofileId): SubprofileResource
    {
        return new SubprofileResource($this->http, subprofileId: $subprofileId, collectionId: $this->collectionId);
    }

    public function unsubscribe(): array
    {
        $id = $this->requireId($this->collectionId, 'collection');

        return $this->http->get("collection/{$id}/unsubscribe");
    }

    public function setUnsubscribe(array $data): void
    {
        $id = $this->requireId($this->collectionId, 'collection');

        $this->updateResource("collection/{$id}/unsubscribe", $data);
    }
}

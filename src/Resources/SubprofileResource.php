<?php

namespace Budgetlens\Copernica\RestClient\Resources;

use Budgetlens\Copernica\RestClient\DTOs\Subprofile;
use Budgetlens\Copernica\RestClient\Http\CopernicaHttpClient;
use Budgetlens\Copernica\RestClient\Http\PaginatedResponse;

class SubprofileResource extends Resource
{
    public function __construct(
        CopernicaHttpClient $http,
        private readonly ?int $subprofileId = null,
        private readonly ?int $profileId = null,
        private readonly ?int $collectionId = null,
    ) {
        parent::__construct($http);
    }

    /**
     * @return array<Subprofile>
     */
    public function list(array $query = []): array
    {
        return $this->listResources($this->collectionEndpoint(), Subprofile::class, $query);
    }

    /**
     * @return PaginatedResponse<Subprofile>
     */
    public function each(array $query = []): PaginatedResponse
    {
        return $this->paginateResources($this->collectionEndpoint(), Subprofile::class, $query);
    }

    public function get(): Subprofile
    {
        $id = $this->requireId($this->subprofileId, 'subprofile');

        return $this->getResource("subprofile/{$id}", Subprofile::class);
    }

    public function create(array $data): int
    {
        $profileId = $this->requireId($this->profileId, 'profile');

        return $this->createResource("profile/{$profileId}/subprofiles", $data);
    }

    public function update(array $data): void
    {
        if ($this->subprofileId) {
            $this->updateResource("subprofile/{$this->subprofileId}", $data);
        } else {
            $profileId = $this->requireId($this->profileId, 'profile');
            $this->updateResource("profile/{$profileId}/subprofiles", $data);
        }
    }

    public function delete(): void
    {
        $id = $this->requireId($this->subprofileId, 'subprofile');

        $this->deleteResource("subprofile/{$id}");
    }

    public function fields(): array
    {
        $id = $this->requireId($this->subprofileId, 'subprofile');

        return $this->http->get("subprofile/{$id}/fields");
    }

    public function updateFields(array $fields): void
    {
        $id = $this->requireId($this->subprofileId, 'subprofile');

        $this->updateResource("subprofile/{$id}/fields", $fields);
    }

    private function collectionEndpoint(): string
    {
        if ($this->collectionId) {
            return "collection/{$this->collectionId}/subprofiles";
        }

        $profileId = $this->requireId($this->profileId, 'profile');

        return "profile/{$profileId}/subprofiles";
    }
}

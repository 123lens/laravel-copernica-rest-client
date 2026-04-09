<?php

namespace Budgetlens\Copernica\RestClient\Resources;

use Budgetlens\Copernica\RestClient\DTOs\Profile;
use Budgetlens\Copernica\RestClient\DTOs\View;
use Budgetlens\Copernica\RestClient\Http\CopernicaHttpClient;
use Budgetlens\Copernica\RestClient\Http\PaginatedResponse;

class ViewResource extends Resource
{
    public function __construct(
        CopernicaHttpClient $http,
        private readonly ?int $viewId = null,
        private readonly ?int $databaseId = null,
    ) {
        parent::__construct($http);
    }

    /**
     * @return array<View>
     */
    public function list(): array
    {
        $dbId = $this->requireId($this->databaseId, 'database');

        return $this->listResources("database/{$dbId}/views", View::class);
    }

    public function get(): View
    {
        $id = $this->requireId($this->viewId, 'view');

        return $this->getResource("view/{$id}", View::class);
    }

    public function create(array $data): int
    {
        $dbId = $this->requireId($this->databaseId, 'database');

        return $this->createResource("database/{$dbId}/views", $data);
    }

    public function update(array $data): void
    {
        $id = $this->requireId($this->viewId, 'view');

        $this->updateResource("view/{$id}", $data);
    }

    public function delete(): void
    {
        $id = $this->requireId($this->viewId, 'view');

        $this->deleteResource("view/{$id}");
    }

    public function rebuild(): void
    {
        $id = $this->requireId($this->viewId, 'view');

        $this->http->post("view/{$id}/rebuild");
    }

    // --- Profiles within this view ---

    public function profiles(): ProfileResource
    {
        $id = $this->requireId($this->viewId, 'view');

        return new ProfileResource($this->http, viewId: $id);
    }

    /**
     * @return PaginatedResponse<Profile>
     */
    public function eachProfile(array $query = []): PaginatedResponse
    {
        $id = $this->requireId($this->viewId, 'view');

        return $this->paginateResources("view/{$id}/profiles", Profile::class, $query);
    }

    /**
     * @return array<int>
     */
    public function profileIds(): array
    {
        $id = $this->requireId($this->viewId, 'view');

        $response = $this->http->get("view/{$id}/profileids");

        return $response['data'] ?? [];
    }

    // --- Rules ---

    public function rules(): array
    {
        $id = $this->requireId($this->viewId, 'view');

        return $this->http->get("view/{$id}/rules")['data'] ?? [];
    }

    public function createRule(array $data): int
    {
        $id = $this->requireId($this->viewId, 'view');

        return $this->createResource("view/{$id}/rules", $data);
    }
}

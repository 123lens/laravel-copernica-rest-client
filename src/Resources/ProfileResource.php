<?php

namespace Budgetlens\Copernica\RestClient\Resources;

use Budgetlens\Copernica\RestClient\DTOs\Profile;
use Budgetlens\Copernica\RestClient\Http\CopernicaHttpClient;
use Budgetlens\Copernica\RestClient\Http\PaginatedResponse;

class ProfileResource extends Resource
{
    public function __construct(
        CopernicaHttpClient $http,
        private readonly ?int $profileId = null,
        private readonly ?int $databaseId = null,
        private readonly ?int $viewId = null,
    ) {
        parent::__construct($http);
    }

    /**
     * @return array<Profile>
     */
    public function list(array $query = []): array
    {
        return $this->listResources($this->collectionEndpoint(), Profile::class, $query);
    }

    /**
     * @return PaginatedResponse<Profile>
     */
    public function each(array $query = []): PaginatedResponse
    {
        return $this->paginateResources($this->collectionEndpoint(), Profile::class, $query);
    }

    /**
     * Filter profiles by field values.
     *
     * @param array<string, string>|string $field Field name (when using two args) or associative array of field => value pairs
     */
    public function where(array|string $field, ?string $value = null): array
    {
        $conditions = is_array($field) ? $field : [$field => $value];

        $query = [];
        foreach ($conditions as $name => $val) {
            $query[] = "{$name}=={$val}";
        }

        return $this->list(['fields' => $query]);
    }

    public function get(): Profile
    {
        $id = $this->requireId($this->profileId, 'profile');

        return $this->getResource("profile/{$id}", Profile::class);
    }

    public function create(array $data): int
    {
        $dbId = $this->requireId($this->databaseId, 'database');

        return $this->createResource("database/{$dbId}/profiles", $data);
    }

    public function update(array $data): void
    {
        if ($this->profileId) {
            $this->updateResource("profile/{$this->profileId}", $data);
        } else {
            $dbId = $this->requireId($this->databaseId, 'database');
            $this->updateResource("database/{$dbId}/profiles", $data);
        }
    }

    public function delete(): void
    {
        if ($this->profileId) {
            $this->deleteResource("profile/{$this->profileId}");
        } else {
            $dbId = $this->requireId($this->databaseId, 'database');
            $this->deleteResource("database/{$dbId}/profiles");
        }
    }

    public function fields(): array
    {
        $id = $this->requireId($this->profileId, 'profile');

        return $this->http->get("profile/{$id}/fields");
    }

    public function updateFields(array $fields): void
    {
        $id = $this->requireId($this->profileId, 'profile');

        $this->updateResource("profile/{$id}/fields", $fields);
    }

    public function interests(): array
    {
        $id = $this->requireId($this->profileId, 'profile');

        return $this->http->get("profile/{$id}/interests");
    }

    public function updateInterests(array $interests): void
    {
        $id = $this->requireId($this->profileId, 'profile');

        $this->updateResource("profile/{$id}/interests", $interests);
    }

    // --- Sub-resources ---

    public function subprofiles(?int $collectionId = null): SubprofileResource
    {
        $id = $this->requireId($this->profileId, 'profile');

        return new SubprofileResource(
            $this->http,
            profileId: $id,
            collectionId: $collectionId,
        );
    }

    public function subprofile(int $subprofileId): SubprofileResource
    {
        return new SubprofileResource(
            $this->http,
            subprofileId: $subprofileId,
            profileId: $this->profileId,
        );
    }

    private function collectionEndpoint(): string
    {
        if ($this->viewId) {
            return "view/{$this->viewId}/profiles";
        }

        $dbId = $this->requireId($this->databaseId, 'database');

        return "database/{$dbId}/profiles";
    }
}

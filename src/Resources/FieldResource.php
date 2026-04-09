<?php

namespace Budgetlens\Copernica\RestClient\Resources;

use Budgetlens\Copernica\RestClient\DTOs\Field;
use Budgetlens\Copernica\RestClient\Http\CopernicaHttpClient;

class FieldResource extends Resource
{
    public function __construct(
        CopernicaHttpClient $http,
        private readonly string $parentType = 'database',
        private readonly ?int $parentId = null,
    ) {
        parent::__construct($http);
    }

    /**
     * @return array<Field>
     */
    public function list(): array
    {
        return $this->listResources("{$this->parentType}/{$this->parentId}/fields", Field::class);
    }

    public function create(array $data): int
    {
        return $this->createResource("{$this->parentType}/{$this->parentId}/fields", $data);
    }

    public function update(int $fieldId, array $data): void
    {
        $this->updateResource("{$this->parentType}/{$this->parentId}/field/{$fieldId}", $data);
    }

    public function delete(int $fieldId): void
    {
        $this->deleteResource("{$this->parentType}/{$this->parentId}/field/{$fieldId}");
    }
}

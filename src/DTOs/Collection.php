<?php

namespace Budgetlens\Copernica\RestClient\DTOs;

use Budgetlens\Copernica\RestClient\DTOs\Contracts\FromArray;

readonly class Collection implements FromArray
{
    public function __construct(
        public int $id,
        public string $name,
        public int $databaseId,
        public array $fields = [],
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            id: (int) $data['ID'],
            name: $data['name'] ?? '',
            databaseId: (int) ($data['database'] ?? 0),
            fields: $data['fields'] ?? [],
        );
    }
}

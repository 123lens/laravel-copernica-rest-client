<?php

namespace Budgetlens\Copernica\RestClient\DTOs;

use Budgetlens\Copernica\RestClient\DTOs\Concerns\ParsesDate;
use Budgetlens\Copernica\RestClient\DTOs\Contracts\FromArray;

readonly class View implements FromArray
{
    use ParsesDate;

    public function __construct(
        public int $id,
        public string $name,
        public string $description,
        public string $parentType,
        public int $parentId,
        public bool $hasRules,
        public bool $hasReferred,
        public bool $hasSearched,
        public ?\DateTimeImmutable $lastBuilt,
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            id: (int) $data['ID'],
            name: $data['name'] ?? '',
            description: $data['description'] ?? '',
            parentType: (string) ($data['parent-type'] ?? ''),
            parentId: (int) ($data['parent-id'] ?? 0),
            hasRules: (bool) ($data['has-rules'] ?? false),
            hasReferred: (bool) ($data['has-referred'] ?? false),
            hasSearched: (bool) ($data['has-searched'] ?? false),
            lastBuilt: self::parseDate($data['last-built'] ?? null),
        );
    }
}

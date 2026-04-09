<?php

namespace Budgetlens\Copernica\RestClient\DTOs;

use Budgetlens\Copernica\RestClient\DTOs\Concerns\ParsesDate;
use Budgetlens\Copernica\RestClient\DTOs\Contracts\FromArray;

readonly class Database implements FromArray
{
    use ParsesDate;

    public function __construct(
        public int $id,
        public string $name,
        public string $description,
        public bool $archived,
        public ?\DateTimeImmutable $created,
        public array $fields = [],
        public array $interests = [],
        public array $collections = [],
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            id: (int) $data['ID'],
            name: $data['name'] ?? '',
            description: $data['description'] ?? '',
            archived: (bool) ($data['archived'] ?? false),
            created: self::parseDate($data['created'] ?? null),
            fields: $data['fields'] ?? [],
            interests: $data['interests'] ?? [],
            collections: $data['collections'] ?? [],
        );
    }
}

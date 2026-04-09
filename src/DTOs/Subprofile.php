<?php

namespace Budgetlens\Copernica\RestClient\DTOs;

use Budgetlens\Copernica\RestClient\DTOs\Concerns\ParsesDate;
use Budgetlens\Copernica\RestClient\DTOs\Contracts\FromArray;

readonly class Subprofile implements FromArray
{
    use ParsesDate;

    public function __construct(
        public int $id,
        public int $profileId,
        public int $collectionId,
        public array $fields,
        public ?\DateTimeImmutable $created,
        public ?\DateTimeImmutable $modified,
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            id: (int) $data['ID'],
            profileId: (int) ($data['profile'] ?? 0),
            collectionId: (int) ($data['collection'] ?? 0),
            fields: $data['fields'] ?? [],
            created: self::parseDate($data['created'] ?? null),
            modified: self::parseDate($data['modified'] ?? null),
        );
    }

    public function field(string $name): mixed
    {
        return $this->fields[$name] ?? null;
    }
}

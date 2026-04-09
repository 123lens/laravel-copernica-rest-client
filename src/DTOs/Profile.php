<?php

namespace Budgetlens\Copernica\RestClient\DTOs;

use Budgetlens\Copernica\RestClient\DTOs\Concerns\ParsesDate;
use Budgetlens\Copernica\RestClient\DTOs\Contracts\FromArray;

readonly class Profile implements FromArray
{
    use ParsesDate;

    public function __construct(
        public int $id,
        public int $databaseId,
        public string $secret,
        public array $fields,
        public array $interests,
        public ?\DateTimeImmutable $created,
        public ?\DateTimeImmutable $modified,
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            id: (int) $data['ID'],
            databaseId: (int) ($data['database'] ?? 0),
            secret: $data['secret'] ?? '',
            fields: $data['fields'] ?? [],
            interests: $data['interests'] ?? [],
            created: self::parseDate($data['created'] ?? null),
            modified: self::parseDate($data['modified'] ?? null),
        );
    }

    public function field(string $name): mixed
    {
        return $this->fields[$name] ?? null;
    }
}

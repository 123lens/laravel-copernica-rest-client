<?php

namespace Budgetlens\Copernica\RestClient\DTOs;

use Budgetlens\Copernica\RestClient\DTOs\Contracts\FromArray;

readonly class Field implements FromArray
{
    public function __construct(
        public int $id,
        public string $name,
        public string $type,
        public mixed $value,
        public bool $displayed,
        public bool $ordered,
        public int $length,
        public int $textlines,
        public bool $hidden,
        public bool $index,
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            id: (int) $data['ID'],
            name: $data['name'] ?? '',
            type: $data['type'] ?? 'text',
            value: $data['value'] ?? null,
            displayed: (bool) ($data['displayed'] ?? false),
            ordered: (bool) ($data['ordered'] ?? false),
            length: (int) ($data['length'] ?? 0),
            textlines: (int) ($data['textlines'] ?? 0),
            hidden: (bool) ($data['hidden'] ?? false),
            index: (bool) ($data['index'] ?? false),
        );
    }
}

<?php

namespace Budgetlens\Copernica\RestClient\DTOs;

use Budgetlens\Copernica\RestClient\DTOs\Contracts\FromArray;

readonly class Webhook implements FromArray
{
    public function __construct(
        public int $id,
        public string $handler,
        public string $url,
        public string $trigger,
        public ?int $databaseId,
        public ?int $collectionId,
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            id: (int) $data['ID'],
            handler: $data['handler'] ?? '',
            url: $data['url'] ?? '',
            trigger: $data['trigger'] ?? '',
            databaseId: isset($data['database']) ? (int) $data['database'] : null,
            collectionId: isset($data['collection']) ? (int) $data['collection'] : null,
        );
    }
}

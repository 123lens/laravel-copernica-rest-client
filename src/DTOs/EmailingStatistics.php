<?php

namespace Budgetlens\Copernica\RestClient\DTOs;

use Budgetlens\Copernica\RestClient\DTOs\Contracts\FromArray;

readonly class EmailingStatistics implements FromArray
{
    public function __construct(
        public int $destinations,
        public int $deliveries,
        public int $impressions,
        public int $clicks,
        public int $unsubscribes,
        public int $abuses,
        public int $errors,
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            destinations: (int) ($data['destinations'] ?? 0),
            deliveries: (int) ($data['deliveries'] ?? 0),
            impressions: (int) ($data['impressions'] ?? 0),
            clicks: (int) ($data['clicks'] ?? 0),
            unsubscribes: (int) ($data['unsubscribes'] ?? 0),
            abuses: (int) ($data['abuses'] ?? 0),
            errors: (int) ($data['errors'] ?? 0),
        );
    }
}

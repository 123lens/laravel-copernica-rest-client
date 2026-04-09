<?php

namespace Budgetlens\Copernica\RestClient\DTOs;

use Budgetlens\Copernica\RestClient\DTOs\Concerns\ParsesDate;
use Budgetlens\Copernica\RestClient\DTOs\Contracts\FromArray;

readonly class EmailingDestination implements FromArray
{
    use ParsesDate;

    public function __construct(
        public int $id,
        public int $emailingId,
        public int $profileId,
        public ?int $subprofileId,
        public ?\DateTimeImmutable $timestampsent,
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            id: (int) $data['ID'],
            emailingId: (int) ($data['emailing'] ?? 0),
            profileId: (int) ($data['profile'] ?? 0),
            subprofileId: isset($data['subprofile']) ? (int) $data['subprofile'] : null,
            timestampsent: self::parseDate($data['timestampsent'] ?? null),
        );
    }
}

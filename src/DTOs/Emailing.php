<?php

namespace Budgetlens\Copernica\RestClient\DTOs;

use Budgetlens\Copernica\RestClient\DTOs\Concerns\ParsesDate;
use Budgetlens\Copernica\RestClient\DTOs\Contracts\FromArray;

readonly class Emailing implements FromArray
{
    use ParsesDate;

    public function __construct(
        public int $id,
        public string $type,
        public string $name,
        public string $subject,
        public string $fromAddress,
        public ?int $documentId,
        public ?int $templateId,
        public ?int $targetId,
        public string $targetType,
        public ?\DateTimeImmutable $sendTime,
        public ?EmailingStatistics $statistics = null,
    ) {}

    public static function fromArray(array $data): static
    {
        $target = is_array($data['target'] ?? null) ? $data['target'] : [];

        return new static(
            id: (int) $data['ID'],
            type: $data['type'] ?? 'html',
            name: $data['name'] ?? '',
            subject: $data['subject'] ?? '',
            fromAddress: $data['from_address'] ?? '',
            documentId: isset($data['document']) ? (int) $data['document'] : null,
            templateId: isset($data['template']) ? (int) $data['template'] : null,
            targetId: isset($target['ID']) ? (int) $target['ID'] : null,
            targetType: $target['type'] ?? '',
            sendTime: self::parseDate($data['sendtime'] ?? null),
            statistics: isset($data['statistics']) ? EmailingStatistics::fromArray($data['statistics']) : null,
        );
    }
}

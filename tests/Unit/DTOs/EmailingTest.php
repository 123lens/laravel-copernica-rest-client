<?php

use Budgetlens\Copernica\RestClient\DTOs\Emailing;
use Budgetlens\Copernica\RestClient\DTOs\EmailingStatistics;

it('creates an emailing DTO from array', function () {
    $emailing = Emailing::fromArray([
        'ID' => 300,
        'type' => 'html',
        'name' => 'Monthly newsletter',
        'subject' => 'Your monthly update',
        'from_address' => 'info@example.com',
        'document' => 50,
        'template' => 10,
        'target' => ['ID' => 123, 'type' => 'database'],
        'sendtime' => '2024-05-01 09:00:00',
    ]);

    expect($emailing)
        ->toBeInstanceOf(Emailing::class)
        ->id->toBe(300)
        ->type->toBe('html')
        ->name->toBe('Monthly newsletter')
        ->subject->toBe('Your monthly update')
        ->fromAddress->toBe('info@example.com')
        ->documentId->toBe(50)
        ->templateId->toBe(10)
        ->targetId->toBe(123)
        ->targetType->toBe('database')
        ->sendTime->toBeInstanceOf(DateTimeImmutable::class)
        ->statistics->toBeNull();
});

it('includes embedded statistics when present', function () {
    $emailing = Emailing::fromArray([
        'ID' => 1,
        'statistics' => [
            'destinations' => 1000,
            'deliveries' => 990,
            'impressions' => 500,
            'clicks' => 100,
            'unsubscribes' => 5,
            'abuses' => 1,
            'errors' => 10,
        ],
    ]);

    expect($emailing->statistics)
        ->toBeInstanceOf(EmailingStatistics::class)
        ->destinations->toBe(1000)
        ->deliveries->toBe(990)
        ->clicks->toBe(100);
});

it('handles missing target gracefully', function () {
    $emailing = Emailing::fromArray(['ID' => 1]);

    expect($emailing)
        ->targetId->toBeNull()
        ->targetType->toBe('')
        ->documentId->toBeNull()
        ->templateId->toBeNull()
        ->sendTime->toBeNull();
});

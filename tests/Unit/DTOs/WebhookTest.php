<?php

use Budgetlens\Copernica\RestClient\DTOs\Webhook;

it('creates a webhook DTO from array', function () {
    $webhook = Webhook::fromArray([
        'ID' => 42,
        'handler' => 'profile',
        'url' => 'https://example.com/webhook',
        'trigger' => 'create',
        'database' => 123,
        'collection' => 456,
    ]);

    expect($webhook)
        ->toBeInstanceOf(Webhook::class)
        ->id->toBe(42)
        ->handler->toBe('profile')
        ->url->toBe('https://example.com/webhook')
        ->trigger->toBe('create')
        ->databaseId->toBe(123)
        ->collectionId->toBe(456);
});

it('handles missing database and collection', function () {
    $webhook = Webhook::fromArray([
        'ID' => 1,
        'handler' => 'profile',
        'url' => 'https://example.com/hook',
        'trigger' => 'update',
    ]);

    expect($webhook)
        ->databaseId->toBeNull()
        ->collectionId->toBeNull();
});

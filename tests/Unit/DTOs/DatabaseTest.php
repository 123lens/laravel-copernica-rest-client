<?php

use Budgetlens\Copernica\RestClient\DTOs\Database;

it('creates a database DTO from array', function () {
    $database = Database::fromArray([
        'ID' => 123,
        'name' => 'Newsletter',
        'description' => 'Newsletter subscribers',
        'archived' => false,
        'created' => '2024-01-15 10:30:00',
        'fields' => [
            ['ID' => 1, 'name' => 'email', 'type' => 'email'],
        ],
        'interests' => ['newsletter', 'promotions'],
        'collections' => [
            ['ID' => 10, 'name' => 'Orders'],
        ],
    ]);

    expect($database)
        ->toBeInstanceOf(Database::class)
        ->id->toBe(123)
        ->name->toBe('Newsletter')
        ->description->toBe('Newsletter subscribers')
        ->archived->toBeFalse()
        ->created->toBeInstanceOf(DateTimeImmutable::class)
        ->fields->toHaveCount(1)
        ->interests->toHaveCount(2)
        ->collections->toHaveCount(1);
});

it('handles missing optional fields gracefully', function () {
    $database = Database::fromArray([
        'ID' => 1,
    ]);

    expect($database)
        ->name->toBe('')
        ->description->toBe('')
        ->archived->toBeFalse()
        ->created->toBeNull()
        ->fields->toBe([])
        ->interests->toBe([])
        ->collections->toBe([]);
});

it('handles zero date string as null', function () {
    $database = Database::fromArray([
        'ID' => 1,
        'created' => '0000-00-00 00:00:00',
    ]);

    expect($database->created)->toBeNull();
});

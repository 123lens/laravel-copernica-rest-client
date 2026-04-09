<?php

use Budgetlens\Copernica\RestClient\DTOs\Collection;

it('creates a collection DTO from array', function () {
    $collection = Collection::fromArray([
        'ID' => 10,
        'name' => 'Orders',
        'database' => 123,
        'fields' => [
            ['ID' => 1, 'name' => 'order_number'],
        ],
    ]);

    expect($collection)
        ->toBeInstanceOf(Collection::class)
        ->id->toBe(10)
        ->name->toBe('Orders')
        ->databaseId->toBe(123)
        ->fields->toHaveCount(1);
});

it('handles missing optional fields', function () {
    $collection = Collection::fromArray(['ID' => 1]);

    expect($collection)
        ->name->toBe('')
        ->databaseId->toBe(0)
        ->fields->toBe([]);
});

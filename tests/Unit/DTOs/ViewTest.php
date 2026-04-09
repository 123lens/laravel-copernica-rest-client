<?php

use Budgetlens\Copernica\RestClient\DTOs\View;

it('creates a view DTO from array', function () {
    $view = View::fromArray([
        'ID' => 20,
        'name' => 'Active subscribers',
        'description' => 'All active newsletter subscribers',
        'parent-type' => 'database',
        'parent-id' => 123,
        'has-rules' => true,
        'has-referred' => false,
        'has-searched' => false,
        'last-built' => '2024-06-01 12:00:00',
    ]);

    expect($view)
        ->toBeInstanceOf(View::class)
        ->id->toBe(20)
        ->name->toBe('Active subscribers')
        ->description->toBe('All active newsletter subscribers')
        ->parentType->toBe('database')
        ->parentId->toBe(123)
        ->hasRules->toBeTrue()
        ->hasReferred->toBeFalse()
        ->hasSearched->toBeFalse()
        ->lastBuilt->toBeInstanceOf(DateTimeImmutable::class);
});

it('handles missing optional values', function () {
    $view = View::fromArray(['ID' => 1]);

    expect($view)
        ->name->toBe('')
        ->parentType->toBe('')
        ->parentId->toBe(0)
        ->hasRules->toBeFalse()
        ->lastBuilt->toBeNull();
});

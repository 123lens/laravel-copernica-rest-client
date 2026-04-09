<?php

use Budgetlens\Copernica\RestClient\DTOs\Field;

it('creates a field DTO from array', function () {
    $field = Field::fromArray([
        'ID' => 5,
        'name' => 'email',
        'type' => 'email',
        'value' => '',
        'displayed' => true,
        'ordered' => false,
        'length' => 255,
        'textlines' => 1,
        'hidden' => false,
        'index' => true,
    ]);

    expect($field)
        ->toBeInstanceOf(Field::class)
        ->id->toBe(5)
        ->name->toBe('email')
        ->type->toBe('email')
        ->displayed->toBeTrue()
        ->ordered->toBeFalse()
        ->length->toBe(255)
        ->textlines->toBe(1)
        ->hidden->toBeFalse()
        ->index->toBeTrue();
});

it('uses defaults for missing values', function () {
    $field = Field::fromArray(['ID' => 1]);

    expect($field)
        ->name->toBe('')
        ->type->toBe('text')
        ->value->toBeNull()
        ->displayed->toBeFalse()
        ->length->toBe(0)
        ->index->toBeFalse();
});

<?php

use Budgetlens\Copernica\RestClient\DTOs\Profile;

it('creates a profile DTO from array', function () {
    $profile = Profile::fromArray([
        'ID' => 456,
        'database' => 123,
        'secret' => 'abc123',
        'fields' => [
            'email' => 'john@example.com',
            'firstname' => 'John',
            'lastname' => 'Doe',
        ],
        'interests' => ['newsletter' => true, 'promotions' => false],
        'created' => '2024-01-15 10:30:00',
        'modified' => '2024-06-20 14:00:00',
    ]);

    expect($profile)
        ->toBeInstanceOf(Profile::class)
        ->id->toBe(456)
        ->databaseId->toBe(123)
        ->secret->toBe('abc123')
        ->fields->toHaveCount(3)
        ->interests->toHaveCount(2)
        ->created->toBeInstanceOf(DateTimeImmutable::class)
        ->modified->toBeInstanceOf(DateTimeImmutable::class);
});

it('accesses individual fields via helper', function () {
    $profile = Profile::fromArray([
        'ID' => 1,
        'fields' => ['email' => 'test@test.com', 'city' => 'Amsterdam'],
    ]);

    expect($profile->field('email'))->toBe('test@test.com')
        ->and($profile->field('city'))->toBe('Amsterdam')
        ->and($profile->field('nonexistent'))->toBeNull();
});

it('handles missing optional fields', function () {
    $profile = Profile::fromArray(['ID' => 1]);

    expect($profile)
        ->databaseId->toBe(0)
        ->secret->toBe('')
        ->fields->toBe([])
        ->interests->toBe([])
        ->created->toBeNull()
        ->modified->toBeNull();
});

<?php

use Budgetlens\Copernica\RestClient\DTOs\Subprofile;

it('creates a subprofile DTO from array', function () {
    $subprofile = Subprofile::fromArray([
        'ID' => 101,
        'profile' => 456,
        'collection' => 789,
        'fields' => ['order_number' => 'ORD-001', 'amount' => '49.95'],
        'created' => '2024-03-10 08:00:00',
        'modified' => '2024-03-10 09:30:00',
    ]);

    expect($subprofile)
        ->toBeInstanceOf(Subprofile::class)
        ->id->toBe(101)
        ->profileId->toBe(456)
        ->collectionId->toBe(789)
        ->fields->toHaveCount(2);
});

it('accesses individual fields via helper', function () {
    $subprofile = Subprofile::fromArray([
        'ID' => 1,
        'fields' => ['order_number' => 'ORD-001'],
    ]);

    expect($subprofile->field('order_number'))->toBe('ORD-001')
        ->and($subprofile->field('missing'))->toBeNull();
});

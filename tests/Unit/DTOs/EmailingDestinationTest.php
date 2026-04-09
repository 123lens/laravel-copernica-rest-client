<?php

use Budgetlens\Copernica\RestClient\DTOs\EmailingDestination;

it('creates an emailing destination DTO from array', function () {
    $dest = EmailingDestination::fromArray([
        'ID' => 1,
        'emailing' => 300,
        'profile' => 456,
        'subprofile' => 101,
        'timestampsent' => '2024-05-01 09:01:00',
    ]);

    expect($dest)
        ->toBeInstanceOf(EmailingDestination::class)
        ->id->toBe(1)
        ->emailingId->toBe(300)
        ->profileId->toBe(456)
        ->subprofileId->toBe(101)
        ->timestampsent->toBeInstanceOf(DateTimeImmutable::class);
});

it('handles missing subprofile', function () {
    $dest = EmailingDestination::fromArray([
        'ID' => 1,
        'emailing' => 300,
        'profile' => 456,
    ]);

    expect($dest->subprofileId)->toBeNull();
});

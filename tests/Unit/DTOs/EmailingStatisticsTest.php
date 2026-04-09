<?php

use Budgetlens\Copernica\RestClient\DTOs\EmailingStatistics;

it('creates emailing statistics DTO from array', function () {
    $stats = EmailingStatistics::fromArray([
        'destinations' => 5000,
        'deliveries' => 4950,
        'impressions' => 2000,
        'clicks' => 500,
        'unsubscribes' => 25,
        'abuses' => 3,
        'errors' => 50,
    ]);

    expect($stats)
        ->toBeInstanceOf(EmailingStatistics::class)
        ->destinations->toBe(5000)
        ->deliveries->toBe(4950)
        ->impressions->toBe(2000)
        ->clicks->toBe(500)
        ->unsubscribes->toBe(25)
        ->abuses->toBe(3)
        ->errors->toBe(50);
});

it('defaults all values to zero', function () {
    $stats = EmailingStatistics::fromArray([]);

    expect($stats)
        ->destinations->toBe(0)
        ->deliveries->toBe(0)
        ->impressions->toBe(0)
        ->clicks->toBe(0)
        ->unsubscribes->toBe(0)
        ->abuses->toBe(0)
        ->errors->toBe(0);
});

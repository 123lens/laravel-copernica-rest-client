<?php

use Budgetlens\Copernica\RestClient\DTOs\Profile;
use Budgetlens\Copernica\RestClient\Http\PaginatedResponse;

it('iterates over paginated results as DTOs', function () {
    $generator = (function () {
        yield ['ID' => 1, 'database' => 10, 'fields' => ['email' => 'a@test.com']];
        yield ['ID' => 2, 'database' => 10, 'fields' => ['email' => 'b@test.com']];
    })();

    $response = new PaginatedResponse(Profile::class, $generator);

    $items = [];
    foreach ($response as $profile) {
        $items[] = $profile;
    }

    expect($items)->toHaveCount(2)
        ->and($items[0])->toBeInstanceOf(Profile::class)
        ->and($items[0]->id)->toBe(1)
        ->and($items[1]->id)->toBe(2);
});

it('converts to array', function () {
    $generator = (function () {
        yield ['ID' => 1];
        yield ['ID' => 2];
        yield ['ID' => 3];
    })();

    $response = new PaginatedResponse(Profile::class, $generator);

    expect($response->toArray())->toHaveCount(3)
        ->and($response->toArray()[0])->toBeInstanceOf(Profile::class);
});

it('is countable', function () {
    $generator = (function () {
        yield ['ID' => 1];
        yield ['ID' => 2];
    })();

    $response = new PaginatedResponse(Profile::class, $generator);

    expect($response)->toHaveCount(2)
        ->and(count($response))->toBe(2);
});

it('handles empty generator', function () {
    $generator = (function () {
        yield from [];
    })();

    $response = new PaginatedResponse(Profile::class, $generator);

    expect($response)->toHaveCount(0)
        ->and($response->toArray())->toBe([]);
});

it('buffers results and allows multiple iterations', function () {
    $callCount = 0;
    $generator = (function () use (&$callCount) {
        $callCount++;
        yield ['ID' => 1];
    })();

    $response = new PaginatedResponse(Profile::class, $generator);

    // First iteration triggers the generator
    $response->toArray();
    // Second iteration uses the buffer
    $response->toArray();

    expect($callCount)->toBe(1);
});

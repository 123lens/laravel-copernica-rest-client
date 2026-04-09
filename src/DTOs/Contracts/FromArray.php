<?php

namespace Budgetlens\Copernica\RestClient\DTOs\Contracts;

interface FromArray
{
    public static function fromArray(array $data): static;
}

<?php

namespace Budgetlens\Copernica\RestClient\DTOs\Concerns;

trait ParsesDate
{
    protected static function parseDate(?string $value): ?\DateTimeImmutable
    {
        if ($value === null || $value === '' || $value === '0000-00-00 00:00:00') {
            return null;
        }

        try {
            return new \DateTimeImmutable($value);
        } catch (\Exception) {
            return null;
        }
    }
}

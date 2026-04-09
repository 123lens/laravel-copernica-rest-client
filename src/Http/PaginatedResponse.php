<?php

namespace Budgetlens\Copernica\RestClient\Http;

use Budgetlens\Copernica\RestClient\DTOs\Contracts\FromArray;

/**
 * @template T of FromArray
 * @implements \IteratorAggregate<int, T>
 */
class PaginatedResponse implements \IteratorAggregate, \Countable
{
    /** @var array<T>|null */
    private ?array $buffer = null;

    /**
     * @param class-string<T> $dtoClass
     * @param \Generator<int, array> $generator
     */
    public function __construct(
        private readonly string $dtoClass,
        private readonly \Generator $generator,
    ) {}

    /**
     * @return \ArrayIterator<int, T>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->buffer());
    }

    /**
     * @return array<T>
     */
    public function toArray(): array
    {
        return $this->buffer();
    }

    public function count(): int
    {
        return count($this->buffer());
    }

    /**
     * @return array<T>
     */
    private function buffer(): array
    {
        if ($this->buffer === null) {
            $this->buffer = [];
            foreach ($this->generator as $item) {
                $this->buffer[] = ($this->dtoClass)::fromArray($item);
            }
        }

        return $this->buffer;
    }
}

<?php

namespace Budgetlens\Copernica\RestClient\Resources;

use Budgetlens\Copernica\RestClient\DTOs\Emailing;
use Budgetlens\Copernica\RestClient\DTOs\EmailingDestination;
use Budgetlens\Copernica\RestClient\DTOs\EmailingStatistics;
use Budgetlens\Copernica\RestClient\Http\CopernicaHttpClient;
use Budgetlens\Copernica\RestClient\Http\PaginatedResponse;

class EmailingResource extends Resource
{
    public function __construct(
        CopernicaHttpClient $http,
        private readonly ?int $emailingId = null,
        private readonly string $type = 'html',
    ) {
        parent::__construct($http);
    }

    // --- Listing ---

    /**
     * @return array<Emailing>
     */
    public function list(array $query = []): array
    {
        return $this->listResources("{$this->prefix()}-emailings", Emailing::class, $query);
    }

    /**
     * @return PaginatedResponse<Emailing>
     */
    public function each(array $query = []): PaginatedResponse
    {
        return $this->paginateResources("{$this->prefix()}-emailings", Emailing::class, $query);
    }

    /**
     * @return array<Emailing>
     */
    public function scheduled(array $query = []): array
    {
        return $this->listResources("{$this->prefix()}-scheduledemailings", Emailing::class, $query);
    }

    // --- Single emailing ---

    public function get(): Emailing
    {
        $id = $this->requireId($this->emailingId, 'emailing');

        return $this->getResource("{$this->prefix()}-emailing/{$id}", Emailing::class);
    }

    public function create(array $data): int
    {
        return $this->createResource("{$this->prefix()}-emailings", $data);
    }

    // --- Statistics ---

    public function statistics(): EmailingStatistics
    {
        $id = $this->requireId($this->emailingId, 'emailing');

        $response = $this->http->get("{$this->prefix()}-emailing/{$id}/statistics");

        return EmailingStatistics::fromArray($response);
    }

    // --- Destinations ---

    /**
     * @return array<EmailingDestination>
     */
    public function destinations(array $query = []): array
    {
        $id = $this->requireId($this->emailingId, 'emailing');

        return $this->listResources(
            "{$this->prefix()}-emailing/{$id}/destinations",
            EmailingDestination::class,
            $query,
        );
    }

    /**
     * @return PaginatedResponse<EmailingDestination>
     */
    public function eachDestination(array $query = []): PaginatedResponse
    {
        $id = $this->requireId($this->emailingId, 'emailing');

        return $this->paginateResources(
            "{$this->prefix()}-emailing/{$id}/destinations",
            EmailingDestination::class,
            $query,
        );
    }

    // --- Result details ---

    public function deliveries(array $query = []): array
    {
        $id = $this->requireId($this->emailingId, 'emailing');

        return $this->http->get("{$this->prefix()}-emailing/{$id}/deliveries", $query)['data'] ?? [];
    }

    public function impressions(array $query = []): array
    {
        $id = $this->requireId($this->emailingId, 'emailing');

        return $this->http->get("{$this->prefix()}-emailing/{$id}/impressions", $query)['data'] ?? [];
    }

    public function clicks(array $query = []): array
    {
        $id = $this->requireId($this->emailingId, 'emailing');

        return $this->http->get("{$this->prefix()}-emailing/{$id}/clicks", $query)['data'] ?? [];
    }

    public function errors(array $query = []): array
    {
        $id = $this->requireId($this->emailingId, 'emailing');

        return $this->http->get("{$this->prefix()}-emailing/{$id}/errors", $query)['data'] ?? [];
    }

    public function unsubscribes(array $query = []): array
    {
        $id = $this->requireId($this->emailingId, 'emailing');

        return $this->http->get("{$this->prefix()}-emailing/{$id}/unsubscribes", $query)['data'] ?? [];
    }

    public function abuses(array $query = []): array
    {
        $id = $this->requireId($this->emailingId, 'emailing');

        return $this->http->get("{$this->prefix()}-emailing/{$id}/abuses", $query)['data'] ?? [];
    }

    // --- Snapshot ---

    public function snapshot(): array
    {
        $id = $this->requireId($this->emailingId, 'emailing');

        return $this->http->get("{$this->prefix()}-emailing/{$id}/snapshot");
    }

    private function prefix(): string
    {
        return $this->type === 'draganddrop' ? 'draganddrop' : 'html';
    }
}

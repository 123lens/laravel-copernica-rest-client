<?php

namespace Budgetlens\Copernica\RestClient\Facades;

use Budgetlens\Copernica\RestClient\CopernicaClient;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array identity()
 * @method static array consumption()
 * @method static \Budgetlens\Copernica\RestClient\Resources\DatabaseResource databases()
 * @method static \Budgetlens\Copernica\RestClient\Resources\DatabaseResource database(int $id)
 * @method static \Budgetlens\Copernica\RestClient\Resources\EmailingResource emailings()
 * @method static \Budgetlens\Copernica\RestClient\Resources\EmailingResource emailing(int $id)
 * @method static \Budgetlens\Copernica\RestClient\Resources\EmailingResource dragAndDropEmailings()
 * @method static \Budgetlens\Copernica\RestClient\Resources\EmailingResource dragAndDropEmailing(int $id)
 * @method static \Budgetlens\Copernica\RestClient\Resources\WebhookResource webhooks()
 * @method static \Budgetlens\Copernica\RestClient\Resources\WebhookResource webhook(int $id)
 * @method static array get(string $endpoint, array $query = [])
 * @method static array post(string $endpoint, array $data = [])
 * @method static array put(string $endpoint, array $data = [])
 * @method static array delete(string $endpoint, array $query = [])
 *
 * @see \Budgetlens\Copernica\RestClient\CopernicaClient
 */
class Copernica extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return CopernicaClient::class;
    }
}

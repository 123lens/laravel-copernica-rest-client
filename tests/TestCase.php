<?php

namespace Budgetlens\Copernica\RestClient\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Budgetlens\Copernica\RestClient\CopernicaServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [CopernicaServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('copernica.access_token', 'test-token');
        $app['config']->set('copernica.base_url', 'https://api.copernica.com/v4');
        $app['config']->set('copernica.timeout', 30);
    }
}

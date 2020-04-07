<?php

namespace Webparking\QueueEnsurer\Tests;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Webparking\QueueEnsurer\ServiceProvider;

class TestCase extends OrchestraTestCase
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param Application $app
     *
     * @return array<int, string>
     */
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }
}

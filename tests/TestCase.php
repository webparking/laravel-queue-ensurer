<?php

namespace Webparking\QueueEnsurer\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Webparking\QueueEnsurer\ServiceProvider;

class TestCase extends OrchestraTestCase
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }
}

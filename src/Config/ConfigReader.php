<?php

namespace Webparking\QueueEnsurer\Config;

class ConfigReader
{
    public function getQueueNames(): array
    {
        return array_keys(config('queue-ensurer.queues'));
    }

    public function getConfiguredAmount(string $queueName): int
    {
        return config('queue-ensurer.queues')[$queueName];
    }
}

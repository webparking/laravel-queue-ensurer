<?php

namespace Webparking\QueueEnsurer\Config;

class ConfigReader
{
    public function getQueueNames(): array
    {
        return array_keys(config('queue-ensurer.queues'));
    }

    public function getAmount(string $queueName): int
    {
        $queueConfig = $this->getQueueConfig($queueName);

        if (\is_array($queueConfig)) {
            return $queueConfig['amount'];
        }

        return $queueConfig;
    }

    public function getConnection(string $queueName): ?string
    {
        $queueConfig = $this->getQueueConfig($queueName);

        if (\is_array($queueConfig) && \array_key_exists('connection', $queueConfig)) {
            return $queueConfig['connection'];
        }

        return null;
    }

    public function specifyQueue(string $queueName): bool
    {
        $queueConfig = $this->getQueueConfig($queueName);

        if (\is_array($queueConfig) && \array_key_exists('specify-queue', $queueConfig)) {
            return $queueConfig['specify-queue'];
        }

        return config('queue-ensurer.defaults.specify-queue');
    }

    public function getTimeout(string $queueName): int
    {
        $queueConfig = $this->getQueueConfig($queueName);

        if (\is_array($queueConfig) && \array_key_exists('timeout', $queueConfig)) {
            return $queueConfig['timeout'];
        }

        return config('queue-ensurer.defaults.timeout');
    }

    public function getSleep(string $queueName): int
    {
        $queueConfig = $this->getQueueConfig($queueName);

        if (\is_array($queueConfig) && \array_key_exists('sleep', $queueConfig)) {
            return $queueConfig['sleep'];
        }

        return config('queue-ensurer.defaults.sleep');
    }

    public function getTries(string $queueName): int
    {
        $queueConfig = $this->getQueueConfig($queueName);

        if (\is_array($queueConfig) && \array_key_exists('tries', $queueConfig)) {
            return $queueConfig['tries'];
        }

        return config('queue-ensurer.defaults.tries');
    }

    /**
     * @return array|int
     */
    private function getQueueConfig(string $queueName)
    {
        return config('queue-ensurer.queues')[$queueName];
    }
}

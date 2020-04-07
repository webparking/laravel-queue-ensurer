<?php

namespace Webparking\QueueEnsurer\Config;

class ConfigReader
{
    /** @return array<string> */
    public function getQueueNames(): array
    {
        /** @var array<string> $res */
        $res = array_keys(config('queue-ensurer.queues'));

        return $res;
    }

    public function getAmount(string $queueName): int
    {
        $queueConfig = $this->getQueueConfig($queueName);

        if (\is_array($queueConfig)) {
            return (int) $queueConfig['amount'];
        }

        return $queueConfig;
    }

    public function getPhpPath(): string
    {
        return config('queue-ensurer.php-path', 'php');
    }

    public function getConnection(string $queueName): ?string
    {
        $queueConfig = $this->getQueueConfig($queueName);

        if (\is_array($queueConfig) && \array_key_exists('connection', $queueConfig)) {
            return (string) $queueConfig['connection'];
        }

        return null;
    }

    public function specifyQueue(string $queueName): bool
    {
        $queueConfig = $this->getQueueConfig($queueName);

        if (\is_array($queueConfig) && \array_key_exists('specify-queue', $queueConfig)) {
            return (bool) $queueConfig['specify-queue'];
        }

        return config('queue-ensurer.defaults.specify-queue');
    }

    public function getTimeout(string $queueName): int
    {
        $queueConfig = $this->getQueueConfig($queueName);

        if (\is_array($queueConfig) && \array_key_exists('timeout', $queueConfig)) {
            return (int) $queueConfig['timeout'];
        }

        return config('queue-ensurer.defaults.timeout');
    }

    public function getSleep(string $queueName): int
    {
        $queueConfig = $this->getQueueConfig($queueName);

        if (\is_array($queueConfig) && \array_key_exists('sleep', $queueConfig)) {
            return (int) $queueConfig['sleep'];
        }

        return config('queue-ensurer.defaults.sleep');
    }

    public function getTries(string $queueName): int
    {
        $queueConfig = $this->getQueueConfig($queueName);

        if (\is_array($queueConfig) && \array_key_exists('tries', $queueConfig)) {
            return (int) $queueConfig['tries'];
        }

        return config('queue-ensurer.defaults.tries');
    }

    /**
     * @return array<string,int|string>|int
     */
    private function getQueueConfig(string $queueName)
    {
        return config('queue-ensurer.queues')[$queueName];
    }
}

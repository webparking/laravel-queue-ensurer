<?php

namespace Webparking\QueueEnsurer\Processes;

class ProcessManager
{
    public function isStillRunning(int $processId): bool
    {
        return posix_kill($processId, 0);
    }

    public function startProcess(
        string $queueName,
        ?string $connection,
        bool $specifyQueue,
        int $timeout,
        int $sleep,
        int $tries
    ): int {
        $command = PHP_BINARY . ' artisan queue:work';

        if (null !== $connection) {
            $command .= ' ' . $connection;
        }

        if ($specifyQueue) {
            $command .= ' --queue ' . $queueName;
        }

        $command .= ' --timeout ' . $timeout;
        $command .= ' --sleep ' . $sleep;
        $command .= ' --tries ' . $tries;

        // Suppress output and return PID
        $command .= ' > /dev/null & echo $!';

        return (int) exec(
            $command
        );
    }

    public function killProcess(int $processId): void
    {
        posix_kill($processId, 9);
    }
}

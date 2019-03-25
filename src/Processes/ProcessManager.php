<?php

namespace Webparking\QueueEnsurer\Processes;

class ProcessManager
{
    public function isStillRunning(int $processId): bool
    {
        return posix_kill($processId, 0);
    }

    public function startProcess(string $queueName): int
    {
        return (int) exec(
            'php artisan queue:work' .
            ' --queue=' . $queueName .
            ' --sleep=10 --tries=5 --timeout=0 > /dev/null & echo $!'
        );
    }

    public function killProcess(int $processId): void
    {
        posix_kill($processId, 9);
    }
}

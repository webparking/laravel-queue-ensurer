<?php

namespace Webparking\QueueEnsurer\Tests;

use Symfony\Component\Process\Process;

class EnsureProcessesCommandTest extends TestCase
{
    public function testRemovesStoppedProcessesFromFile(): void
    {
        $this->setQueueConfig([
            'default' => 0,
        ]);

        $process = $this->startProcess();
        $pid = $process->getPid();
        $process->stop();

        $this->assertNoLongerRunning($process);

        $this->writePidsFile([
            'default' => [
                $pid,
            ],
        ]);

        $this->artisan('queue:ensure-processes');

        $this->assertPidsFileEquals([
            'default' => [],
        ]);
    }

    public function testKillNoLongerConfiguredQueues(): void
    {
        $this->setQueueConfig([]);

        $process = $this->startProcess();

        $this->writePidsFile([
            'test-queue' => [
                $process->getPid(),
            ],
        ]);

        $this->artisan('queue:ensure-processes');

        $this->assertPidsFileEquals([]);
        $this->assertNoLongerRunning($process);
    }

    public function testKillsNoLongerNeededProcess(): void
    {
        $this->setQueueConfig([
            'default' => 1,
        ]);

        $process1 = $this->startProcess();
        $process2 = $this->startProcess();

        $this->writePidsFile([
            'default' => [
                $process1->getPid(),
                $process2->getPid(),
            ],
        ]);

        $this->artisan('queue:ensure-processes');

        $this->assertPidsFileEquals([
            'default' => [
                $process1->getPid(),
            ],
        ]);
        $this->assertStillRunning($process1);
        $this->assertNoLongerRunning($process2);
        $process1->stop();
    }

    public function testStartsNewProcesses(): void
    {
        $this->setQueueConfig([
            'default' => 1,
        ]);

        $this->writePidsFile([]);

        $this->artisan('queue:ensure-processes');

        $this->assertNumberOfProcessesForQueue('default', 1);
    }

    public function testStartsNewProcessesWithArrayConfig(): void
    {
        $this->setQueueConfig([
            'default' => [
                'amount' => 2,
                'connection' => 'altcon',
                'specify-queue' => true,
                'timeout' => 120,
                'sleep' => 2,
                'tries' => 2,
            ],
        ]);

        $this->writePidsFile([]);

        $this->artisan('queue:ensure-processes');

        $this->assertNumberOfProcessesForQueue('default', 2);
    }

    public function testLeaveCorrectProcessesAsIs(): void
    {
        $this->setQueueConfig([
            'default' => 1,
        ]);

        $process = $this->startProcess();

        $this->writePidsFile([
            'default' => [
                $process->getPid(),
            ],
        ]);

        $this->artisan('queue:ensure-processes');

        $this->assertPidsFileEquals([
            'default' => [
                $process->getPid(),
            ],
        ]);
        $this->assertStillRunning($process);
        $process->stop();
    }

    public function testWorkWhenNoFileExists(): void
    {
        $this->setQueueConfig([
            'default' => 1,
        ]);

        unlink(storage_path('app/queue-listener-pids.json'));

        $this->artisan('queue:ensure-processes');

        $this->assertNumberOfProcessesForQueue('default', 1);
    }

    private function writePidsFile(array $contents): void
    {
        file_put_contents(
            storage_path('app/queue-listener-pids.json'),
            json_encode($contents)
        );
    }

    private function assertPidsFileEquals(array $contents): void
    {
        $this->assertJsonStringEqualsJsonString(
            (string) json_encode($contents),
            (string) file_get_contents(storage_path('app/queue-listener-pids.json'))
        );
    }

    private function assertNumberOfProcessesForQueue(string $queueName, int $expected): void
    {
        $contents = json_decode(
            (string) file_get_contents(storage_path('app/queue-listener-pids.json')),
            true
        );

        $this->assertArrayHasKey($queueName, $contents);
        $this->assertCount($expected, $contents[$queueName]);
    }

    private function startProcess(): Process
    {
        $process = new Process(['sleep', '10']);
        $process->start();

        return $process;
    }

    private function assertNoLongerRunning(Process $process): void
    {
        $this->assertFalse(
            $process->isRunning()
        );
    }

    private function assertStillRunning(Process $process): void
    {
        $this->assertTrue(
            $process->isRunning()
        );
    }

    private function setQueueConfig(array $config): void
    {
        config()->set('queue-ensurer.queues', $config);
    }
}

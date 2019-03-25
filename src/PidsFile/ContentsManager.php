<?php

namespace Webparking\QueueEnsurer\PidsFile;

class ContentsManager
{
    private const PIDS_FILE_NAME = 'app/queue-listener-pids.json';

    /**
     * @return string[]
     */
    public function getQueueNames(): array
    {
        return array_keys($this->getFileContents());
    }

    /**
     * @return int[]
     */
    public function getPids(string $queueName): array
    {
        $fileContents = $this->getFileContents();

        if (!isset($fileContents[$queueName])) {
            return [];
        }

        return $fileContents[$queueName];
    }

    public function addPid(string $queueName, int $processId): void
    {
        $pids = $this->getPids($queueName);

        $pids[] = $processId;

        $this->updateFileForQueue($queueName, $pids);
    }

    public function removePid(string $queueName, int $processId): void
    {
        $pids = $this->getPids($queueName);

        unset($pids[array_search($processId, $pids)]);

        $this->updateFileForQueue($queueName, $pids);
    }

    public function removeQueue(string $queueName): void
    {
        $fileContents = $this->getFileContents();

        unset($fileContents[$queueName]);

        $this->writeFile($fileContents);
    }

    private function updateFileForQueue(string $queueName, array $contents): void
    {
        $fileContents = $this->getFileContents();

        $fileContents[$queueName] = $contents;

        $this->writeFile($fileContents);
    }

    private function getFileContents(): array
    {
        if (!file_exists($this->getPidsFilePath())) {
            return [];
        }

        return json_decode(
            (string) file_get_contents($this->getPidsFilePath()),
            true
        );
    }

    private function writeFile(array $contents): void
    {
        file_put_contents(
            $this->getPidsFilePath(),
            json_encode($contents)
        );
    }

    private function getPidsFilePath(): string
    {
        return storage_path(self::PIDS_FILE_NAME);
    }
}

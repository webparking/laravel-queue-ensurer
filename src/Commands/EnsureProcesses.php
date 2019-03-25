<?php

namespace Webparking\QueueEnsurer\Commands;

use Illuminate\Console\Command;
use Webparking\QueueEnsurer\Config\ConfigReader;
use Webparking\QueueEnsurer\PidsFile\ContentsManager;
use Webparking\QueueEnsurer\Processes\ProcessManager;

class EnsureProcesses extends Command
{
    protected $signature = 'queue:ensure-processes';

    protected $description = 'Ensure queue processes.';

    /**
     * @var ContentsManager
     */
    private $contentsManager;

    /**
     * @var ProcessManager
     */
    private $processManager;

    /**
     * @var ConfigReader
     */
    private $configReader;

    public function __construct(ContentsManager $contentsManager, ProcessManager $processManager, ConfigReader $configReader)
    {
        $this->contentsManager = $contentsManager;
        $this->processManager = $processManager;
        $this->configReader = $configReader;

        parent::__construct();
    }

    public function handle(): void
    {
        $this->removeStoppedProcessesFromFile();
        $this->killNoLongerConfiguredQueueProcesses();
        $this->killNoLongerNeededProcesses();
        $this->startNeededProcesses();
    }

    private function removeStoppedProcessesFromFile(): void
    {
        foreach ($this->contentsManager->getQueueNames() as $queueName) {
            foreach ($this->contentsManager->getPids($queueName) as $processId) {
                if (!$this->processManager->isStillRunning($processId)) {
                    $this->contentsManager->removePid($queueName, $processId);
                }
            }
        }
    }

    private function killNoLongerConfiguredQueueProcesses(): void
    {
        $queuesToKill = array_diff(
            $this->contentsManager->getQueueNames(),
            $this->configReader->getQueueNames()
        );

        foreach ($queuesToKill as $queueName) {
            foreach ($this->contentsManager->getPids($queueName) as $processId) {
                $this->processManager->killProcess($processId);
                $this->contentsManager->removePid($queueName, $processId);
            }

            $this->contentsManager->removeQueue($queueName);
        }
    }

    private function killNoLongerNeededProcesses(): void
    {
        foreach ($this->configReader->getQueueNames() as $queueName) {
            $configuredAmount = $this->configReader->getConfiguredAmount($queueName);
            $amountOfProcesses = \count($this->contentsManager->getPids($queueName));

            if ($configuredAmount < $amountOfProcesses) {
                foreach (\array_slice(
                    $this->contentsManager->getPids($queueName),
                    $configuredAmount - $amountOfProcesses
                ) as $processId) {
                    $this->processManager->killProcess($processId);
                    $this->contentsManager->removePid($queueName, $processId);
                }
            }
        }
    }

    private function startNeededProcesses(): void
    {
        foreach ($this->configReader->getQueueNames() as $queueName) {
            $configuredAmount = $this->configReader->getConfiguredAmount($queueName);
            $amountOfProcesses = \count($this->contentsManager->getPids($queueName));

            if ($configuredAmount > $amountOfProcesses) {
                for ($i = 1; $i <= ($configuredAmount - $amountOfProcesses); ++$i) {
                    $this->contentsManager->addPid(
                        $queueName,
                        $this->processManager->startProcess($queueName)
                    );
                }
            }
        }
    }
}

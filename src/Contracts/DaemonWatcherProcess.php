<?php

namespace Mrden\Demonizer\Contracts;

use Mrden\Demonizer\Exceptions\DemonizeException;
use Mrden\Forker\Contracts\Process;
use Mrden\Forker\Exceptions\ForkException;
use Mrden\Forker\Forker;

abstract class DaemonWatcherProcess extends DaemonProcess implements Parental
{
    /**
     * @var array<Forker>
     */
    private array $childForkers = [];

    private int $runningChildrenCount = 0;

    final public function maxCloneCount(): int
    {
        return 1;
    }

    protected function initGracefulShutdown(): void
    {
        $this->addAfterStopCallback(function () {
            foreach ($this->children() as $process) {
                $processObject = $this->createProcess($process);
                $forker = $this->childForkers[$processObject->id()] ?? null;
                $forker?->stopAll();
            }
        });
        parent::initGracefulShutdown();
    }

    /**
     * @throws DemonizeException
     * @throws ForkException
     */
    protected function job(): void
    {
        $this->runningChildrenCount = 0;
        foreach ($this->children() as $process) {
            $processObject = $this->createProcess($process);
            $this->childForkers[$processObject->id()] = new Forker($processObject);
            $count = $process['count'] ?? 1;
            $this->childForkers[$processObject->id()]->run($count);
            $this->runningChildrenCount++;
        }
    }

    protected function updateTitle(): void
    {
        $usedMemoryBytes = \memory_get_usage(true);
        $usedMemoryMb = \round($usedMemoryBytes / 1024 / 1024, 2);
        \cli_set_process_title(\sprintf(
            '%s [children %d, memory %sMb]',
            $this->getTitle(),
            $usedMemoryMb,
            $this->runningChildrenCount
        ));
    }

    /**
     * @psalm-param array{process:class-string<ChildProcess>, params?:array} $process
     * @throws DemonizeException
     */
    private function createProcess(array $process): Process
    {
        if (!isset($process['process'])) {
            throw new DemonizeException('Incorrect process config');
        }
        if (!\class_exists($process['process'])) {
            throw new DemonizeException('Not found process ' . $process['process']);
        }
        if (!\is_subclass_of($process['process'], ChildProcess::class)) {
            throw new DemonizeException('Incorrect implementation child process ' . $process['process']);
        }
        return new $process['process'](
            $process['params'] ?? [],
            $this->getProcessManager(),
            \get_class($this->getPidStorage()),
            $this->getProcessManager()->getCurrentPid()
        );
    }
}

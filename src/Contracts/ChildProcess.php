<?php

namespace Mrden\Demonizer\Contracts;

use Mrden\Forker\Contracts\Process;
use Mrden\Forker\Contracts\Titled;

/**
 * @psalm-consistent-constructor
 */
abstract class ChildProcess extends Process implements Titled
{
    private Parental&Process $parent;

    public function __construct(Parental&Process $parentProcess, array $params = [])
    {
        $this->parent = $parentProcess;
        parent::__construct($params);
    }

    public function run(int $cloneNumber = 1): void
    {
        $this->parent->setIsChildContext(true);
        parent::run($cloneNumber);
    }

    public function getTitle(): string
    {
        $title = ($this->nameProcess ?? \get_class($this)) . $this->paramsToString();
        $parentPid = $this->parent->pid();
        if ($parentPid === null) {
            return $title;
        }
        return \sprintf('%s => %s', $parentPid, $title);
    }
}

<?php

namespace Mrden\Demonizer\Contracts;

use Mrden\Forker\Contracts\Process;

abstract class ChildProcess extends Process
{
    /**
     * @var Parental|Process|null
     */
    private $parentProcess;

    public function __construct(array $params = [], ?Parental $parentProcess = null)
    {
        $this->parentProcess = $parentProcess;
        parent::____construct($params);
    }

    public function run(int $cloneNumber = 1): void
    {
        if ($this->parentProcess) {
            $this->parentProcess->setIsChildContext(true);
        }
        parent::run($cloneNumber);
    }

    protected function title(): string
    {
        $title = \get_class($this) . ($this->params ? ' ' . $this->paramToString() : '');
        if ($this->parentProcess) {
            $parentPid = $this->parentProcess->pid();
            if ($parentPid) {
                $title = "$parentPid => $title";
            }
        }
        return $title;
    }
}

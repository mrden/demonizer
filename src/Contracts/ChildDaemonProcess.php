<?php

namespace Mrden\Demonizer\Contracts;

abstract class ChildDaemonProcess extends ChildProcess
{
    use DaemonProcessTrait;

    abstract protected function job(): void;
}

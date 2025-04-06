<?php

namespace Mrden\Demonizer\Contracts;

use Mrden\Forker\Contracts\Process;

abstract class MainDaemonProcess extends Process
{
    use DaemonProcessTrait;

    abstract protected function job(): void;
}

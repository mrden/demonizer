<?php

namespace Tests;

use Mrden\Forker\Forker;
use PHPUnit\Framework\TestCase;

class DemonizerTest extends TestCase
{
    public function testRestorePidInStorage()
    {
        $process = new TestMainDaemonProcess(['test-param' => 50]);
        $forker = new Forker($process);
        $pids = $forker->run();
        $pidFile = __DIR__ . '/../.mrden/forker/' . $process->id() . '/1.storage';
        \sleep(1);
        if (\file_exists($pidFile)) {
            \unlink($pidFile);
        }
        $this->assertTrue($process->pid(1) == 0);
        \sleep(5);
        $pid = $process->pid(1);
        $this->assertTrue($pid > 0 && $pid == $pids[0]);
        $forker->stop(Forker::STOP_ALL);
        \sleep(3);
    }
}

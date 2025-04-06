# Php process demonizer

## Install

`composer require mrden/demonizer`

## Fork single daemon process in 3 clones

```php
namespace Any;

class SingleDaemonProcess extends \Mrden\Demonizer\Contracts\MainDaemonProcess
{
    use \Mrden\Forker\Traits\FilePidStorageTrait;
    
    /**
     * in sec
     */
    protected $period = 5;
    
    protected function job(): void
    {
        echo 'I\'m the code of iteration daemon process';
    }
    
    protected function checkParams(): void
    {
    }

    protected function prepare(): void
    {
    }
}
```
### Start in code

```php
$singleProcess = new \Any\SingleDaemonProcess();
$forker = new \Mrden\Forker\Forker($singleProcess);
$forker->run(3);
```
### Start via `bin/forker`
`php bin/forker --process="\Any\SingleDaemonProcess" --count=3`

### Stop via `bin/forker`
`php bin/forker --process="\Any\SingleDaemonProcess" --stop=1`

### Stop only 2 clones via `bin/forker`
`php bin/forker --process="\Any\SingleDaemonProcess" --stop=1 --count=2`

### Stop only 2-nd clone via `bin/forker`
`php bin/forker --process="\Any\SingleDaemonProcess" --stop=1 --clone_number=2`

### Restart all clones via `bin/forker`
`php bin/forker --process="\Any\SingleDaemonProcess" --restart=1`

### Restart only 2 clones via `bin/forker`
`php bin/forker --process="\Any\SingleDaemonProcess" --restart=1 --count=2`

### Restart only 2-nd clone via `bin/forker`
`php bin/forker --process="\Any\SingleDaemonProcess" --restart=1 --clone_number=2`

## Start single daemon watcher process

```php
namespace Any;

class SingleProcess extends \Mrden\Demonizer\Contracts\ChildProcess
{
    use \Mrden\Forker\Traits\FilePidStorageTrait;

    public function execute(): void
    {
        echo 'context from parent process ' . ($this->params['context'] ?? '');
    }

    protected function prepare(): void
    {
    }
    
    protected function checkParams(): void
    {
    }
}

class SingleDaemonProcess extends \Mrden\Demonizer\Contracts\ChildDaemonProcess
{
    use \Mrden\Forker\Traits\FilePidStorageTrait;
    
    /**
     * in sec
     */
    protected $period = 5;
    
    protected function job(): void
    {
        echo 'I\'m the code of iteration daemon process';
    }
    
    protected function checkParams(): void
    {
    }

    protected function prepare(): void
    {
    }
}

class SingleDaemonWatcherProcess extends \Mrden\Demonizer\Contracts\DaemonWatcherProcess
{
    use \Mrden\Forker\Traits\ProcessFileStorageTrait;
    
    protected function processes(): array
    {
        return return [
            [
                'process' => \Any\SingleProcess::class,
                'params' => [
                    'time' => 11,
                ],
                'count' => 1,
            ],
            [
                'process' => \Any\SingleDaemonProcess::class,
                'count' => 2,
            ],
        ];
    }

    protected function prepare(): void
    {
    }
}
```
Daemon watcher process forked only in 1 clone.

### Start in code

```php
$singleProcess = new \Any\SingleDaemonWatcherProcess();
$forker = new \Mrden\Forker\Forker($singleProcess);
$forker->run();
```

### Start via `bin/forker`
`php bin/forker --process="\Any\SingleDaemonWatcherProcess"`

### Stop via `bin/forker`
`php bin/forker --process="\Any\SingleDaemonWatcherProcess" --stop=1`
will be stopped all (self and child process)

### Stop via `kill`
`kill PID` - will be stopped all (self and child process)

### Restart daemon watcher and all children processes vai `bin/forker`
`php bin/forker --process="\Any\SingleDaemonWatcherProcess" --restart=1`
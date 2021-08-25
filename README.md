# Laravel Logger

**Log seperate folder by level build on monolog/monolog**

## Installation

```bash
$ composer require bluebik/logger
```

## Usage

```php
use Bluebik\Logger\LoggerFactory;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $logger;

    public function __construct()
    {
        $this->logger = LoggerFactory::create('action');
    }

    public function index()
    {
        $this->logger->action(__METHOD__, "start");
        ...
    }
}
```


## Components
There are 3 components including

- Bluebik\Logger\Commands\LogBackup - Command for backup and archive log daily
- Bluebik\Logger\Middleware\AccessLogMiddleware - Middleware to handle access log
- Bluebik\Logger\Traits\CommandLogger - Trait for creating logger of command class

### Log Backup Configuration

Edit in app\Console\Kernel.php

```php
protected $commands = [
    \Bluebik\Logger\Commands\LogBackup::class,
    ...
];


protected function schedule(Schedule $schedule) {
    $schedule->command('log:backup')->daily();
    ...
}
```

### AccessLogMiddleware Configuration
Edit in app\Http\Kernel.php
```php
protected $middleware = [
    \Bluebik\Logger\Middleware\AccessLogMiddleware::class,
    ...
];
```

### CommandLogger Usage
```php
use Bluebik\Logger\Traits\CommandLogger;

class CommandClass extends Command {
    use CommandLogger;

    public function handle()
    {
        $this->logger->action(__METHOD__, "start");
        ...
    }
}
```

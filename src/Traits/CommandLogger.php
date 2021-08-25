<?php

namespace Bluebik\Logger\Traits;

use Bluebik\Logger\LoggerFactory;
use Illuminate\Support\Str;

trait CommandLogger
{

    protected $logger;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->logger = LoggerFactory::create('command');
        $this->logger->setRequestId(time() . Str::random(6));
    }
}

<?php

namespace Bluebik\Logger;

use Illuminate\Support\Facades\Facade;

class LoggerFacade extends Facade {
    protected static function getFacadeAccessor() {
        return 'LoggerFactory';
    }
}
<?php

namespace Bluebik\Logger;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Illuminate\Support\Facades\Request;

/**
 * Class LoggerFactory
 *
 * @package \Bluebik\Logger
 */
class LoggerFactory
{

    /**
     * @param $name
     *
     * @return Logger
     */
    public static function create($name)
    {
        $logger = new Logger($name);
        $streamHandler = self::streamHandler($name);
        $logger->pushHandler($streamHandler);
        $logger->setRequestId(Request::header('x-request-id'));
        return $logger;
    }

    /**
     * @param $name
     *
     * @return \Monolog\Handler\StreamHandler;
     */
    public static function streamHandler($name)
    {
        $suffix = date('Y-m-d-H');
        $date = date('Y-m-d');
        $pathName = str_replace('.', '/', $name);
        $streamHandler = new StreamHandler(storage_path("logs/$date/$pathName/$name-$suffix.log"));
        $streamHandler->setFormatter(new LineFormatter(null, null, true, false));
        return $streamHandler;
    }

}

<?php

namespace Bluebik\Logger;

use Monolog\Formatter\LineFormatter;
use Monolog\Formatter\GelfMessageFormatter;
use Monolog\Handler\StreamHandler;
use Illuminate\Support\Facades\Request;
use Monolog\Handler\GelfHandler;
use Gelf\Publisher;
use Gelf\Transport\UdpTransport;

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

        if(config('logger.logstash.enabled')){
            $gelfHandler = self::logStashHandler();
            $logger->pushHandler($gelfHandler);
        }

        $requestId = Request::header('x-request-id');
        $logger->setRequestId($requestId);

        $logger->pushProcessor(function($record) use ($requestId){
            $record['extra']['request_id'] = $requestId;
            return $record;
        });

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

    /**
     *
     * @return \Monolog\Handler\GelfHandler;
     */
    public static function logStashHandler()
    {
        $logstashConfig = config('logger.logstash');
        $gelfHandler = new GelfHandler(new Publisher(new UdpTransport($logstashConfig['host'], $logstashConfig['port'])));
        $gelfHandler->setFormatter(new GelfMessageFormatter($logstashConfig['system_name']));
        return $gelfHandler;
    }

}

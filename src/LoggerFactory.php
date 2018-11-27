<?php

namespace Bluebik\Logger;

use Monolog\Formatter\LineFormatter;
use Monolog\Formatter\GelfMessageFormatter;
use Monolog\Handler\StreamHandler;
use Illuminate\Support\Facades\Request;
use Monolog\Handler\GelfHandler;
use Gelf\Publisher;
use Gelf\Transport\UdpTransport;
use Gelf\Transport\TcpTransport;

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

        $localLogConfig = config('logger.local_log');
        if($localLogConfig['enabled'] && !in_array($name, $localLogConfig['exclude_names'])) {
            $streamHandler = self::streamHandler($name);
            $logger->pushHandler($streamHandler);
        }

        $logstashConfig = config('logger.logstash');
        if($logstashConfig['enabled'] && !in_array($name, $logstashConfig['exclude_names'])){
            $gelfHandler = self::logStashHandler($logstashConfig);
            $logger->pushHandler($gelfHandler);
        }

        $requestId = Request::header('x-request-id');
        $logger->setRequestId($requestId);

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
    public static function logStashHandler($logstashConfig)
    {
        if($logstashConfig['protocol'] == 'UDP'){
            $transport = new UdpTransport($logstashConfig['host'], $logstashConfig['port']);
        }
        else{
            $transport = new TcpTransport($logstashConfig['host'], $logstashConfig['port']);
        }

        $gelfHandler = new GelfHandler(new Publisher($transport));
        $gelfHandler->setFormatter(new GelfMessageFormatter($logstashConfig['system_name']));
        return $gelfHandler;
    }

}

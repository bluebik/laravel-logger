<?php

namespace Bluebik\Logger;

/**
 * Class Logger
 *
 * @package \Bluebik\Logger
 */
class Logger extends \Monolog\Logger
{
    protected $requestId;

    /**
     * @param         $action
     * @param         $message
     * @param array   $context
     * @param boolean $debug
     */
    public function action($action, $message, array $context = array(), $debug = false)
    {

        $message = "$action $message";

        $hiddenFields = config('logger.hidden_fields');
        foreach ($context as $key => $values) {
            if (is_array($values)) {
                $collection = collect($values);
                if ($key != 'errors') {
                    $collection = $collection->except($hiddenFields['all']);
                }

                if (in_array($key, array_keys($hiddenFields))) {
                    $collection = $collection->except($hiddenFields[$key]);
                }
                $context[$key] = $collection->all();
            }
        }

        if ($debug) {
            $this->debug($message, $context);
        } else {
            $this->info($message, $context);
        }

    }

    public function error($message, array $context = array())
    {
        return parent::error("{$this->requestId} $message", $context);
    }

    public function info($message, array $context = array())
    {
        return parent::info("{$this->requestId} $message", $context);
    }

    public function debug($message, array $context = array())
    {
        return parent::debug("{$this->requestId} $message", $context);
    }

    /**
     * @param mixed $requestId
     */
    public function setRequestId($requestId)
    {
        $this->requestId = $requestId;
    }

}
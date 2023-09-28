<?php

namespace TestBlocks\Helpers;

use TestBlocks\Contracts\LoggerInterface;
use TestBlocks\Core;

/**
 * Class LoggerHelper
 * @package TestBlocks\Helpers
 */
class LoggerHelper implements LoggerInterface
{
    /**
     * If WP_DEBUG_LOG enabled
     * @var bool
     */
    private $debugLog;

    /**
     * LoggerHelper constructor.
     */
    public function __construct()
    {
        $this->debugLog = Core::DEBUG_LOG_ENABLED;
    }

    /**
     * @inheritDoc
     */
    public function log($message, $code)
    {
        if (! $this->debugLog) {
            return;
        }

        error_log(
            sprintf(
                'Message: %s. Code: %s.',
                $message,
                $code
            )
        );
    }
}

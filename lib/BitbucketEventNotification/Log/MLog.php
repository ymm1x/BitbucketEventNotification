<?php
namespace BitbucketEventNotification\Log;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Monolog logger instance creator. (singleton)
 *
 * @package BitbucketEventNotification\Log
 */
class MLog
{
    /**
     * @var Logger $instance
     */
    private static $instance;

    /**
     * Invalid constructor.
     */
    private function __construct()
    {
    }

    /**
     * Get Monolog instance. (singleton)
     *
     * @return Logger instance
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::initInstance();
        }

        return self::$instance;
    }

    /**
     * Initial Monolog instance (for when want self initialize.)
     *
     * @param int $level log level for Monolog (Default: Logger::WARNING)
     * @param string $loggingPath logging path
     * @return Logger instance
     */
    public static function initInstance($level = Logger::WARNING, $loggingPath = null)
    {
        if (!$loggingPath) {
            $loggingPath = APP_DIR . '/tmp/logs/error.log';
        }

        self::$instance = new Logger('general');
        self::$instance->pushHandler(new StreamHandler($loggingPath, $level));
    }
}
<?php

namespace Core;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class LoggerUtility
{
    private static $logger;

    public static function init()
    {
        if (self::$logger === null) {
            self::$logger = new Logger('api_logger');
            self::$logger->pushHandler(new StreamHandler(__DIR__ . '/../logs/api.log', Logger::DEBUG));
        }
    }

    public static function logInput($data)
    {
        self::init();
        self::$logger->info('Received input data: ' . json_encode($data));
    }

    // إضافة الدالة logMessage
    public static function logMessage($level, $message)
    {
        self::init();  // تأكد من تهيئة logger
        self::$logger->$level($message);
    }
}

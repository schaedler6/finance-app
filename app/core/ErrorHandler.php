<?php

namespace App\Core;

class ErrorHandler
{
    public static function handleException($exception)
    {
        http_response_code(500);
        echo json_encode([
            "error" => true,
            "message" => $exception->getMessage()
        ]);
    }

    public static function handleError($errno, $errstr, $errfile, $errline)
    {
        throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    public static function register()
    {
        set_exception_handler([self::class, 'handleException']);
        set_error_handler([self::class, 'handleError']);
    }
}

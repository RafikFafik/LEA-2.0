<?php

declare(strict_types=1);

namespace Lea\Core\Logger;

final class Logger
{
    public static function save($message): void
    {
        if(is_object($message))
            $message = $message->getMessage();
        file_put_contents(__DIR__ . '/../../../logs/error.log', "======================" . "\n", FILE_APPEND);
        file_put_contents(__DIR__ . '/../../../logs/error.log', date('Y-M-D H:i:s') . "\n", FILE_APPEND);
        file_put_contents(__DIR__ . '/../../../logs/error.log', json_encode($message, JSON_PRETTY_PRINT) . "\n", FILE_APPEND);
        file_put_contents(__DIR__ . '/../../../logs/error.log', "======================" . "\n", FILE_APPEND);
    }

    public static function primitiveLog(string $filename): void
    {
        $headers = getallheaders();
        $req_dump = file_get_contents('php://input');
        $fp = file_put_contents(__DIR__ . '/../../../logs/' . $filename . '.log', "======================" . "\n", FILE_APPEND);
        $fp = file_put_contents(__DIR__ . '/../../../logs/' . $filename . '.log', date('Y-M-D H:i:s') . "\n", FILE_APPEND);
        $fp = file_put_contents(__DIR__ . '/../../../logs/' . $filename . '.log', json_encode($headers, JSON_PRETTY_PRINT) . "\n", FILE_APPEND);
        $fp = file_put_contents(__DIR__ . '/../../../logs/' . $filename . '.log', json_encode($_POST, JSON_PRETTY_PRINT) . "\n", FILE_APPEND);
        $fp = file_put_contents(__DIR__ . '/../../../logs/' . $filename . '.log', $req_dump . "\n", FILE_APPEND);
        $fp = file_put_contents(__DIR__ . '/../../../logs/' . $filename . '.log', "======================" . "\n", FILE_APPEND);
    }
}

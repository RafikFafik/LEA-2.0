<?php

namespace Lea\Response;

class Response
{
    private static function m_die($message = null): void
    {
        if (is_array($message)) {
            if (!headers_sent())
                header('Content-Type: application/json');
            die(json_encode($message, JSON_UNESCAPED_UNICODE));
        } else {
            if (!headers_sent())
                header('Content-Type: text/plain; charset=UTF-8');
            die($message);
        }
    }

    public static function badRequest($message = null): void
    {
        http_response_code(400);
        self::m_die($message);
    }

    public static function forbidden($message = null): void
    {
        http_response_code(403);
        self::m_die($message);
    }

    public static function notFound($message = null): void
    {
        http_response_code(404);
        self::m_die($message);
    }

    public static function methodNotAllowed($message = null): void
    {
        http_response_code(405);
        self::m_die($message);
    }

    public static function noContent($message = null): void
    {
        http_response_code(204);
        self::m_die($message);
    }

    public static function resourceDoesNotExists($message = null): void
    {
        http_response_code(200);
        self::m_die($message);
    }

    public static function ok($data = null): void
    {
        http_response_code(200);
        self::m_die($data);
    }

    public static function internalServerError($message = null): void
    {
        http_response_code(500);
        self::m_die($message);
    }

    public static function badGateway($message = null): void
    {
        http_response_code(502);
        self::m_die($message);
    }

    public static function notImplemented($message = null): void
    {
        http_response_code(501);
        self::m_die($message);
    }


    public static function unauthorized($message = null): void
    {
        http_response_code(401);
        self::m_die($message);
    }

    public static function accepted($message = null): void
    {
        http_response_code(202);
        self::m_die($message);
    }

    public static function file($message = null): void
    {
        http_response_code(200);
        self::m_die($message);
    }
}

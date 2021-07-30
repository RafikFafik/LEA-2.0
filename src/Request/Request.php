<?php

declare(strict_types=1);

namespace Lea\Request;

use Lea\Core\Logger\Logger;
use Lea\Response\Response;

final class Request
{
    private $data;

    private $server;

    public $payload;

    private static $pagination = null;
    private static $filters = null;
    private static $custom_params = null;

    const APPLICATION_JSON = "application/json";
    const MULTIPART_FORM_DATA_APP = "multipart/form-data; boundary=--dio-boundary";
    // const MULTIPART_FORM_DATA_WEB = "multipart/form-data; boundary=----WebKitFormBoundary"; <-- Only for Edge
    const MULTIPART_FORM_DATA_WEB = "multipart/form-data;"; /* Workaround for all browsers */

    public function __construct()
    {
        $this->data = $_REQUEST;
        $this->server = $_SERVER;
        $this->parseRequestPayload();
    }

    public function get(string $key)
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        return null;
    }

    public function getPayload(): array
    {
        return $this->payload ?? [];
    }

    public function set(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    public function data(): array
    {
        return $this->data;
    }

    public function url(): string
    {
        return $this->server['REQUEST_URI'];
    }

    public function method(): string
    {
        return $this->server['REQUEST_METHOD'];
    }

    public function action(): string
    {
        return explode("/", $this->url())[0];
    }

    public function getURLTokens()
    {
        return "";
    }

    private function parseRequestPayload(): void
    {
        if (strtoupper($this->server['REQUEST_METHOD']) == "GET") {
            $this->payload = null;
            return;
        }

        if (str_starts_with($_SERVER['CONTENT_TYPE'], self::MULTIPART_FORM_DATA_APP)) {
            Logger::primitiveLog("request-app");
            $this->parsePOST();
        } elseif (str_starts_with($_SERVER['CONTENT_TYPE'], self::MULTIPART_FORM_DATA_WEB)) {
            $this->parsePOST();
            Logger::primitiveLog("request-web");
        } elseif (str_starts_with($_SERVER['CONTENT_TYPE'], self::APPLICATION_JSON)) {
            $this->parseJSON();
        } else {
            Response::badRequest("Content-Type " . $_SERVER['CONTENT_TYPE'] . " not allowed");
        }
    }

    private function parseJSON(): void
    {
        $json = file_get_contents('php://input');
        $this->payload = json_decode($json, TRUE);
    }

    private function parsePOST(): void
    {
        foreach ($_POST as $key => $val) {
            $parsed = $array = json_decode($val, true);
            if (is_array($parsed))
                $this->payload[$key] = $array;
            else
                $this->payload[$key] = $val;
        }
    }

    public static function setPaginationParams(array $params): void
    {
        if (self::$pagination !== null)
            Response::internalServerError("Redefining of pagination params is not allowed");
        self::$pagination = $params;
    }

    public static function getPaginationParams(): array
    {
        return self::$pagination;
    }

    public static function setFilterParams(array $filters): void
    {
        if (self::$filters !== null)
            Response::internalServerError("Redefining of pagination params is not allowed");
        self::$filters = $filters;
    }

    public static function getFilterParams(): array
    {
        return self::$filters ?? [];
    }

    public static function setCustomParams(array $params): void
    {
        if (self::$custom_params !== null)
            Response::internalServerError("Redefining of custom params is not allowed");
        self::$custom_params = $params;
    }

    public static function getCustomParams(): array
    {
        return self::$custom_params;
    }
}

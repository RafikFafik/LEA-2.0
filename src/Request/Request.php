<?php

declare(strict_types=1);

namespace Lea\Request;

final class Request
{
    private $data;

    private $server;

    public $payload;

    const APPLICATION_JSON = "application/json";
    const MULTIPART_FORM_DATA = "multipart/form-data";

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

    public function getPayload(): array {
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
        switch ($_SERVER['CONTENT_TYPE'] ?? self::APPLICATION_JSON) {
            case self::APPLICATION_JSON:
                $this->parseJSON();
                break;
            default:
                $this->parsePOST();
        }
    }

    private function parseJSON(): void
    {
        $json = file_get_contents('php://input');
        $this->payload = json_decode($json, TRUE);
    }

    private function parsePOST(): void
    {
        foreach($_POST as $key => $val) {
            $parsed = $array = json_decode($val, true);
            if(is_array($parsed))
                $this->payload[$key] = $array;
            else
                $this->payload[$key] = $val;
        }
    }
}

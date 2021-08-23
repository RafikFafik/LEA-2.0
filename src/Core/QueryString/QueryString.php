<?php

declare(strict_types=1);

namespace Lea\Core\QueryString;

use Lea\Response\Response;
use Lea\Core\Type\DateTime;

final class QueryString
{
    private static $pagination;
    private static $filters;
    private static $state;
    private static $custom_params;

    public static function init(): void
    {
        self::setPaginationParams($pagination);
        self::setCustomParams($module['params']);
    }

    public static function processQueryString(string $query_string): void
    {
        if ($index = strpos($request_url, "?")) {
            $keyvals = explode("&", substr($request_url, $index + 1));
            foreach ($keyvals as $keyval) {
                if (!$index = strpos($keyval, "="))
                    Response::badRequest("Incorrect parameter pair: $keyval");
                $key = substr($keyval, 0, $index);
                $val = substr($keyval, $index + 1);
                $val_casted = (int)$val;
                if ($key == 'filters')
                    $query_string_params[$key] = $this->parseFilters($val);
                elseif (strlen((string)$val_casted) == strlen($val))
                    $query_string_params[$key] = $val_casted;
                else
                    $query_string_params[$key] = $val;
            }
            foreach ($required_params ?? [] as $param) {
                if (!array_key_exists($param, $query_string_params))
                    $not_delivered[] = $param;
            }
        } elseif ($required_params) {
            Response::badRequest('Missed query string params: ' . json_encode($required_params));
        }

        if ($not_delivered ?? false)
            Response::badRequest(['Missed query string params' => $not_delivered]);
    }

    private static function parseFilters(string $filters): array
    {
        $filters = explode(",", $filters);
        foreach ($filters as $pair) {
            $keyval = urldecode($pair);
            $keyval = explode("=", $keyval);
            $result[$keyval[0]] = $keyval[1];
        }
        return $result;
    }

    private static function processFilterParams(array $filters): array
    {
        if(!isset($this->module['filters']))
            return [];
        $config = $this->module['filters'];
        foreach ($filters as $key => $val) {
            if (in_array($key, $config['match'])) {
                $result[$key . '_LIKE'] = $val;
            } elseif (in_array($key, $config['range']) && str_contains($val,  "-")) {
                $tokens = explode("-", $val);
                if (!empty($tokens[0])) {
                    if ($date = DateTime::createFromFormat("d/m/Y", $tokens[0]))
                        $result[$key . '_>='] = $date->format("Y-m-d");
                    else
                        $result[$key . '_>='] = $tokens[0];
                }
                if (!empty($tokens[1])) {
                    if ($date = DateTime::createFromFormat("d/m/Y", $tokens[1]))
                        $result[$key . '_<='] = $date->format("Y-m-d");
                    else
                        $result[$key . '_<='] = $tokens[1];
                }
            }
        }

        return $result ?? [];
    }

    private static function getPaginationParams(array $params): array
    {
        $pagination['order'] = isset($params['order']) && strtoupper($params['order']) == 'DESC' ? $params['order'] : "ASC";
        $pagination['page'] = isset($params['page']) && $params['page'] > 0 ? $params['page'] - 1 : 0;
        $pagination['limit'] = isset($params['limit']) && $params['limit'] > 0 ? $params['limit'] : null;
        $pagination['sortby'] = isset($params['sortby']) ? $params['sortby'] : 'id';

        return $pagination;
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

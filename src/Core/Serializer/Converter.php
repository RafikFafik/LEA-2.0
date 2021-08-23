<?php

declare(strict_types=1);

namespace Lea\Core\Serializer;

class Converter
{
    private static function processSnakeToPascal(string $text): string
    {
        $result = str_replace('_', '', ucwords($text, '_'));

        return $result;
    }

    public static function getValuesFromObjectListByKey(iterable $objects, $key): array
    {
        $getValue = 'get' . self::processSnakeToPascal($key);
        foreach ($objects as $object) {
            $result[] = $object->$getValue();
        }

        return $result ?? [];
    }
}

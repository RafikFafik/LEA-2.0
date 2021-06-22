<?php

namespace Lea\Core\Serializer;

use Lea\Module\ContractorModule\Entity\Contractor;
use Lea\Module\ContractorModule\Service\ContractorService;

class Normalizer
{
    public static function normalize(array $data, string $class): object
    {
        $object = new $class($data);

        return $object;
    }

    public static function denormalize(object $object): array
    {
        $res = $object->get();

        return $res;
    }

    public static function denormalizeSpecificFields(object $object, array $needles): array
    {
        $res = $object->get();

        return $res;
    }



    public static function denormalizeList(iterable $list): array {
        foreach($list as $object) {
            $res[] = $object->get();
        }

        return $res ?? [];
    }
  
    public static function filterSpecificFieldsFromArrayList(array $haystack, array $needles): array {
        foreach($haystack as $key => $val) {
            // if(in_array($key, $to_get)
                // $res[$key] = 
        }

        return [];
    }

    public static function mapKeyOfArrayList(array $haystack, string $seek, $to_replace): array
    {
        foreach($haystack as $key => $val) {
            if(is_array($val)) {
                $res[$key] = self::mapKeyOfArrayList($val, $seek, $to_replace);
            } elseif($key == $seek) {
                $res[$to_replace] = $val;
            } else {
                $res[$key] = $val;
            }
        }

        return $res ?? [];
    }

    /**
     * One dimension support only
     */
    public static function arrayToJson(array $haystack, string $needle): array
    {
        foreach($haystack as $key => $val) {
            if($key != $needle || !is_array($val))
                continue;
            $haystack[$key] = json_encode($val);
        }

        return $haystack ?? [];
    }

    public static function jsonToArray(array $haystack, string $needle): array
    {
        foreach($haystack as $key => $val) {
            if($key != $needle)
                continue;
            $haystack[$key] = json_decode($val, true);
        }

        return $haystack ?? [];
    }

    public static function jsonToArrayList(array $arrays, string $needle): array
    {
        foreach($arrays as $array) {
            $res[] = self::jsonToArray($array, $needle);
        }

        return $res ?? [];
    }
}

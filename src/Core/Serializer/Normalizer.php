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
    // public static function normalize(array $data, object $namespace): object
    // {
    //     $ret = [];
    //     foreach ($data as $key => $el) {
    //         if (is_array($el))
    //             $ret[$key] = self::normalize($el, $namespace);
    //         else if (is_object($el))
    //             $ret[$key] = array_merge($el->get(), self::normalize($el, $namespace));
    //         else
    //             $ret[$key] = $el;
    //     }
    // }
}

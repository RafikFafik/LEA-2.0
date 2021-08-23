<?php

namespace Lea\Core\Entity;

use Lea\Core\Reflection\ReflectionClass;
use Lea\Core\Reflection\ReflectionProperty;
use Lea\Core\Validator\AnnotationValidator;

trait EntityGetter
{
    use Parser;

    public function get(array $specific_fields = null): array
    {
        $res = [];
        $class = get_called_class();
        $reflection = new ReflectionClass($class);
        foreach ($reflection->getProperties() as $property) {
            $recursive_res = [];
            $key = $property->getName();
            if ($specific_fields) {
                if (!in_array($key, $specific_fields))
                    continue;
            }
            $getValue = 'get' . $this->processSnakeToPascal($key);
            if (!method_exists($class, $getValue))
                continue;
            $val = $this->$getValue();
            $reflection = new ReflectionProperty($class, $key);
            if ($reflection->isObject()) {
                if (is_iterable($val)) {
                    foreach ($val as $obj) {
                        $recursive_res[] = $obj->get($specific_fields);
                    }
                    $res[$key] = $recursive_res;
                    /* Disposable - Begin */
                    if (str_contains($reflection->getName(), "files")) {
                        $res[$key][] = ['file_key' => "", 'deleted' => false];
                    }
                    /* Disposable - End*/
                }
            } else {
                $type = $reflection->getType2();
                $res[$key] = (($type == "Date" || $type == "Currency" || $type == "DateTime") && $val !== null) ? $val->__get() : $res[$key] = $val;
            }
        }
        /* Get fields that are not in entity */
        $ovars = get_object_vars($this);
        foreach ($ovars as $key => $val) {
            if ($specific_fields) {
                if (!in_array($key, $specific_fields))
                    continue;
            }
            if (!isset($res[$key]))
                $fields[$key] = $val;
        }
        foreach ($fields ?? [] as $key => $val) {
            if (is_iterable($val)) {
                foreach ($val as $obj) {
                    $recursive_res[] = $obj->get();
                }
                $res[$key] = $recursive_res;
            } else {
                $res[$key] = $val;
            }
        }
        return $res;
    }

    public function getGetters(): array
    {
        foreach (get_class_methods($this) as $method) {
            if (AnnotationValidator::hasPropertyCorrespondingToMethod($this, $method))
                $getters[] = $method;
        }

        return $getters ?? [];
    }
}
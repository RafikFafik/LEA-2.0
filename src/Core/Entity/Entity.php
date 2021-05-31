<?php

namespace Lea\Core\Entity;

use Generator;
use ArrayIterator;
use ReflectionClass;
use MultipleIterator;
use ReflectionProperty;
use Lea\Core\Reflection\Reflection;
use Lea\Module\ContractorModule\Entity\Address;

abstract class Entity
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var bool
     */
    protected $active = 1;

    /**
     * @var bool
     */
    protected $deleted = 0;

    public function __construct(array $data = NULL)
    {
        if ($data !== NULL)
            $this->set($data);
    }

    public function hasKey(string $key): bool
    {
        return $this->$key ? TRUE : FALSE;
    }

    public function set(array $data): void
    {
        $class = get_called_class();
        $reflection = new Reflection($this);
        // $mi = $this->getMultipleIterator($reflection->getProperties(), $data);
        foreach ($reflection->getProperties() as $property) {
            $key = $property->getName();
            if(!array_key_exists($key, $data))
                continue;
            $setValue = 'set' . $this->processSnakeToPascal($key);
            if ($property->is_object) {
                if (is_iterable($data[$key])) {
                    $children = [];
                    foreach ($data[$key] as $obj) {
                        $ChildClass = $reflection->getNamespaceName() . '\\' . $property->type;
                        $children[] = new $ChildClass($obj);
                    }
                    $this->$setValue($children);
                }
            } else {
                $this->$setValue($data[$key]);
            }
        }
    }

    private function getMultipleIterator(array $data, array $properties): ?MultipleIterator
    {
        $mi = new MultipleIterator(MultipleIterator::MIT_NEED_ANY);
        $mi->attachIterator(new ArrayIterator($data), "REQUEST");
        $mi->attachIterator(new ArrayIterator($properties), "REFLECTOR");

        return $mi;
    }

    public function get(): array
    {
        $res = [];
        $class = get_called_class();
        $reflection = new ReflectionClass($class);
        $protected_properties = $reflection->getProperties(ReflectionProperty::IS_PROTECTED);
        $private_properties = $reflection->getProperties(ReflectionProperty::IS_PRIVATE);
        $properties = array_merge($protected_properties, $private_properties);
        foreach ($properties as $var) {
            $key = $var->getName();
            if (!property_exists($class, $key))
                continue;
            $reflection = new Reflection($this);
            $getValue = 'get' . $this->processSnakeToPascal($key);
            $val = $this->$getValue();
            if ($reflection->isObject()) {
                if (is_iterable($val)) {
                    $children = [];
                    foreach ($val as $obj) {
                        $ChildClass = $reflection->getClassName();
                        $children[] = new $ChildClass($obj);
                    }
                    $res[$key] = $this->$getValue($children);
                }
            } else {
                $res[$key] = $val;
            }
        }

        return $res;
    }

    public function getGetters(): array
    {
        $getters = [];
        foreach (get_class_methods($this) as $method) {
            if ($this->hasPropertyCorrespondingToMethod($method))
                $getters[] = $method;
        }

        return $getters;
    }

    public function getSetters(): array
    {
        $setters = [];
        foreach (get_class_methods($this) as $method) {
            if ($this->hasPropertyCorrespondingToMethod($method, TRUE))
                $setters[] = $method;
        }

        return $setters;
    }

    public function getChildObjects(): iterable
    {
        $class = get_called_class();
        $obj = new $class;

        $reflection = new Reflection($obj);
        foreach ($reflection->getProperties() as $property) {
            $var = $property->getName();
            if (!$property->is_object)
                continue;
            if (is_iterable($var)) {
                $recursive_objs = [];
                foreach ($var as $obj) {
                    $ChildClass = $reflection->getClassName();
                    $recursive_objs[] = new $ChildClass($obj);
                }
            }
            $getValue = 'get' . $this->processSnakeToPascal($var);
            $objs[$var] = $this->$getValue();
        }

        return $objs ?? [];
    }

    public function hasPropertyCorrespondingToMethod(string $method_name, bool $is_setter = FALSE): bool
    {
        $prefix = substr($method_name, 0, 3);
        $type = $is_setter ? 'set' : 'get';
        if ($prefix != $type)
            return FALSE;
        $VarName = substr($method_name, 3);
        $varName = lcfirst($VarName);
        $var_name = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $varName));

        return property_exists(get_called_class(), $var_name) ? TRUE : FALSE;
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of active
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set the value of active
     *
     * @return  self
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get the value of deleted
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set the value of deleted
     *
     * @return  self
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    public static function getNamespace(): string
    {
        return get_called_class();
    }

    public function xd(): Generator
    {
        for ($i = 0; $i <= 10; $i++) {
            yield $i;
        }
    }

    private function processSnakeToPascal(string $text): string {
        $result = str_replace('_', '', ucwords($text, '_'));

        return $result;
    }

    public function hasId(): bool
    {
        return $this->id ? TRUE : FALSE;
    }
}

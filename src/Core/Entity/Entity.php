<?php

declare(strict_types=1);

namespace Lea\Core\Entity;

use Exception;
use TypeError;
use Lea\Core\Type\Date;
use Lea\Core\Entity\Active;
use Lea\Core\Type\Currency;
use Lea\Core\Type\DateTime;
use Lea\Core\Entity\Deleted;
use Lea\Core\Reflection\ReflectionClass;
use Lea\Core\Exception\InvalidDateFormatException;

/** Generic Entity that contains all functionalities connected with  */
abstract class Entity 
{
    use Active, Deleted, NamespaceProvider, EntityGetter, EntitySetter;
    /**
     * @var int
     */
    protected $id;

    public function __construct(array $data = NULL)
    {
        if ($data !== NULL)
            $this->set($data);
    }

    public function getChildObjects(): iterable
    {
        $class = get_called_class();
        $obj = new $class;

        $reflection = new ReflectionClass($obj);
        foreach ($reflection->getProperties() as $property) {
            $var = $property->getName();
            if (!$property->isObject())
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

    public function getReferencedProperties(): iterable
    {
        $getters = $this->getGetters();
        foreach ($getters as $getter) {
            if (str_ends_with($getter, "Id") && $getter !== "getId")
                $referenced[] = $getter;
        }

        return $referenced ?? [];
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

    public function hasId(): bool
    {
        return (bool)$this->id;
    }


    /**
     * @throws InvalidDateFormatException
     */
    protected static function castVariable($variable, string $type_to_cast, $key)
    {
        switch (strtoupper($type_to_cast)) {
            case "INT":
                return (int)$variable;
                break;
            case "BOOL":
                return filter_var($variable, FILTER_VALIDATE_BOOLEAN);
                break;
            case "DATE":
                if (!is_string($variable) || strlen($variable) == 0)
                    throw new TypeError($key . " - expected $type_to_cast");
                try {
                    $type = new Date($variable);
                } catch (Exception $e) {
                    throw new InvalidDateFormatException($key);
                }
                return $type;
            case "DATETIME":
                if (!is_string($variable))
                    throw new TypeError($key . " - expected string");
                try {
                    $type = new DateTime($variable);
                } catch (Exception $e) {
                    throw new InvalidDateFormatException($key);
                }
                return $type;
            case "CURRENCY":
                return new Currency($variable, false);
            default:
                return $variable;
                break;
        }
    }

    public function saveFiles(): void
    {
        foreach ($this->getChildObjects() as $children) {
            if (!is_array($children))
                continue;
            foreach ($children as $obj) {
                if ($obj->isFileClass() && $obj->binaryFileAppended())
                    $obj->moveUploadedFile();
            }
        }
    }

    public function isFileClass(): bool
    {
        $classname = $this->getClassName();

        return str_contains($classname, "File") ? true : false;
    }

    public function binaryFileAppended(): bool
    {
        return $this->file ?? false ? true : false;
    }

    public function removeKey(string $key): void
    {
        unset($this->$key);
    }
}

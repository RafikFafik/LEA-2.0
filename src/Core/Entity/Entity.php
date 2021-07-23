<?php

declare(strict_types=1);

namespace Lea\Core\Entity;

use Exception;
use TypeError;
use Lea\Core\Type\Date;
use Lea\Core\Reflection\ReflectionClass;
use Lea\Core\Exception\InvalidDateFormatException;
use Lea\Core\Reflection\ReflectionProperty;
use Lea\Core\Type\Currency;
use Lea\Core\Security\Service\AuthorizedUserService;
use Lea\Core\Type\DateTime;

abstract class Entity implements EntityInterface
{
    use NamespaceProvider, Getter, Setter;
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

    public function hasPropertyCorrespondingToMethod(string $method_name, bool $is_setter = FALSE): bool
    {
        $prefix = substr($method_name, 0, 3);
        $type = $is_setter ? 'set' : 'get';
        if ($prefix != $type)
            return FALSE;
        $VarName = substr($method_name, 3);
        $varName = lcfirst($VarName);
        $var_name = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $varName));

        return property_exists(get_called_class(), $var_name);
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
                if (!is_string($variable))
                    throw new TypeError($key . " - expected string");
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

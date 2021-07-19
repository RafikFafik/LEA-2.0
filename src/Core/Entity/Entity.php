<?php

declare(strict_types=1);

namespace Lea\Core\Entity;

use Exception;
use Generator;
use TypeError;
use Lea\Core\Type\Date;
use Lea\Core\Reflection\Reflection;
use Lea\Core\Exception\InvalidDateFormatException;
use Lea\Core\Reflection\ReflectionPropertyExtended;
use Lea\Core\Type\Currency;
use Lea\Core\Security\Service\AuthorizedUserService;

trait NamespaceProvider
{
    public static function getNamespace(): string
    {
        return get_called_class();
    }

    public function getClassName(): string
    {
        $tokens = explode('\\', get_class($this));
        $class = end($tokens);

        return $class;
    }
}

abstract class Entity implements EntityInterface
{
    use NamespaceProvider;
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
        return property_exists($this, $key);
    }

    public function set(array $data): void
    {
        $reflection = new Reflection($this);
        foreach ($reflection->getProperties() as $property) {
            $key = $property->getName();
            if (!array_key_exists($key, $data) && $key != 'user_id')
                continue;
            $setValue = 'set' . $this->processSnakeToPascal($key);
            if ($property->isObject()) {
                if (is_iterable($data[$key])) {
                    $children = [];
                    foreach ($data[$key] as $obj) {
                        $ChildClass = $property->getType2();
                        /* Disposable - begin */
                        if (str_contains($ChildClass, "File") && !isset($obj['id']) && !isset($_FILES[$obj['file_key']]))
                            continue;
                        /* Disposable - end */
                        $children[] = new $ChildClass($obj);
                    }
                    $this->$setValue($children);
                }
            } else {
                if ($setValue == 'setUserId' && !isset($data[$key]))
                    $val = AuthorizedUserService::getAuthorizedUserId();
                else
                    $val = self::castVariable($data[$key], $property->getType2(), $key);
                $this->$setValue($val);
            }
        }
    }

    public function get(array $specific_fields = null): array
    {
        $res = [];
        $class = get_called_class();
        $reflection = new Reflection($class);
        foreach ($reflection->getProperties() as $property) {
            $recursive_res = [];
            $key = $property->getName();
            if($specific_fields) {
                if(!in_array($key, $specific_fields))
                    continue;
            }
            $getValue = 'get' . $this->processSnakeToPascal($key);
            if (!method_exists($class, $getValue))
                continue;
            $val = $this->$getValue();
            $reflection = new ReflectionPropertyExtended($class, $key);
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
                $res[$key] = (($type == "Date" || $type =="Currency") && $val !== null) ? $val->__get() : $res[$key] = $val;
            }
        }
        /* Get fields that are not in entity */
        $ovars = get_object_vars($this);
        foreach ($ovars as $key => $val) {
            if($specific_fields) {
                if(!in_array($key, $specific_fields))
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
            if ($this->hasPropertyCorrespondingToMethod($method))
                $getters[] = $method;
        }

        return $getters ?? [];
    }

    public function getSetters(): array
    {
        foreach (get_class_methods($this) as $method) {
            if ($this->hasPropertyCorrespondingToMethod($method, TRUE))
                $setters[] = $method;
        }

        return $setters ?? [];
    }

    public function getChildObjects(): iterable
    {
        $class = get_called_class();
        $obj = new $class;

        $reflection = new Reflection($obj);
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

    private function processSnakeToPascal(string $text): string
    {
        $result = str_replace('_', '', ucwords($text, '_'));

        return $result;
    }

    public function hasId(): bool
    {
        return $this->id ? TRUE : FALSE;
    }


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

<?php

namespace Lea\Core\Entity;

use Exception;
use Lea\Core\Reflection\Reflection;

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

    public function __construct(array $data)
    {
        $this->set($data);
    }

    public function set(array $data): void
    {
        $class = get_called_class();

        foreach ($data as $key => $val) {
            if (!property_exists($class, $key))
                continue;
            $reflection = new Reflection($class, $key);
            if ($reflection->isObject()) {
                if (is_iterable($val)) {
                    $children = [];
                    foreach($val as $obj) {
                        $ChildClass = $reflection->getClassName();
                        $children[] = new $ChildClass($obj);
                    }
                    $this->$key = $children;
                }
            } else {
                $this->$key = $val;
            }
        }
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

    public static function __set_state(array $state)
    {
        $class = get_called_class();
        // Assumption: Constructor can be invoked without parameters!
        $object = new $class();
        foreach (get_class_vars($class) as $member) {
            if (!isset($state[$member])) {
                // Member was not provided, you can choose to ignore this case
                throw new Exception("$class::$member was not provided");
            } else {
                // Set member directly
                // Assumption: members are all public or protected
                $object->$member = $state[$member];
                // Or use the setter-approach given by Chaos.
                $setter = 'set' . ucfirst($member);
                $object->setter($state[$member]);
            }
        }
        return $object;
    }

    public static function getNamespace(): string
    {
        return get_called_class();
    }
}

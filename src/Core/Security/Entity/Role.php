<?php

declare(strict_types=1);

namespace Lea\Core\SecurityModule\Entity;

use Lea\Core\Entity\Entity;

/**
 * @HasMany <User>
 */
class Role extends Entity
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $parent_id;

    /**
     * Get the value of name
     *
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @param  string  $name
     *
     * @return  self
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of parent_id
     *
     * @return  int
     */
    public function getParentId()
    {
        return $this->parent_id;
    }

    /**
     * Set the value of parent_id
     *
     * @param  int  $parent_id
     *
     * @return  self
     */
    public function setParentId(int $parent_id)
    {
        $this->parent_id = $parent_id;

        return $this;
    }
}

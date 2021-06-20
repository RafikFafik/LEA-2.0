<?php

declare(strict_types=1);

namespace Lea\Core\Security\Entity;

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
     * Parent role id;
     * @var int
     */
    private $role_id;

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
    public function getRoleId()
    {
        return $this->role_id;
    }

    /**
     * Set the value of parent_id
     *
     * @param  int  $parent_id
     *
     * @return  self
     */
    public function setRoleId(int $role_id)
    {
        $this->role_id = $role_id;

        return $this;
    }
}

<?php

namespace Lea\Core\Entity;

trait Active
{
    /**
     * @var bool
     */
    protected $active = 1;

    public function getActive()
    {
        return $this->active;
    }

    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }
}

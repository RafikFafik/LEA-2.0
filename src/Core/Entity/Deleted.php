<?php

namespace Lea\Core\Entity;

trait Deleted
{
    /**
     * @var bool
     */
    protected $deleted = 0;

    public function getDeleted()
    {
        return $this->deleted;
    }

    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }
}

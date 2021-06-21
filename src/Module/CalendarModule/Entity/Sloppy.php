<?php

declare(strict_types=1);

namespace Lea\Module\CalendarModule\Entity;

use Lea\Core\Entity\Entity;

class Sloppy extends Entity
{
    /**
     * @var int
     */
    private $field;

    /**
     * @var string
     */
    private $field_name;

    /**
     * Get the value of field_id
     *
     * @return  int
     */ 
    public function getField()
    {
        return $this->field;
    }

    /**
     * Set the value of field_id
     *
     * @param  int  $field_id
     *
     * @return  self
     */ 
    public function setField(int $field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * Get the value of field_name
     *
     * @return  string
     */ 
    public function getFieldName()
    {
        return $this->field_name;
    }

    /**
     * Set the value of field_name
     *
     * @param  string  $field_name
     *
     * @return  self
     */ 
    public function setFieldName(string $field_name)
    {
        $this->field_name = $field_name;

        return $this;
    }
}

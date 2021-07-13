<?php

declare(strict_types=1);

namespace Lea\Module\CalendarModule\Entity;

use Lea\Core\Entity\Entity;

class Alert extends Entity
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var integer
     */
    private $time;

    /**
     * @var integer
     */
    private $calendar_event_id;

    /**
     * Get the value of type
     *
     * @return  string
     */ 
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the value of type
     *
     * @param  string  $type
     *
     * @return  self
     */ 
    public function setType(string $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the value of time
     *
     * @return  integer
     */ 
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set the value of time
     *
     * @param  integer  $time
     *
     * @return  self
     */ 
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get the value of calendar_event_id
     *
     * @return  integer
     */ 
    public function getCalendarEventId()
    {
        return $this->calendar_event_id;
    }

    /**
     * Set the value of calendar_event_id
     *
     * @param  integer  $calendar_event_id
     *
     * @return  self
     */ 
    public function setCalendarEventId($calendar_event_id)
    {
        $this->calendar_event_id = $calendar_event_id;

        return $this;
    }
}

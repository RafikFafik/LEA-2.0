<?php

declare(strict_types=1);

namespace Lea\Module\CalendarModule\Entity;

use Lea\Core\Entity\Entity;

class Alert extends Entity
{
    /**
     * @var string
     */
    private $kind;

    /**
     * @var integer
     */
    private $time;

    /**
     * @var integer
     */
    private $calendar_event_id;

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

    /**
     * Get the value of kind
     *
     * @return  string
     */
    public function getKind()
    {
        return $this->kind;
    }

    /**
     * Set the value of kind
     *
     * @param  string  $kind
     *
     * @return  self
     */
    public function setKind(string $kind)
    {
        $this->kind = $kind;

        return $this;
    }
}

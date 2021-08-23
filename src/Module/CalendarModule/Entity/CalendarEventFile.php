<?php

declare(strict_types=1);

namespace Lea\Module\CalendarModule\Entity;

use Lea\Core\File\Entity\File;


class CalendarEventFile extends File
{
    /**
     * @var int
     */
    private $calendar_event_id;

    /**
     * Get the value of calendar_event_id
     *
     * @return  int
     */ 
    public function getCalendarEventId()
    {
        return $this->calendar_event_id;
    }

    /**
     * Set the value of calendar_event_id
     *
     * @param  int  $calendar_event_id
     *
     * @return  self
     */ 
    public function setCalendarEventId(int $calendar_event_id)
    {
        $this->calendar_event_id = $calendar_event_id;

        return $this;
    }
}
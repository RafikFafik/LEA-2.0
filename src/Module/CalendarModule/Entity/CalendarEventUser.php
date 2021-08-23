<?php

declare(strict_types=1);

namespace Lea\Module\CalendarModule\Entity;

use Lea\Core\Entity\Entity;

class CalendarEventUser extends Entity
{
    /**
     * @var int
     */
    private $user_id;

    /**
     * @var int
     */
    private $calendar_event_id;

    /**
     * Get the value of user_id
     *
     * @return  int
     */ 
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set the value of user_id
     *
     * @param  int  $user_id
     *
     * @return  self
     */ 
    public function setUserId(int $user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * Get the value of calendar_event
     *
     * @return  int
     */ 
    public function getCalendarEventId()
    {
        return $this->calendar_event_id;
    }

    /**
     * Set the value of calendar_event
     *
     * @param  int  $calendar_event
     *
     * @return  self
     */ 
    public function setCalendarEventId(int $calendar_event_id)
    {
        $this->calendar_event_id = $calendar_event_id;

        return $this;
    }
}

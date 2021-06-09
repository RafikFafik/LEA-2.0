<?php

declare(strict_types=1);

namespace Lea\Module\CalendarModule\Repository;

use Lea\Core\Repository\Repository;
use Lea\Module\CalendarModule\Entity\Event;

final class CalendarRepository extends Repository
{
    private $entity;

    public function __construct(array $params)
    {
        $this->entity = new Event();
        parent::__construct($this->entity);
    }

    public function getEventListByStartDate(string $date, object $object): iterable
    {

        return [];
    }
}

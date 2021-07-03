<?php

declare(strict_types=1);

namespace Lea\Module\CalendarModule\Repository;

use Lea\Core\Reflection\Reflection;
use Lea\Core\Validator\Validator;
use Lea\Core\Repository\Repository;
use Lea\Core\Serializer\Converter;
use Lea\Module\Security\Service\AuthorizedUserService;

final class CalendarEventRepository extends Repository
{
    public function findCalendarEventListByStartDate(string $date, object $object): iterable
    {
        $constraints = ['date_start' => $date];
        $res = $this->getListDataByConstraints($object, $constraints);

        return $res;
    }

    public function findCalendarEventListByConstraints($month, $year, int $user_id = null): iterable
    {
        $month = Validator::parseMonth($month);
        $constraint = $year . '-' . $month;
        $constraints = ['date_start_LIKE' => $constraint];
        $reflector = new Reflection($this->object);
        if($reflector->hasSubClassDependency()) {
            $subclass = $reflector->getSubClass();
            $subkey = $reflector->getSubKey();
            $objs = $this->getListDataByConstraints(new $subclass, [$subkey => $user_id ?? AuthorizedUserService::getAuthorizedUserId()]);
            $ids = Converter::getValuesFromObjectListByKey($objs, 'calendar_event_id');
            $constraints['id_IN'] = $ids;
        }
        $list = $this->getListDataByConstraints($this->object, $constraints);

        return $list;
    }
}

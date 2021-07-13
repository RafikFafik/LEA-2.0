<?php

declare(strict_types=1);

namespace Lea\Module\CalendarModule\Repository;

use Lea\Core\Validator\Validator;
use Lea\Core\Serializer\Converter;
use Lea\Core\Reflection\Reflection;
use Lea\Core\Repository\Repository;
use Lea\Core\Security\Service\AuthorizedUserService;

final class CalendarEventRepository extends Repository
{
    public function findCalendarEventListByStartDate(string $date, int $user_id = null): iterable
    {
        $constraints['date_start_<='] = $date;
        $constraints['date_end_>='] = $date;
        $reflector = new Reflection($this->object);
        if($reflector->hasSubClassDependency()) {
            $subclass = $reflector->getSubClass();
            $subkey = $reflector->getSubKey();
            $objs = $this->getListDataByConstraints(new $subclass, [$subkey => $user_id ?? AuthorizedUserService::getAuthorizedUserId()]);
            $ids = Converter::getValuesFromObjectListByKey($objs, 'calendar_event_id');
            $constraints['id_IN'] = $ids;
        }
        $res = $this->getListDataByConstraints($this->object, $constraints);

        return $res;
    }

    public function findCalendarEventListByConstraints($month, $year, int $user_id = null): iterable
    {
        $month = Validator::parseMonth($month);
        $constraint = $year . '-' . $month;
        $between['from'] = $year . '-' . Validator::parseMonth($month - 1) . '-01';
        $between['to'] = $year . '-' . Validator::parseMonth($month + 1) . '-31';
        $constraints = ['date_start_BETWEEN' => $between];
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

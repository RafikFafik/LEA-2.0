<?php

declare(strict_types=1);

namespace Lea\Module\CalendarModule\Repository;

use DateInterval;
use Lea\Core\Database\QueryProvider;
use Lea\Core\Exception\ResourceAlreadyInactiveException;
use Lea\Core\Exception\ResourceNotExistsException;
use Lea\Core\Type\DateTime;
use Lea\Core\Validator\Validator;
use Lea\Core\Serializer\Converter;
use Lea\Core\Repository\Repository;
use Lea\Core\Type\DateTimeImmutable;
use Lea\Core\Reflection\ReflectionClass;
use Lea\Core\Security\Service\AuthorizedUserService;
use Lea\Module\CalendarModule\Entity\CalendarEventUser;
use Lea\Module\ContractorModule\Entity\ContractorEmployee;
use Lea\Module\ContractorModule\Repository\ContractorEmployeeRepository;
use Lea\Module\ContractorModule\Repository\ContractorRepository;
use Lea\Response\Response;

final class CalendarEventRepository extends Repository
{
    public function save(object &$object): int
    {
        $event_date = $object->getDateStart()->format('Y-m-d');
        $event_timestart = $object->getTimeStart();
        
        foreach ($object->getAlerts() as $alert) {
            $alert_on = new DateTime($event_date . " " . $event_timestart);
            $minutes_before = $alert->getTime();
            $alert_on->sub(new DateInterval('PT' . $minutes_before . 'M'));
            $alert->setLaunchDateTime($alert_on);
        }
        return parent::save($object);
    }

    public function findCalendarEventListByStartDateAndUserId(string $date, int $user_id = null): iterable
    {
        $constraints['date_start_<='] = $date;
        $constraints['date_end_>='] = $date;
        $reflector = new ReflectionClass($this->object);
        if ($reflector->hasSubClassDependency()) {
            $subclass = $reflector->getSubClass();
            $subkey = $reflector->getSubKey();
            $objs = $this->getListDataByConstraints(new $subclass, [$subkey => $user_id ?? AuthorizedUserService::getAuthorizedUserId()]);
            $ids = Converter::getValuesFromObjectListByKey($objs, 'calendar_event_id');
            $constraints['id_IN'] = $ids;
        }

        return $this->getListDataByConstraints($this->object, $constraints);
    }

    public function findCalendarEventListByStartDate(string $date): iterable
    {
        $constraints['date_start_<='] = $date;
        $constraints['date_end_>='] = $date;

        return $this->getListDataByConstraints($this->object, $constraints);
    }

    public function findTodayCalendarEventList(): iterable
    {
        $constraints['date_start'] = date('Y-m-d');

        return $this->getListDataByConstraints($this->object, $constraints);
    }

    public function findCalendarEventListByConstraints($month, $year, int $user_id = null): iterable
    {
        $month = Validator::parseMonth($month);
        $constraint = $year . '-' . $month;
        $between['from'] = $year . '-' . Validator::parseMonth($month - 1) . '-01';
        $between['to'] = $year . '-' . Validator::parseMonth($month + 1) . '-31';
        $constraints = ['date_start_BETWEEN' => $between];
        $reflector = new ReflectionClass($this->object);
        if ($reflector->hasSubClassDependency()) {
            $subclass = $reflector->getSubClass();
            $subkey = $reflector->getSubKey();
            $objs = $this->getListDataByConstraints(new $subclass, [$subkey => $user_id ?? AuthorizedUserService::getAuthorizedUserId()]);
            $ids = Converter::getValuesFromObjectListByKey($objs, 'calendar_event_id');
            $constraints['id_IN'] = $ids;
        }
        $cr = new ContractorRepository();
        $events = $this->getListDataByConstraints($this->object, $constraints);
        foreach($events as $event) {
            try {
                $contractor = $cr->findById($event->getContractorId());
                $event->contractor_name = $contractor->getShortname();
            } catch(ResourceNotExistsException $e) {
                $event->contractor_name = "";
            }
        }

        return $events;
    }

    public function findCalendarEventListByYearAndWeekAndUserId($year, $week, $user_id): iterable
    {
        $query_provider = new QueryProvider("xd");
        $date = new DateTimeImmutable();
        $from = $date->setISODate($year, $week);
        $to = $date->setISODate($year, $week, 7);
        $objs = $this->getListDataByConstraints(new CalendarEventUser, ['user_id' => $user_id]);
        $ids = Converter::getValuesFromObjectListByKey($objs, 'calendar_event_id');
        $constraints = [
            'date_start_>=' => $from,
            'date_end_<=' => $to,
            // 'id_IN' => $query_provider->getSelectIdsQuery($this->object, 'user_id', $user_id)
            'id_IN' => $ids
        ];
        
        return $this->getListDataByConstraints($this->object, $constraints);
    }
    
    public function findCalendarEventListByYearAndWeek(int $year, int $week): iterable
    {
        $date = new DateTimeImmutable();
        $from = $date->setISODate($year, $week);
        $to = $date->setISODate($year, $week, 7);
        $constraints = ['date_start_>=' => $from, 'date_end_<=' => $to];
        
        return $this->getListDataByConstraints($this->object, $constraints);
    }
}

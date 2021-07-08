<?php
namespace Lea\CronJobs;

use Lea\Core\Cron\Cron;

class CronJobs {
  public function addJobs() {
    Cron::addJob('curl api.sempre.lea24.pl/calendar/send-alert', '* * * * *');
  }
}

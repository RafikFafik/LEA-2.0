<?php
namespace Lea\CronJobs;

use Lea\Core\Cron\Cron;

class CronJobs {
  public function addJobs() {
    Cron::addJob('curl ' . $_ENV['API'] . '/calendar/send-alert', '* * * * *');
  }
}

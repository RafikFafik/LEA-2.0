<?php

namespace Lea\Core\Cron;

class Cron {

  static private function stringToArray(string $jobs = '') : array {
    $array = explode("\r\n", trim($jobs));
    foreach ($array as $key => $item) {
      if ($item == '') 
        unset($array[$key]);
    }

    return $array;
  }

  static private function arrayToString(array $jobs = array()) : string {
    return implode('\r\n', $jobs);
  }

  
  static public function getJobs() : array {
    $output = shell_exec('crontab -l');
    return self::stringToArray($output);
  }

  static public function saveJobs(array $jobs = array()) {
    $output = shell_exec('echo "'. self::arrayToString($jobs) . '" | crontab -');
    return $output;
  }

  static public function jobExists(string $job = '', string $schedule = '* * * * *') : bool {
    $jobs = self::getJobs();
    return in_array($schedule.' '.$job, $jobs);
  }

  static public function addJob(string $job, string $schedule) {
    if (self::jobExists($schedule.' '.$job))
      return false;

    $jobs = self::getJobs();
    $jobs[] = $schedule.' '.$schedule;

    return self::saveJobs($jobs);
  }

  static public function removeJob(string $job, string $schedule) {
    if (!self::jobExists($job, $schedule))
      return false;

    $jobs = self::getJobs();
    unset($jobs[array_search($job.' '.$schedule, $jobs)]);
    return self::saveJobs($jobs);
  }
}

<?php

declare(strict_types=1);

namespace Lea\Core\Database;

interface DatabaseManagerInterface {
    public function getRecordData(object $object, $fldVal, $fldName = "id", $debug = false);
    public function insertRecordData(object $object, $arr, $retId = false, $debug = false);
}
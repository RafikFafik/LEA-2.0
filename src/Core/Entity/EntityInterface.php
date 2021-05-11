<?php

namespace Lea\Core\Entity;

interface EntityInterface {
    public function set(array $action, bool $safe = false);
    public function get(...$name);
    public static function load($array, $db);
    public static function loadAll($array, $db, $obj_to_arr = false);
    public static function search($db, $search = [], $fields = [], $to_array = false, $debug = false);
    public function save($debug = false);
    public function remove(): void;
    public function insert($debug = false);
}

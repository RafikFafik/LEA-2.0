<?php

namespace Lea;
class ServiceLoader {
    public static function load() {
        $core = __DIR__ . "/Core/**/*.php";
        $list = glob($core);
        $index2 = array_search("/var/www/html/src/Core/Database/DatabaseConnection.php", $list); /* Workaround */
        $index = array_search("/var/www/html/src/Core/Database/DatabaseUtil.php", $list); /* Workaround */
        include $list[$index2]; /* Workaround */
        include $list[$index]; /* Workaround */
        foreach ($list as $filename) {
            require_once $filename;
        }
        $module = __DIR__ . "/**/**/**/*.php";
        foreach (glob($module) as $filename) {
            include $filename;
        }
        $additional = __DIR__ . '/**/*.php';
        foreach (glob($additional) as $filename) {
            include $filename;
        }
    }
}

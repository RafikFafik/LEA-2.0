<?php

namespace Lea;

use Dotenv;
class ServiceLoader {
    public static function load() {
        $core = __DIR__ . "/Core/**/*.php";
        foreach (glob($core) as $filename) {
            include $filename;
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

<?php

namespace Lea;

class ServiceLoader {
    public static function load() {
        $path = __DIR__ . "/**/**/**/*.php";
        foreach (glob($path) as $filename) {
            include $filename;
        }
        include glob(__DIR__ . '/**/*.php')[0];
    }
}

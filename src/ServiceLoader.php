<?php

namespace Lea;

class ServiceLoader {
    public static function load() {
        $src = __DIR__ . "/**/**/**/*.php";
        foreach (glob($src) as $filename) {
            include $filename;
        }
        $additional = __DIR__ . '/**/*.php';
        foreach (glob($additional) as $filename) {
            include $filename;
        }
    }
}

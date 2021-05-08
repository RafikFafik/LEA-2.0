<?php

namespace Lea;

class ServiceLoader
{
    public static function load()
    {
        foreach (glob("./../src/**/*.php") as $filename) {
            include $filename;
        }
    }
}

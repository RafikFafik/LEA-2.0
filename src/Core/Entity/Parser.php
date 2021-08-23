<?php

namespace Lea\Core\Entity;

trait Parser
{
    private function processSnakeToPascal(string $text): string
    {
        return str_replace('_', '', ucwords($text, '_'));
    }
}
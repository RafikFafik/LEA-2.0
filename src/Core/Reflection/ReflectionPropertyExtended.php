<?php

declare(strict_types=1);

namespace Lea\Core\Reflection;

use Exception;
use ReflectionProperty;

class ReflectionPropertyExtended extends ReflectionProperty
{
    private $is_object;
    private $type;

    public function __construct($class, $property)
    {
        parent::__construct($class, $property);
        $this->type = $this->getTypePHP7($this);
        $this->is_object = $this->type == NULL ? FALSE : TRUE;
    }

    protected function setType($type): void
    {
    }

    public function isObject(): bool
    {
        return $this->is_object ? TRUE : FALSE;
    }

    public static function getTypePHP7(ReflectionProperty $property)
    {
        $comment = $property->getDocComment();
        if (!$comment)
            throw new Exception("TODO - DocComment exception support", 500);
        if (!(int)strpos($comment, "@var"))
            return null;
        $tokens = explode(" ", $comment);
        $index = array_search("@var", $tokens);

        $var = $tokens[$index + 1];

        return self::getDataType($var);
    }

    private static function getDataType(string $data_type)
    {
        if (!(int)strpos($data_type, "<"))
            return null;
        $a = explode("<", $data_type);
        $datatype = explode(">", $a[1])[0];

        return $datatype;
    }
}

<?php

declare(strict_types=1);

namespace Lea\Core\Reflection;

use Exception;
use ReflectionClass;
use ReflectionProperty;

class ReflectionPropertyExtended extends ReflectionProperty
{
    private $is_object;
    private $type;
    private $namespace;

    public function __construct($class, $property)
    {
        parent::__construct($class, $property);
        $a = new $class;
        $b = new ReflectionClass($a);
        $this->namespace = $b->getNamespaceName();
        $type = self::getTypePHP7($this);
        if (ctype_upper($type[0])) {
            $this->is_object = TRUE;
            $this->type = $this->getNamespaceName() . "\\" . $type;
        } else {
            $this->is_object = FALSE;
            $this->type = $type;
        }
    }

    public function isObject(): bool
    {
        return $this->is_object ? TRUE : FALSE;
    }

    public function getType2()
    {
        return $this->type;
    }

    private function parseClassNamespace(string $class): string
    {
        return "";
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

        $var = trim($tokens[$index + 1]);

        return self::getDataType($var);
    }

    private static function getDataType(string $data_type)
    {
        if (!(int)strpos($data_type, "<"))
            return $data_type;
        $a = explode("<", $data_type);
        $datatype = explode(">", $a[1])[0];

        return $datatype;
    }

    public function getNamespaceName(): string
    {
        return $this->namespace;
    }
}

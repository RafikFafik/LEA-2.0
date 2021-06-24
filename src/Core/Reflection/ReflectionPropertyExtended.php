<?php

declare(strict_types=1);

namespace Lea\Core\Reflection;

use Exception;
use ReflectionClass;
use ReflectionProperty;
use Lea\Core\Exception\DocCommentMissedException;

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
        if ($this->isPrimitiveType($type)) {
            $this->is_object = FALSE;
            $this->type = $type;
        } else {
            /* WIP */
            $this->is_object = TRUE;
            $class_list = get_declared_classes();
            $index = array_keys(get_declared_classes(), $type);
            if (str_contains($type, "\\"))
                $this->type = $type;
            else
                $this->type = $this->getNamespaceName() . "\\" . $type;
        }
    }

    public function setIfObject(bool $is_object): void
    {
        $this->is_object = $is_object;
    }

    public function isObject(): bool
    {
        return $this->is_object ? TRUE : FALSE;
    }

    public function getType2()
    {
        return $this->type;
    }

    public static function getTypePHP7(ReflectionProperty $property)
    {
        $comment = $property->getDocComment();
        if (!$comment)
            throw new DocCommentMissedException($property->getName());
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

    public static function isPrimitiveType(string $type)
    {
        switch (strtoupper($type)) {
            case "INT":
            case "INTEGER":
            case "BOOL":
            case "BOOLEAN":
            case "DATE":
            case "DATETIME":
            case "STRING":
                return true;
            default:
                return false;
        }
    }
}

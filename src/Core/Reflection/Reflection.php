<?php

declare(strict_types=1);

namespace Lea\Core\Reflection;

use Exception;
use Lea\Response\Response;
use ReflectionClass;
use ReflectionProperty;

final class Reflection extends ReflectionProperty
{
    private $is_object;
    private $type;
    private $namespace;

    public function __construct(string $class, string $key)
    {
        parent::__construct($class, $key);
        $reflaction_class = new ReflectionClass($class);
        $this->namespace = $reflaction_class->getNamespaceName();
        $this->type = $this->getTypePHP7();
        $this->is_object = $this->type == NULL ? FALSE : TRUE;
    }

    public function isObject(): bool
    {
        return $this->is_object ? TRUE : FALSE;
    }

    public function getClassName(): string
    {
        return $this->namespace . "\\" . $this->type;
    }

    private function getTypePHP7()
    {
        $comment = $this->getDocComment();
        if (!$comment)
            throw new Exception("TODO - DocComment exception support", 500);
        if (!(int)strpos($comment, "@var"))
            return null;
        $tokens = explode(" ", $comment);
        $index = array_search("@var", $tokens);

        $var = $tokens[$index + 1];

        return $this->getDataType($var);
    }

    private function getDataType(string $data_type)
    {
        if (!(int)strpos($data_type, "<"))
            return null;
        $a = explode("<", $data_type);
        $datatype = explode(">", $a[1])[0];

        return $datatype;
    }
}

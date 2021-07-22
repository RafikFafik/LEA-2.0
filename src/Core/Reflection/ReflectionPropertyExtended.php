<?php

declare(strict_types=1);

namespace Lea\Core\Reflection;

use ReflectionClass;
use ReflectionProperty;
use Lea\Core\Exception\DocCommentMissedException;

class ReflectionPropertyExtended extends ReflectionProperty
{
    private $is_object;
    private $type;
    private $namespace;
    private $annotation;

    public function __construct($class, $property)
    {
        parent::__construct($class, $property);
        $this->loadDocComment();
        $reflector = new ReflectionClass($class);
        $this->namespace = $reflector->getNamespaceName();
        $this->type = $this->getTypePHP7($this);
        if($this->isEntity())
            $this->loadType();
        elseif($this->isView())
            $this->loadView();
    }

    private function isEntity(): bool
    {
        return str_contains($this->annotation, "@var");
    }
    
    private function isView(): bool
    {
        return str_contains($this->annotation, "@from");
    }

    
    private function loadType(): void
    {
        if ($this->isPrimitiveType($this->type)) {
            $this->is_object = FALSE;
            $this->type = $this->type;
        } else {
            $this->is_object = TRUE;
            if (str_contains($this->type, "\\"))
                $this->type = $this->type;
            else
                $this->type = $this->getNamespaceName() . "\\" . $this->type;
        }
    }

    private function loadView(): void
    {
        $this->is_object = false;
    }

    public function isObject(): bool
    {
        return $this->is_object;
    }

    public function getType2()
    {
        return $this->type;
    }

    public function getTypePHP7()
    {
        if (!(int)strpos($this->annotation, "@var"))
            return null;
        $tokens = explode(" ", $this->annotation);
        $index = array_search("@var", $tokens);

        $var = trim($tokens[$index + 1]);

        return self::getDataType($var);
    }

    private function loadDocComment(): void
    {
        $this->annotation = $this->getDocComment();
        if (!$this->annotation)
            throw new DocCommentMissedException($this->getName());
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
            case "CURRENCY":
            case "STRING":
                return true;
            default:
                return false;
        }
    }
}

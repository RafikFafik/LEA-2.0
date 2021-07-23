<?php

declare(strict_types=1);

namespace Lea\Core\View;

use Lea\Core\Entity\EntityGetter;
use Lea\Core\Entity\EntitySetter;
use Lea\Core\Entity\NamespaceProvider;

abstract class View
{
    use NamespaceProvider, EntityGetter, EntitySetter;

    /**
     * @var int
     */
    protected $id;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}

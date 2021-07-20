<?php

declare(strict_types=1);

namespace Lea\Core\View;

use Lea\Core\Entity\Getter;
use Lea\Core\Entity\NamespaceProvider;
use Lea\Core\Entity\Setter;

abstract class View
{
    use NamespaceProvider, Getter, Setter;

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

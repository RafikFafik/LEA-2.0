<?php

declare(strict_types=1);

namespace Lea\Core\View;

use Lea\Core\Entity\Id;
use Lea\Core\Entity\Active;
use Lea\Core\Entity\EntityGetter;
use Lea\Core\Entity\EntitySetter;
use Lea\Core\Entity\NamespaceProvider;

abstract class View
{
    use Id, Active, NamespaceProvider, EntityGetter, EntitySetter;
}

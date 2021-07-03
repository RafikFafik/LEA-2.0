<?php

declare(strict_types=1);

namespace Lea\Core\Controller;

use Lea\Request\Request;

interface ControllerInterface {
    public function __construct(Request $request, array $params = NULL);
    public function init(): void;
}

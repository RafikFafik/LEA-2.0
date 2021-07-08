<?php

declare(strict_types=1);

namespace Lea\Module\OfferModule\Service;

use Lea\Core\Service\Service;

final class OfferService extends Service
{
    public function getView(): iterable
    {
        $list = $this->repository->findList();

        return $list;
    }
}

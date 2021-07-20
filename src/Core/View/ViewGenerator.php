<?php

declare(strict_types=1);

namespace Lea\Core\View;

use Lea\Request\Request;
use Lea\Core\Repository\Repository;
use Lea\Core\Serializer\Normalizer;

final class ViewGenerator implements ViewInterface
{
    function __construct(Repository $repository)
    {
        $this->repository = $repository;
        $this->pagination = Request::getPaginationParams();;
    }
    
    public function getView(): iterable
    {
        $list = $this->repository->findList();
        $list = Normalizer::denormalizeList($list);
        $result['data'] = $list;
        $result['pagination'] = $this->getPaginationData();

        return $result;
    }

    private function getPaginationData(): array
    {
        $count_data = $this->repository->findCountData();
        $page = (int)$this->pagination['page'] + 1;
        if (!$this->pagination['limit'])
            $this->pagination['limit'] = $count_data;
        $all_pages = ceil(($count_data / $this->pagination['limit']));

        $data['page'] = $page;
        $data['limit'] = $this->pagination['limit'];
        $data['pages'] = $all_pages;

        return $data;
    }
}

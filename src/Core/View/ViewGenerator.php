<?php

declare(strict_types=1);

namespace Lea\Core\View;

use Lea\Request\Request;
use Lea\Core\Repository\Repository;
use Lea\Core\Serializer\Normalizer;

final class ViewGenerator implements ViewInterface
{
    /**
     * @var Repository
     */
    private $repository;

    function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }
    
    public function getView(string $state): iterable
    {
        $constraints['active'] = $state !== null && $state == 'inactive' ? false : true;
        $pagination = $this->getPaginationData(Request::getPaginationParams(), $constraints);
        $list = $this->repository->findList($constraints, $pagination);
        $list = Normalizer::denormalizeList($list);
        $result['data'] = $list;
        $result['pagination'] = $pagination;

        return $result;
    }

    public function formatPagination(array $data): array
    {
        $result['data'] = $data;
        $result['pagination'] = $this->getPaginationData(Request::getPaginationParams(), ['active' => true]);

        return $result;
    }

    private function getPaginationData(array $pagination, $constraints): array
    {
        /* TODO - $ avtive / inactive */
        $count_data = $this->repository->findCountData($constraints);
        if($count_data == 0)
            $count_data = 1;
        $requested_page = (int)$pagination['page'] + 1;
        if (!$pagination['limit'])
            $pagination['limit'] = $count_data;
        $available_pages = ceil(($count_data / $pagination['limit']));
        if($available_pages < $requested_page)
            $requested_page = $available_pages;

        $data['page'] = $requested_page;
        $data['limit'] = $pagination['limit'];
        $data['pages'] = $available_pages;

        return $data;
    }
}

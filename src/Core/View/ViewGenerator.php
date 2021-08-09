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
        $existed_pages = ceil($this->repository->findCountData($constraints) / $pagination['limit']);
        Request::setPaginationPage((int)$existed_pages);
        
        if($state !== null && $state == 'inactive') {
            $list = $this->repository->findList(['active' => false]);
        } else {
            $list = $this->repository->findList();
        }
        $list = Normalizer::denormalizeList($list);
        $result['data'] = $list;
        $result['pagination'] = $pagination;

        return $result;
    }

    public function formatPagination(array $data): array
    {
        $result['data'] = $data;
        $result['pagination'] = $this->getPaginationData(Request::getPaginationParams());

        return $result;
    }

    private function getPaginationData(array $pagination, $constraints): array
    {
        /* TODO - $ avtive / inactive */
        $count_data = $this->repository->findCountData($constraints);
        if($count_data == 0)
            $count_data = 1;
        $page = (int)$pagination['page'] + 1;
        if (!$pagination['limit'])
            $pagination['limit'] = $count_data;
        $all_pages = ceil(($count_data / $pagination['limit']));

        $data['page'] = $page;
        $data['limit'] = $pagination['limit'];
        $data['pages'] = $all_pages;

        return $data;
    }
}

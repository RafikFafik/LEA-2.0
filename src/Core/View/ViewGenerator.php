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
    /**
     * @var array|null
     */
    private $pagination;

    function __construct(Repository $repository)
    {
        $this->repository = $repository;
        $this->pagination = Request::getPaginationParams();
    }
    
    public function getView(string $state): iterable
    {
        if($state !== null && $state == 'inactive')
            $list = $this->repository->findList(['active' => false]);
        else
            $list = $this->repository->findList();
            
        $list = Normalizer::denormalizeList($list);
        $result['data'] = $list;
        $result['pagination'] = $this->getPaginationData();

        return $result;
    }

    public function formatPagination(array $data): array
    {
        $result['data'] = $data;
        $result['pagination'] = $this->getPaginationData();

        return $result;
    }

    private function getPaginationData(): array
    {
        $count_data = $this->repository->findCountData();
        if($count_data == 0)
            $count_data = 1;
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

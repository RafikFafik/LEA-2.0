<?php

declare(strict_types=1);

namespace Lea\Module\ProductOfferModule\Service;

use Lea\Core\Service\Service;
use Lea\Core\Serializer\Normalizer;
use Lea\Core\View\ViewGenerator;
use Lea\Module\ContractorModule\Repository\ContractorRepository;

class ProductOfferService extends Service
{
    public function getView(): iterable
    {
        $list = $this->repository->findList();
        $contractor_repository = new ContractorRepository();
        foreach ($list as $obj) {
            $sums = $this->getProductsSums($obj->getProducts());
            $contractor = $contractor_repository->findById($obj->getContractorId());
            $obj->net_sum = $sums['net_sum'];
            $obj->gross_sum = $sums['gross_sum'];
            $obj->contractor_fullname = $contractor->getFullName();
        }

        $list = Normalizer::denormalizeList($list);
        $list = Normalizer::removeSpecificFieldsFromArrayList($list, ["products"]);
        $view = new ViewGenerator($this->repository);
        $array = $view->formatPagination($list);

        return $array;
    }

    private function getProductsSums(iterable $products): array
    {
        $net_sum = 0;
        $gross_sum = 0;
        foreach ($products as $product) {
            $price = $product->getNetPrice();
            $tax = $product->getVatRate();
            $net = $price->__get();
            $net_sum += $net;
            $gross_sum += ($net * ($tax / 100)) + $net;
        }

        return ['net_sum' => floatval(number_format($net_sum, 2)), 'gross_sum' => floatval(number_format($gross_sum, 2))];
    }
}

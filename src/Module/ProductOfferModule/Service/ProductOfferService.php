<?php

declare(strict_types=1);

namespace Lea\Module\ProductOfferModule\Service;

use Lea\Core\Type\Currency;
use Lea\Core\Service\Service;
use Lea\Core\View\ViewGenerator;
use Lea\Core\Serializer\Normalizer;
use Lea\Core\Validator\TypeValidator;
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
            $obj->setNetSum(new Currency($sums['net_sum'], TypeValidator::DATABASE));
            $obj->setGrossSum(new Currency($sums['gross_sum'], TypeValidator::DATABASE));
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
            $net_price = $product->getUnitNetPrice();
            $tax = $product->getVatRate();
            $net = $net_price->__get();
            $net_sum += $net;
            $gross_sum += ($net * ($tax / 100)) + $net;
        }

        return ['net_sum' => floatval(number_format($net_sum, 2)), 'gross_sum' => floatval(number_format($gross_sum, 2))];
    }
}

<?php

declare(strict_types=1);

namespace Lea\Module\ProductOfferModule\Service;

use Lea\Core\Service\Service;

class ProductOfferService extends Service
{
    public function getView(): iterable
    {
        $list = $this->repository->findList();
        foreach ($list as $obj) {
            $sums = $this->getProductsSums($obj->getProducts());
            $obj->net_sum = $sums['net_sum'];
            $obj->gross_sum = $sums['gross_sum'];
        }

        return $list;
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

        return ['net_sum' => $net_sum, 'gross_sum' => $gross_sum];
    }
}

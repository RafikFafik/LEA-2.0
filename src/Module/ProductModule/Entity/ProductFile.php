<?php

declare(strict_types=1);

namespace Lea\Module\ProductModule\Entity;

use Lea\Core\File\Entity\File;


class ProductFile extends File
{
    /**
     * @var int
     */
    private $product_id;

    /**
     * Get the value of product_id
     *
     * @return  int
     */ 
    public function getProductId()
    {
        return $this->product_id;
    }

    /**
     * Set the value of product_id
     *
     * @param  int  $product_id
     *
     * @return  self
     */ 
    public function setProductId(int $product_id)
    {
        $this->product_id = $product_id;

        return $this;
    }
}

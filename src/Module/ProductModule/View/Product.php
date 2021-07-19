<?php

declare(strict_types=1);

namespace Lea\Module\ProductModule\View;

use Lea\Core\View\View;

class Product extends View
{
  /**
   * @from Product
   * @Route()
   */
  private $product_name;

  /**
   * @from Product
   */
  private $product_model;

  /**
   * @from Product
   */
  private $unit_of_measure;

  /**
   * @from Product
   */
  private $price;

  /**
   * @from Product
   */
  private $vat_rate;

  /**
   * @from Product
   */
  private $producent;

  /**
   * @from Product
   */
  private $product_code;

  /**
   * @from Product
   */
  private $product_categories;

  /**
   * @from Product
   */
  private $type;

  /**
   * @from Product
   */
  private $description;

  /**
   * @from Product
   */
  private $product_files;

}

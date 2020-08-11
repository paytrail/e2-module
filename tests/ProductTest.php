<?php

declare(strict_types=1);

namespace Paytrail\Tests;

use Paytrail\E2Module\Product;
use Paytrail\Exceptions\ProductException;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testProductWithoutTitleThrowsException()
    {
        $this->expectException(ProductException::class);
        Product::create([
            'ITEM_UNIT_PRICE' => 10,
        ]);
    }

    public function testProductWithoutPriceThrowsException()
    {
        $this->expectException(ProductException::class);
        Product::create([
            'ITEM_TITLE' => 'Test Product',
        ]);
    }
}

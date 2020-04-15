<?php

declare(strict_types=1);

namespace Paytrail\E2Module;

/**
 * Product class for holding product details for payment.
 *
 * @package e2-module
 * @author Paytrail <tech@paytrail.com>
 */
class Product
{
    const TYPE_NORMAL = 1;
    const TYPE_SHIPMENT_COST = 2;
    const TYPE_HANDLING_COST = 3;

    public $ITEM_TITLE;
    public $ITEM_UNIT_PRICE;
    public $ITEM_QUANTITY;
    public $ITEM_TYPE;
    public $ITEM_VAT_PERCENT;
    public $ITEM_DISCOUNT_PERCENT;
    public $ITEM_ID;

    public static function create(array $productData)
    {
        $product = new Self();
        $product->ITEM_TITLE = $productData['ITEM_TITLE'];
        $product->ITEM_UNIT_PRICE = $productData['ITEM_UNIT_PRICE'];
        $product->ITEM_QUANTITY = $productData['ITEM_QUANTITY'] ?? 1;
        $product->ITEM_TYPE = $productData['ITEM_TYPE'] ?? self::TYPE_NORMAL;
        $product->ITEM_VAT_PERCENT = $productData['ITEM_VAT_PERCENT'] ?? 24;
        $product->ITEM_DISCOUNT_PERCENT = $productData['ITEM_DISCOUNT_PERCENT'] ?? null;
        $product->ITEM_ID = $productData['ITEM_ID'] ?? null;


        return $product;
    }
}

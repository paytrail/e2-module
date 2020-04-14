<?php

declare(strict_types=1);

namespace Paytrail\E2Module;

class Product
{
        const TYPE_NORMAL = 1;
        const TYPE_SHIPMENT_COST = 2;
        const TYPE_HANDLING_COST = 3;

        public $ITEM_TITLE;
        public $ITEM_ID;
        public $ITEM_QUANTITY;
        public $ITEM_UNIT_PRICE;
        public $ITEM_TYPE;
        public $ITEM_DISCOUNT_PERCENT;
        public $ITEM_VAT_PERCENT;

        public static function create(array $productData)
        {
                $product = new Self();
                $product->ITEM_TITLE = $productData['ITEM_TITLE'];
                $product->ITEM_ID = $productData['ITEM_ID'] ?? null;
                $product->ITEM_UNIT_PRICE = $productData['ITEM_UNIT_PRICE'];
                $product->ITEM_QUANTITY = $productData['ITEM_QUANTITY'] ?? 1;
                $product->ITEM_TYPE = $productData['ITEM_TYPE'] ?? self::TYPE_NORMAL;
                $product->ITEM_DISCOUNT_PERCENT = $productData['ITEM_DISCOUNT_PERCENT'] ?? null;
                $product->ITEM_VAT_PERCENT = $productData['ITEM_VAT_PERCENT'] ?? null;

                return $product;
        }
}

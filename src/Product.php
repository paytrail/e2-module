<?php

declare(strict_types=1);

namespace Paytrail\E2Module;

class Product
{
    const TYPE_NORMAL = 1;
    const TYPE_SHIPMENT_COST = 2;
    const TYPE_HANDLING_COST = 3;

    public $title;
    public $id;
    public $quantity;
    public $unitPrice;
    public $itemType;
    public $discount;
    public $vat;

    public static function create(array $productData)
    {
        $product = new Self();
        $product->title = $productData['title'];
        $product->id = $productData['id'];
        $product->unitPrice = $productData['unitPrice'];
        $product->quantity = $productData['quantity'] ?? 1;
        $product->itemType = $productData['itemType'] ?? self::TYPE_NORMAL;
        $product->discount = $productData['discount'] ?? 0;
        $product->vat = $productData['vat'] ?? 0;

        return $product;
    }
}

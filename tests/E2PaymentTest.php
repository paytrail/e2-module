<?php

declare(strict_types=1);

use Paytrail\E2Module\Customer;
use Paytrail\E2Module\E2Payment;
use Paytrail\E2Module\Product;
use PHPUnit\Framework\TestCase;

Class E2PaymentTest Extends TestCase
{
    const REQUIRED_PAYMENT_DATA = [
        'MERCHANT_ID',
        'URL_SUCCESS',
        'URL_CANCEL',
        'ORDER_NUMBER',
        'PARAMS_IN',
        'PARAMS_OUT',
        'AUTHCODE',
    ];


    private $e2Payment;
    private $product;
    private $customer;

    public function setUp(): void
    {
        parent::setUp();

        $this->e2Payment = new E2Payment('123', 'asd');
        $this->product = Product::create([
            'title' => 'Foo',
            'id' => '001',
            'unitPrice' => 2,
        ]);
        $this->customer = Customer::create([
            'firstName' => 'Foo',
            'lastName' => 'Bar',
        ]);
    }

    public function testExceptionIsThrownWithoutOrderNumber()
    {
        $this->expectException(Exception::class);
        $this->e2Payment->getPaymentForm();
    }

    public function testExceptionIsThrownWithoutProductsOrPrice()
    {
        $this->expectException(Exception::class);
        $this->e2Payment->createPayment('1234');
        $this->e2Payment->getPaymentForm();
    }

    public function testExceptionIsThrownWhenAddingProductWhenAmountIsSet()
    {
        $this->expectException(Exception::class);
        $this->e2Payment->addAmount(10);
        $this->e2Payment->addProducts([$this->product]);
    }

    public function testExceptionIsThrownWhenAddingAmountAndHasProducts()
    {
        $this->expectException(Exception::class);
        $this->e2Payment->addProducts([$this->product]);
        $this->e2Payment->addAmount(10);
    }

    public function testFormIsCreated()
    {
        $this->e2Payment->addProducts([$this->product]);
        $this->e2Payment->addCustomer($this->customer);
        $this->e2Payment->createPayment('order-123');

        $formData = $this->e2Payment->getPaymentForm();

        $this->assertNotEmpty($formData);

        foreach(self::REQUIRED_PAYMENT_DATA as $requiredData) {
            $this->assertStringContainsString($requiredData, $formData);
        }

        $this->assertStringContainsString('<input name="ITEM_TITLE[0]"', $formData);
    }

    public function testFormIsCreatedWithMiniumParameters()
    {
        $this->e2Payment->addAmount(15);
        $this->e2Payment->createPayment('order-123');

        $formData = $this->e2Payment->getPaymentForm();

        $this->assertNotEmpty($formData);

        foreach(self::REQUIRED_PAYMENT_DATA as $requiredData) {
            $this->assertStringContainsString($requiredData, $formData);
        }

        $this->assertStringContainsString('<input name="AMOUNT" type="hidden" value="15">', $formData);
    }

    public function testWidgetIsCreated()
    {
        $dummyUrl = 'dummyUrl/widget.js';

        $this->e2Payment->addProducts([$this->product]);
        $this->e2Payment->addCustomer($this->customer);
        $this->e2Payment->createPayment('order-123');

        $formData = $this->e2Payment->getPaymentWidget('Pay here', 'thisIsFormId', $dummyUrl);

        $this->assertNotEmpty($formData);

        foreach(self::REQUIRED_PAYMENT_DATA as $requiredData) {
            $this->assertStringContainsString($requiredData, $formData);
        }

        $this->assertStringContainsString('<input name="ITEM_TITLE[0]"', $formData);

        $this->assertStringContainsString($dummyUrl, $formData);
    }
}
<?php

declare(strict_types=1);

namespace Tests;

use Paytrail\E2Module\Customer;
use Paytrail\E2Module\E2Payment;
use Paytrail\E2Module\Product;
use PHPUnit\Framework\TestCase;

class E2PaymentTest extends TestCase
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

        $this->e2Payment = new E2Payment('13466', '6pKF4jkv97zmqBJ3ZL8gUw5DfT2NMQ');
        $this->product = Product::create([
            'ITEM_TITLE' => 'Foo',
            'ITEM_ID' => '001',
            'ITEM_UNIT_PRICE' => 2,
        ]);
        $this->customer = Customer::create([
            'PAYER_PERSON_FIRSTNAME' => 'Foo',
            'PAYER_PERSON_LASTNAME' => 'Bar',
        ]);
    }

    public function testExceptionIsThrownWithoutOrderNumber()
    {
        $this->expectException(\Exception::class);
        $this->e2Payment->getPaymentForm();
    }

    public function testExceptionIsThrownWithoutProductsOrPrice()
    {
        $this->expectException(\Exception::class);
        $this->e2Payment->createPayment('1234');
        $this->e2Payment->getPaymentForm();
    }

    public function testExceptionIsThrownWhenAddingProductWhenAmountIsSet()
    {
        $this->expectException(\Exception::class);
        $this->e2Payment->addAmount(10);
        $this->e2Payment->addProducts([$this->product]);
    }

    public function testExceptionIsThrownWhenAddingAmountAndHasProducts()
    {
        $this->expectException(\Exception::class);
        $this->e2Payment->addProducts([$this->product]);
        $this->e2Payment->addAmount(10);
    }

    public function testFormIsCreatedWithProductAndCustomerInformation()
    {
        $this->e2Payment->addProducts([$this->product]);
        $this->e2Payment->addCustomer($this->customer);
        $this->e2Payment->createPayment('order-123');

        $formData = $this->e2Payment->getPaymentForm();

        $this->assertNotEmpty($formData);

        foreach (self::REQUIRED_PAYMENT_DATA as $requiredData) {
            $this->assertStringContainsString($requiredData, $formData);
        }

        $this->assertStringContainsString('<input name="ITEM_TITLE[0]"', $formData);
        $this->assertStringContainsString('<input name="ITEM_ID[0]"', $formData);
        $this->assertStringContainsString('<input name="ITEM_UNIT_PRICE[0]"', $formData);
        $this->assertStringContainsString('<input name="ITEM_QUANTITY[0]"', $formData);
        $this->assertStringContainsString('<input name="PAYER_PERSON_FIRSTNAME"', $formData);
        $this->assertStringContainsString('<input name="PAYER_PERSON_LASTNAME"', $formData);
    }

    public function testFormIsCreatedWithOnlyAmountAndOrderNumber()
    {
        $this->e2Payment->addAmount(15);
        $this->e2Payment->createPayment('order-123');

        $formData = $this->e2Payment->getPaymentForm();

        $this->assertNotEmpty($formData);

        foreach (self::REQUIRED_PAYMENT_DATA as $requiredData) {
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

        foreach (self::REQUIRED_PAYMENT_DATA as $requiredData) {
            $this->assertStringContainsString($requiredData, $formData);
        }

        $this->assertStringContainsString('<input name="ITEM_TITLE[0]"', $formData);

        $this->assertStringContainsString($dummyUrl, $formData);
    }
}

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

    public function testPaymentCanBeConfirmed()
    {
        $urlData = [
            'ORDER_NUMBER' => 'Order-123',
            'PAYMENT_ID' => '109056237731',
            'AMOUNT' => '95.00',
            'CURRENCY' => 'EUR',
            'PAYMENT_METHOD' => '1',
            'TIMESTAMP' => '1586411733',
            'STATUS' => 'PAID',
            'RETURN_AUTHCODE' => '1CBCBC693DB07D0F0528C681F8C4B9FD974371588CDA1219D49911EB5D1CA53D',
        ];

        $this->assertTrue($this->e2Payment->paymentIsValid($urlData));
    }

    public function testExceptionIsThrownWhenMissingReturnParameter()
    {
        $urlData = [
            'ORDER_NUMBER' => 'Order-123',
            'PAYMENT_ID' => '109056237731',
            'AMOUNT' => '95.00',
            'PAYMENT_METHOD' => '1',
            'TIMESTAMP' => '1586411733',
            'STATUS' => 'PAID',
            'RETURN_AUTHCODE' => 'CA07B387D484F20E370EF4A4B7007588F0C5A3090F682CBCE440A97CFA75CCC2',
        ];

        $this->assertFalse($this->e2Payment->paymentIsValid($urlData));
        $this->assertCount(1, $this->e2Payment->getErrors());
        $this->assertStringContainsString('CURRENCY', $this->e2Payment->getErrors()[0]);
    }

    public function testExceptionIsThrownOnInvalidReturnAuthCode()
    {
        $urlData = [
            'ORDER_NUMBER' => 'Order-123',
            'PAYMENT_ID' => '109056237731',
            'AMOUNT' => '95.00',
            'CURRENCY' => 'EUR',
            'PAYMENT_METHOD' => '1',
            'TIMESTAMP' => '1586411733',
            'STATUS' => 'PAID',
            'RETURN_AUTHCODE' => '111111111111111',
        ];

        $this->assertFalse($this->e2Payment->paymentIsValid($urlData));
        $this->assertCount(1, $this->e2Payment->getErrors());
        $this->assertStringContainsString('RETURN_AUTHCODE', $this->e2Payment->getErrors()[0]);
    }
}
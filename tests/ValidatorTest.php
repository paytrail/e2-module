<?php

declare(strict_types=1);

namespace Tests;

use Paytrail\E2Module\Merchant;
use Paytrail\E2Module\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    private $validator;

    public function setUp(): void
    {
        parent::setUp();

        $merchant = Merchant::create('13466', '6pKF4jkv97zmqBJ3ZL8gUw5DfT2NMQ');
        $this->validator = new Validator($merchant);
    }

    public function testReturnAuthcodeIsValidReturnsTrueWithValidReturnValues()
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

        $this->assertTrue($this->validator->returnAuthcodeIsValid($urlData));
    }

    public function testReturnAuthcodeIsInvalidWhenMissingParameter()
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

        $this->assertFalse($this->validator->returnAuthcodeIsValid($urlData));
        $this->assertCount(1, $this->validator->getErrors());
        $this->assertStringContainsString('CURRENCY', $this->validator->getErrors()[0]);
    }

    public function testReturnAuthcodeIsInvalidWhitWrongAuthcode()
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

        $this->assertFalse($this->validator->returnAuthcodeIsValid($urlData));
        $this->assertCount(1, $this->validator->getErrors());
        $this->assertStringContainsString('RETURN_AUTHCODE', $this->validator->getErrors()[0]);
    }
}

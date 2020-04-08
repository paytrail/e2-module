<?php

declare(strict_types=1);

namespace Paytrail\E2Module;

class E2Payment
{
    const PAYMENT_URL = 'https://payment.paytrail.com/e2';

    const DEFAULT_ALGORITHM = 1;

    const DEFAULT_LOCALE = 'fi_FI';

    const PARAMS_IN = 'MERCHANT_ID,URL_SUCCESS,URL_CANCEL,ORDER_NUMBER,PARAMS_IN,PARAMS_OUT,MSG_UI_MERCHANT_PANEL,URL_NOTIFY,LOCALE,CURRENCY,REFERENCE_NUMBER,PAYMENT_METHODS,ALG';
    const PARAMS_OUT = 'ORDER_NUMBER,PAYMENT_ID,AMOUNT,CURRENCY,PAYMENT_METHOD,TIMESTAMP,STATUS';

    private $merchantId;
    private $merchantSecret;

    private $orderNUmber;

    private $products = [];
    private $amount;

    private $paymentData;

    public function __construct(string $merchantId, string $merchantSecret)
    {
        $this->merchantId = $merchantId;
        $this->merchantSecret = $merchantSecret;

        $this->paymentData['PARAMS_IN'] = self::PARAMS_IN;
        $this->paymentData['PARAMS_OUT'] = self::PARAMS_OUT;
        $this->paymentData['MERCHANT_ID'] = $this->merchantId;
        $this->paymentData['CURRENCY'] = 'EUR';
        $this->paymentData['ALG'] = self::DEFAULT_ALGORITHM;

        $this->getDefaultUrls();
    }

    private function getDefaultUrls(): void
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        $rootUrl = "{$protocol}://{$host}{$requestUri}";

        $this->paymentData['URL_SUCCESS'] = $rootUrl . 'success';
        $this->paymentData['URL_CANCEL'] = $rootUrl . 'cancel';
        $this->paymentData['URL_NOTIFY'] =$rootUrl . 'notify';
    }

    public function createPayment(string $orderNUmber, array $paymentData = []): void
    {
        $this->paymentData = array_merge($paymentData, $this->paymentData);
        $this->orderNUmber = $orderNUmber;

        $this->paymentData['ORDER_NUMBER'] = $orderNUmber;

        $this->paymentData['MSG_UI_MERCHANT_PANEL'] = $this->paymentData['MSG_UI_MERCHANT_PANEL'] ?? $this->orderNUmber;
        $this->paymentData['LOCALE'] = $this->paymentData['LOCALE'] ?? self::DEFAULT_LOCALE;
        $this->paymentData['REFERENCE_NUMBER'] = $this->paymentData['REFERENCE_NUMBER'] ?? null;
        $this->paymentData['PAYMENT_METHODS'] = $this->paymentData['PAYMENT_METHODS'] ?? null;
    }

    public function addCustomer(Customer $customer)
    {
        $this->paymentData['PAYER_PERSON_FIRSTNAME'] = $customer->firstName;
        $this->paymentData['PAYER_PERSON_LASTNAME'] = $customer->lastName;
        $this->paymentData['PAYER_PERSON_EMAIL'] = $customer->email;
        $this->paymentData['PAYER_PERSON_ADDR_STREET'] = $customer->streetAddress;
        $this->paymentData['PAYER_PERSON_ADDR_POSTAL_CODE'] = $customer->postalCode;
        $this->paymentData['PAYER_PERSON_ADDR_TOWN'] = $customer->town;
        $this->paymentData['PAYER_PERSON_ADDR_COUNTRY'] = $customer->countryCode;
        $this->paymentData['PAYER_PERSON_PHONE'] = $customer->phone;
        $this->paymentData['PAYER_COMPANY_NAME'] = $customer->company;

        $this->paymentData['PARAMS_IN'] .= ',PAYER_PERSON_PHONE,PAYER_PERSON_EMAIL,PAYER_PERSON_FIRSTNAME,PAYER_PERSON_LASTNAME,PAYER_COMPANY_NAME,PAYER_PERSON_ADDR_STREET,PAYER_PERSON_ADDR_POSTAL_CODE,PAYER_PERSON_ADDR_TOWN,PAYER_PERSON_ADDR_COUNTRY';
    }

    public function addAmount(float $amount): void
    {
        if (!empty($this->products)) {
            throw new \Exception('Either Amount of Product must be added, not both');
        }

        $this->amount = $amount;

        $this->paymentData['AMOUNT'] = $amount;
        $this->paymentData['PARAMS_IN'] .= ',AMOUNT';
    }

    public function addProducts(array $products): void
    {
        if ($this->amount) {
            throw new \Exception('Either Amount of Product must be added, not both');
        }

        $this->products = $products;

        $this->paymentData['PARAMS_IN'] .= ',VAT_IS_INCLUDED';
        $this->paymentData['VAT_IS_INCLUDED'] = $this->paymentData['VAT_IS_INCLUDED'] ?? 1;

        foreach ($this->products as $index => $product) {
            $this->paymentData["ITEM_TITLE[{$index}]"] = $product->title;
            $this->paymentData["ITEM_ID[{$index}]"] = $product->id;
            $this->paymentData["ITEM_QUANTITY[{$index}]"] = $product->quantity;
            $this->paymentData["ITEM_UNIT_PRICE[{$index}]"] = $product->unitPrice;
            $this->paymentData["ITEM_VAT_PERCENT[{$index}]"] = $product->vat;
            $this->paymentData["ITEM_DISCOUNT_PERCENT[{$index}]"] = $product->discount;
            $this->paymentData["ITEM_TYPE[{$index}]"] = $product->itemType;

            $this->paymentData['PARAMS_IN'] .= ",ITEM_TITLE[{$index}],ITEM_ID[{$index}],ITEM_QUANTITY[{$index}],ITEM_UNIT_PRICE[{$index}],ITEM_VAT_PERCENT[{$index}],ITEM_DISCOUNT_PERCENT[{$index}],ITEM_TYPE[{$index}]";
        }
    }

    public function getPaymentForm(string $buttonText = 'Pay here', string $formId = Form::DEFAULT_FORM_ID): string
    {
        $this->validatePaymentData();

        $this->paymentData['AUTHCODE'] = $this->calculateAuthCode();

        return Form::createWithoutWidget($this->paymentData, $buttonText, $formId);
    }

    public function getPaymentWidget(string $buttonText = 'Pay here', string $formId = Form::DEFAULT_FORM_ID, ?string $widgetUrl = null): string
    {
        $this->validatePaymentData();

        $this->paymentData['AUTHCODE'] = $this->calculateAuthCode();        

        return Form::create($this->paymentData, $buttonText, $formId, $widgetUrl);
    }

    private function calculateAuthCode(): string
    {
        $hashData = [$this->merchantSecret];
        $hashParams = explode(',', $this->paymentData['PARAMS_IN']);

        foreach($hashParams as $parameter) {
            $hashData[] = $this->paymentData[$parameter];
        }
        
        return strToUpper(hash('sha256', implode('|', $hashData)));
    }

    private function validatePaymentData(): void
    {
        if (!$this->orderNUmber) {
            throw new \Exception('No payment created.');
        }
        
        if (!$this->amount && empty($this->products)) {
            throw new \Exception('Either amount of at least one product must be added.');
        }
    }
}
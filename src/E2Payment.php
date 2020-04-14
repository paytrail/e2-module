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

    private $products = [];
    private $amount;

    private $paymentData = [];

    private $validator;

    public function __construct(string $merchantId, string $merchantSecret)
    {
        $this->validator = new Validator($merchantSecret);

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
        $rootUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $this->paymentData['URL_SUCCESS'] = $rootUrl . 'success';
        $this->paymentData['URL_CANCEL'] = $rootUrl . 'cancel';
        $this->paymentData['URL_NOTIFY'] = $rootUrl . 'notify';
    }

    public function createPayment(string $orderNUmber, array $paymentData = []): void
    {
        $this->paymentData = array_merge($paymentData, $this->paymentData);

        $this->paymentData['ORDER_NUMBER'] = $orderNUmber;

        $this->paymentData['MSG_UI_MERCHANT_PANEL'] = $this->paymentData['MSG_UI_MERCHANT_PANEL'] ?? $this->orderNUmber;
        $this->paymentData['MSG_SETTLEMENT_PAYER'] = $this->paymentData['MSG_UI_MERCHANT_PANEL'] ?? $this->orderNUmber;
        $this->paymentData['MSG_SETTLEMENT_MERCHANT'] = $this->paymentData['MSG_UI_MERCHANT_PANEL'] ?? $this->orderNUmber;
        $this->paymentData['MSG_UI_PAYMENT_METHOD'] = $this->paymentData['MSG_UI_MERCHANT_PANEL'] ?? $this->orderNUmber;

        $this->paymentData['LOCALE'] = $this->paymentData['LOCALE'] ?? self::DEFAULT_LOCALE;
        $this->paymentData['REFERENCE_NUMBER'] = $this->paymentData['REFERENCE_NUMBER'] ?? null;
        $this->paymentData['PAYMENT_METHODS'] = $this->paymentData['PAYMENT_METHODS'] ?? null;
    }

    public function addCustomer(Customer $customer)
    {
        foreach (get_object_vars($customer) as $key => $value) {
            if ($value === null) {
                continue;
            }
            $this->paymentData[$key] = $value;

            $this->paymentData['PARAMS_IN'] .= ',' . $key;
        }
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
            foreach (get_object_vars($product) as $key => $value) {
                if ($value === null) {
                    continue;
                }
                $key .= "[{$index}]";
                $this->paymentData[$key] = $value;

                $this->paymentData['PARAMS_IN'] .= ',' . $key;
            }
        }
    }

    public function getPaymentForm(string $formId = Form::DEFAULT_FORM_ID): string
    {
        $this->paymentData['AUTHCODE'] = $this->calculateAuthCode();

        return Form::createPaymentForm($this->paymentData, $buttonText, $formId);
    }

    public function getPaymentWidget(string $buttonText = 'Pay here', string $formId = Form::DEFAULT_FORM_ID, ?string $widgetUrl = null): string
    {
        $this->validatePaymentData();

        $this->paymentData['AUTHCODE'] = $this->calculateAuthCode();

        return Form::createPaymentWidget($this->paymentData, $buttonText, $formId, $widgetUrl);
    }

    private function calculateAuthCode(): string
    {
        $hashData = [$this->merchantSecret];
        $hashParams = explode(',', $this->paymentData['PARAMS_IN']);

        foreach ($hashParams as $parameter) {
            $hashData[] = $this->paymentData[$parameter];
        }

        return strToUpper(hash('sha256', implode('|', $hashData)));
    }

    private function validatePaymentData(): void
    {
        if (!$this->amount && empty($this->products)) {
            throw new \Exception('Either amount of atleast one product must be added.');
        }
    }

    public function paymentIsValid(array $returnParameters): bool
    {
        return $this->validator->paymentIsValid($returnParameters);
    }

    public function getErrors(): array
    {
        return $this->validator->getErrors();
    }
}

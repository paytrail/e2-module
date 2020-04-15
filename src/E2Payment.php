<?php

declare(strict_types=1);

namespace Paytrail\E2Module;

/**
 * Payment class for Paytrail E2 payment interface.
 *
 * @package e2-module
 * @author Paytrail <tech@paytrail.com>
 */
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

    /**
     * Get default return urls by appending current url with success, cancel or notify suffix.
     *
     * @return void
     */
    private function getDefaultUrls(): void
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        $rootUrl = "{$protocol}://{$host}{$requestUri}";

        $this->paymentData['URL_SUCCESS'] = $rootUrl . 'success';
        $this->paymentData['URL_CANCEL'] = $rootUrl . 'cancel';
        $this->paymentData['URL_NOTIFY'] = $rootUrl . 'notify';
    }

    /**
     * Create payment.
     *
     * @param string $orderNUmber
     * @param array $paymentData
     * @return void
     */
    public function createPayment(string $orderNUmber, array $paymentData = []): void
    {
        $this->paymentData = array_merge($paymentData, $this->paymentData);
        $this->orderNUmber = $orderNUmber;

        $this->paymentData['ORDER_NUMBER'] = $orderNUmber;

        $this->paymentData['MSG_UI_MERCHANT_PANEL'] = $this->paymentData['MSG_UI_MERCHANT_PANEL'] ?? $this->orderNUmber;
        $this->paymentData['MSG_SETTLEMENT_PAYER'] = $this->paymentData['MSG_UI_MERCHANT_PANEL'] ?? $this->orderNUmber;
        $this->paymentData['MSG_SETTLEMENT_MERCHANT'] = $this->paymentData['MSG_UI_MERCHANT_PANEL'] ?? $this->orderNUmber;
        $this->paymentData['MSG_UI_PAYMENT_METHOD'] = $this->paymentData['MSG_UI_MERCHANT_PANEL'] ?? $this->orderNUmber;

        $this->paymentData['LOCALE'] = $this->paymentData['LOCALE'] ?? self::DEFAULT_LOCALE;
        $this->paymentData['REFERENCE_NUMBER'] = $this->paymentData['REFERENCE_NUMBER'] ?? null;
        $this->paymentData['PAYMENT_METHODS'] = $this->paymentData['PAYMENT_METHODS'] ?? null;
    }

    /**
     * Add customer to payment.
     *
     * @param Customer $customer
     * @return void
     */
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

    /**
     * Add payment total amount.
     *
     * @param float $amount
     * @return void
     */
    public function addAmount(float $amount): void
    {
        if (!empty($this->products)) {
            throw new \Exception('Either Amount of Product must be added, not both');
        }

        $this->amount = $amount;

        $this->paymentData['AMOUNT'] = $amount;
        $this->paymentData['PARAMS_IN'] .= ',AMOUNT';
    }

    /**
     * Add product details to payment.
     *
     * @param array $products
     * @return void
     */
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

    /**
     * Get payment form with button to proceed Paytrail payment page.
     *
     * @param string $buttonText
     * @param string $formId
     * @return string
     */
    public function getPaymentForm(string $buttonText = 'Pay here', string $formId = Form::DEFAULT_FORM_ID): string
    {
        $this->validator->validatePaymentData($this->paymentData);

        $this->paymentData['AUTHCODE'] = $this->calculateAuthCode();

        return Form::createPaymentForm($this->paymentData, $buttonText, $formId);
    }

    /**
     * Get Paytrail payment widget.
     *
     * @param string $buttonText
     * @param string $formId
     * @param string|null $widgetUrl
     * @return string
     */
    public function getPaymentWidget(string $buttonText = 'Pay here', string $formId = Form::DEFAULT_FORM_ID, ?string $widgetUrl = null): string
    {
        $this->validator->validatePaymentData($this->paymentData);

        $this->paymentData['AUTHCODE'] = $this->calculateAuthCode();

        return Form::createPaymentWidget($this->paymentData, $buttonText, $formId, $widgetUrl);
    }

    /**
     * Calculate payment authcode.
     *
     * @return string
     */
    private function calculateAuthCode(): string
    {
        $hashData = [$this->merchantSecret];
        $hashParams = explode(',', $this->paymentData['PARAMS_IN']);

        foreach ($hashParams as $parameter) {
            $hashData[] = $this->paymentData[$parameter];
        }

        return strToUpper(hash('sha256', implode('|', $hashData)));
    }

    /**
     * Validate return authcode.
     *
     * @param array $returnParameters
     * @return boolean
     */
    public function returnAuthcodeIsValid(array $returnParameters): bool
    {
        return $this->validator->returnAuthcodeIsValid($returnParameters);
    }

    /**
     * Get return authcode errors.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->validator->getErrors();
    }

    public function isPaid(array $returnParameters): bool
    {
        return $returnParameters['STATUS'] === 'PAID';
    }
}

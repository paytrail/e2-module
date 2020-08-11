<?php

declare(strict_types=1);

namespace Paytrail\E2Module;

/**
 * Class for creating payment form for payment page.
 *
 * @package e2-module
 * @author Paytrail <tech@paytrail.com>
 */
class Form
{
    public const DEFAULT_FORM_ID = 'PaytrailPaymentForm';
    public const WIDGET_URL = 'https://payment.paytrail.com/js/payment-widget-v1.0.min.js';
    public const BUTTON_DEFAULT_TEXT = 'Pay here';

    /**
     * Create Paytrail payment form with widget.
     *
     * @param array $paymentData
     * @param string $buttonText
     * @param string $formId
     * @param string|null $widgetUrl
     * @return string
     */
    public static function createPaymentWidget(array $paymentData, string $buttonText, string $formId, ?string $widgetUrl): string
    {
        $templateData = [
            'paymentData' => $paymentData,
            'buttonText' => $buttonText,
            'formId' => $formId,
            'widgetUrl' => $widgetUrl ?? self::WIDGET_URL,
        ];

        return Template::render('PaymentWidget', $templateData);
    }

    /**
     * Create payment form with button to Paytrail payment page.
     *
     * @param array $paymentData
     * @param string $buttonText
     * @param string $formId
     * @return string
     */
    public static function createPaymentForm(array $paymentData, string $buttonText, string $formId): string
    {
        $templateData = [
            'paymentData' => $paymentData,
            'buttonText' => $buttonText,
            'formId' => $formId,
        ];

        return Template::render('PaymentForm', $templateData);
    }
}

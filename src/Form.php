<?php

declare(strict_types=1);

namespace Paytrail\E2Module;

/**
 * Class for creating payment form for payment page.
 *
 * @package E2-Module
 * @author Paytrail <tech@paytrail.com>
 */
class Form
{
    const DEFAULT_FORM_ID = 'PaytrailPaymentForm';
    const WIDGET_URL = 'https://payment.paytrail.com/js/payment-widget-v1.0.min.js';

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
        $formData = '<form action="' . E2Payment::PAYMENT_URL . '" id="' . $formId . '">';
        foreach ($paymentData as $key => $value) {
            $formData .= "<input name=\"{$key}\" type=\"hidden\" value=\"{$value}\">\n";
        }

        $widgetUrl = $widgetUrl ?? self::WIDGET_URL;

        $formData .= "<input type=\"submit\" value=\"{$buttonText}\"></form><script type=\"text/javascript\" src=\"{$widgetUrl}\">
        </script><script type=\"text/javascript\">SV.widget.initWithForm('{$formId}', {charset:'UTF-8'});</script>";

        return $formData;
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
        $formData = '<form action="' . E2Payment::PAYMENT_URL . '" id="' . $formId . '">';
        foreach ($paymentData as $key => $value) {
            $formData .= "<input name=\"{$key}\" type=\"hidden\" value=\"{$value}\">\n";
        }

        $formData .= "<input type=\"submit\" value=\"{$buttonText}\"></form>";

        return $formData;
    }
}

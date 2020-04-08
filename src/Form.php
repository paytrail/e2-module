<?php

declare(strict_types=1);

namespace Paytrail\E2Module;

class Form
{
    const DEFAULT_FORM_ID = 'PaytrailPaymentForm';
    const WIDGET_URL = 'https://payment.paytrail.com/js/payment-widget-v1.0.min.js';

    public static function create(array $paymentData, string $buttonText, string $formId, ?string $widgetUrl): string
    {

        $formData = "<form id=\"{$formId}\">";
        foreach ($paymentData as $key => $value) {
            $formData .= "<input name=\"{$key}\" type=\"hidden\" value=\"{$value}\">\n";
        }

        $widgetUrl = $widgetUrl ?? self::WIDGET_URL;

        $formData .= "<input type=\"submit\" value=\"{$buttonText}\"></form><script type=\"text/javascript\" src=\"{$widgetUrl}\">
        </script><script type=\"text/javascript\">SV.widget.initWithForm('{$formId}', {charset:'UTF-8'});</script>";

        return $formData;
    }

    public static function createWithoutWidget(array $paymentData, string $buttonText, string $formId): string
    {
        $formData = "<form id=\"{$formId}\">";
        foreach ($paymentData as $key => $value) {
            $formData .= "<input name=\"{$key}\" type=\"hidden\" value=\"{$value}\">\n";
        }

        $formData .= "<input type=\"submit\" value=\"{$buttonText}\"></form>";

        return $formData;
    }
}

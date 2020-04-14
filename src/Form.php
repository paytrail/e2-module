<?php

declare(strict_types=1);

namespace Paytrail\E2Module;

class Form
{
    const DEFAULT_FORM_ID = 'PaytrailPaymentForm';

    public static function createPaymentWidget(array $paymentData, string $buttonText, string $formId, ?string $widgetUrl): string
    {
        $formData = "<form action=\"https://payment.paytrail.com/e2\" id=\"{$formId}\">";
        foreach ($paymentData as $key => $value) {
            $formData .= "<input name=\"{$key}\" type=\"hidden\" value=\"{$value}\">\n";
        }

        $widgetUrl = $widgetUrl ?? self::WIDGET_URL;

        $formData .= "<input type=\"submit\" value=\"{$buttonText}\"></form><script type=\"text/javascript\" src=\"{$widgetUrl}\">
        </script><script type=\"text/javascript\">SV.widget.initWithForm('{$formId}', {charset:'UTF-8'});</script>";

        return $formData;
    }

    public static function createPaymentForm(array $paymentData, string $buttonText, string $formId): string
    {
        $formData = "<form action=\"https://payment.paytrail.com/e2\"  id=\"{$formId}\">";
        foreach ($paymentData as $key => $value) {
            $formData .= "<input name=\"{$key}\" type=\"hidden\" value=\"{$value}\">\n";
        }

        $formData .= "SV.widget.initWithForm('{$formId}', {charset:'UTF-8'});</script>";


        return $formData;
    }
}

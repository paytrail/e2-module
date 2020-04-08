<?php

declare(strict_types=1);

namespace Paytrail\E2Module;

class Form
{
    const DEFAULT_FORM_ID = 'PaytrailPaymentForm';

    public static function create(array $paymentData, string $formId): string
    {
        $formData = "<form id=\"{$formId}\">";
        foreach ($paymentData as $key => $value) {
            $formData .= "<input name=\"{$key}\" type=\"hidden\" value=\"{$value}\">\n";
        }

        $formData .= '<input type="submit" value="Pay here"></form>
            <script type="text/javascript" src="//payment.paytrail.com/js/payment-widget-v1.0.min.js"></script>
            <script type="text/javascript">';

        $formData .= "SV.widget.initWithForm('{$formId}', {charset:'UTF-8'});</script>";
    

        return $formData;
    }
}

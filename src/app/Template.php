<?php

declare(strict_types=1);

namespace Paytrail\E2Module;

/**
 * Templating class for including form templates.
 *
 * @package e2-module
 * @author Paytrail <tech@paytrail.com>
 */
class Template
{
    const TEMPLATE_PATH = '/../Templates/';

    /**
     * Extract data variables from array and render template from template folder, use basename to chroot in template directory.
     *
     * @param string $templateName
     * @param array $data
     * @return void
     */
    public static function render(string $templateName, array $data)
    {
        foreach ($data as $key => $value) {
            $$key = $value;
        }

        $paymentUrl =  E2Payment::PAYMENT_URL;

        ob_start();
        include __DIR__ . self::TEMPLATE_PATH . basename($templateName) . '.phtml';
        $formData = ob_get_contents();
        ob_end_clean();

        return $formData;
    }
}

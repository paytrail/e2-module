<?php

declare(strict_types=1);

namespace Paytrail\E2Module;

use Paytrail\Exceptions\TemplateException;

/**
 * Templating class for including form templates.
 *
 * @package e2-module
 * @author Paytrail <tech@paytrail.com>
 */
class Template
{
    const TEMPLATE_PATH = '/../templates/';

    /**
     * Extract data variables from array and render template from template folder, use basename to chroot in template directory.
     *
     * @param string $templateName
     * @param array $data
     * @return void
     * @throws TemplateException
     */
    public static function render(string $templateName, array $data = []): string
    {
        $templateFile = __DIR__ . self::TEMPLATE_PATH . basename($templateName) . '.phtml';

        if (!file_exists($templateFile)) {
            throw new TemplateException("Template for {$templateName} not found");
        }

        foreach ($data as $key => $value) {
            $$key = $value;
        }

        $paymentUrl =  E2Payment::PAYMENT_URL;

        ob_start();
        include $templateFile;
        $formData = ob_get_contents();
        ob_end_clean();

        return $formData;
    }
}

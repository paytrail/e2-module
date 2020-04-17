<?php

declare(strict_types=1);

namespace Paytrail\E2Module;

use Paytrail\Exceptions\ProductException;

/**
 * Class for calculating authcode and return authcode.
 *
 * @package e2-module
 * @author Paytrail <tech@paytrail.com>
 */
class Authcode
{
    /**
     * Calculate payment authcode.
     *
     * @param array $paymentData
     * @param Merchant $merchant
     * @return string
     */
    public static function calculateAuthcode(array $paymentData, Merchant $merchant): string
    {
        $hashData = [$merchant->secret];
        $hashParams = explode(',', $paymentData['PARAMS_IN']);

        foreach ($hashParams as $parameter) {
            $hashData[] = $paymentData[$parameter];
        }

        return strToUpper(hash('sha256', implode('|', $hashData)));
    }

    /**
     * Calculate expected return authcode.
     *
     * @param array $returnParameters
     * @return string
     */
    public static function calculateReturnAuthCode(array $returnParameters, Merchant $merchant): string
    {
        $returnParameters[] = $merchant->secret;
        unset($returnParameters['RETURN_AUTHCODE']);

        return strToUpper(hash('sha256', implode('|', $returnParameters)));
    }
}

<?php

declare(strict_types=1);

namespace Paytrail\E2Module;

use Paytrail\Exceptions\ValidationException;

/**
 * Validator class for validating outgoing and incoming payment data.
 *
 */
class Validator
{
    private $errors = [];
    private $merchant;

    public function __construct(Merchant $merchant)
    {
        $this->merchant = $merchant;
    }

    /**
     * Make sure payment has order number and either price or at least one product. Throws ValidationException if either one is missing.
     *
     * @param array $paymentData
     * @return void
     * @throws ValidationException
     */
    public function validatePaymentData(array $paymentData): void
    {
        if (!isset($paymentData['ORDER_NUMBER'])) {
            throw new ValidationException('ORDER_NUMBER is missing.');
        }

        if (!isset($paymentData['AMOUNT']) && !isset($paymentData['ITEM_TITLE[0]'])) {
            throw new ValidationException('Either AMOUNT or at least one product must be added.');
        }
    }

    /**
     * Validate return authcode against expected parameters and expected authcode matches actual.
     *
     * @param array $returnParameters
     * @return boolean
     */
    public function returnAuthcodeIsValid(array $returnParameters): bool
    {
        $requiredParameters = explode(',', E2Payment::PARAMS_OUT);
        foreach ($requiredParameters as $requiredParameter) {
            if (!array_key_exists($requiredParameter, $returnParameters)) {
                $this->errors[] = "Missing parameter {$requiredParameter}";
            }
        }

        if (Authcode::calculateReturnAuthCode($returnParameters, $this->merchant) !== $returnParameters['RETURN_AUTHCODE']) {
            $this->errors[] = 'Invalid RETURN_AUTHCODE';
        }

        if (empty($this->errors)) {
            return true;
        }

        return false;
    }

    /**
     * Get return return validation errors.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}

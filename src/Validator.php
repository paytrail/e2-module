<?php

declare(strict_types=1);

namespace Paytrail\E2Module;

/**
 * Validator class for validating outgoing and incoming payment data.
 *
 * @package E2-Module
 * @author Paytrail <tech@paytrail.com>
 */
class Validator
{
    private $errors = [];
    private $merchantSecret;

    public function __construct(string $merchantSecret)
    {
        $this->merchantSecret = $merchantSecret;
    }

    /**
     * Make sure payment has order number and either price or at least one product. Throw an exception if either one is missing.
     *
     * @param array $paymentData
     * @return void
     * @throws \Exception
     */
    public function validatePaymentData(array $paymentData): void
    {
        if (!isset($paymentData['ORDER_NUMBER'])) {
            throw new \Exception('No payment created.');
        }
        
        if (!isset($paymentData['AMOUNT']) && !isset($paymentData['ITEM_TITLE[0]'])) {
            throw new \Exception('Either amount of at least one product must be added.');
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

        if ($this->calculateReturnAuthCode($returnParameters) !== $returnParameters['RETURN_AUTHCODE']) {
            $this->errors[] = 'Invalid RETURN_AUTHCODE';
        }

        if (empty($this->errors)) {
            return true;
        }

        return false;
    }

    /**
     * Calculate expected return auhtcode.
     *
     * @param array $returnParameters
     * @return string
     */
    private function calculateReturnAuthCode(array $returnParameters): string
    {
        $returnParameters[] = $this->merchantSecret;
        unset($returnParameters['RETURN_AUTHCODE']);
        return strToUpper(hash('sha256', implode('|', $returnParameters)));
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

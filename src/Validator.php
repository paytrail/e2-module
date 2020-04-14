<?php

declare(strict_types=1);

namespace Paytrail\E2Module;

class Validator
{
    private $errors = [];
    private $merchantSecret;

    public function __construct(string $merchantSecret)
    {
        $this->merchantSecret = $merchantSecret;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function paymentIsValid(array $returnParameters): bool
    {
        $requiredParameters = explode(',', E2Payment::PARAMS_OUT);
        foreach($requiredParameters as $requiredParameter) {
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

    private function calculateReturnAuthCode(array $returnParameters): string
    {
        $returnParameters[] = $this->merchantSecret;
        unset($returnParameters['RETURN_AUTHCODE']);
        return strToUpper(hash('sha256', implode('|', $returnParameters)));
    }
}

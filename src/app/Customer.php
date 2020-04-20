<?php

declare(strict_types=1);

namespace Paytrail\E2Module;

/**
 * Customer class for holding customer details for payment.
 *
 * @package e2-module
 * @author Paytrail <tech@paytrail.com>
 */
class Customer
{
    public $PAYER_PERSON_FIRSTNAME;
    public $PAYER_PERSON_LASTNAME;
    public $PAYER_PERSON_EMAIL;
    public $PAYER_PERSON_ADDR_STREET;
    public $PAYER_PERSON_ADDR_POSTAL_CODE;
    public $PAYER_PERSON_ADDR_TOWN;
    public $PAYER_PERSON_ADDR_COUNTRY;
    public $PAYER_PERSON_PHONE;
    public $PAYER_COMPANY_NAME;

    /**
     * Create customer for payment.
     *
     * @param array $customerData
     * @return void
     */
    public static function create(array $customerData): self
    {
        $customer = new self();
        $customer->PAYER_PERSON_FIRSTNAME = $customerData['PAYER_PERSON_FIRSTNAME'] ?? null;
        $customer->PAYER_PERSON_LASTNAME = $customerData['PAYER_PERSON_LASTNAME'] ?? null;
        $customer->PAYER_PERSON_EMAIL = $customerData['PAYER_PERSON_EMAIL'] ?? null;
        $customer->PAYER_PERSON_ADDR_STREET = $customerData['PAYER_PERSON_ADDR_STREET'] ?? null;
        $customer->PAYER_PERSON_ADDR_POSTAL_CODE = $customerData['PAYER_PERSON_ADDR_POSTAL_CODE'] ?? null;
        $customer->PAYER_PERSON_ADDR_TOWN = $customerData['PAYER_PERSON_ADDR_TOWN'] ?? null;
        $customer->PAYER_PERSON_ADDR_COUNTRY = $customerData['PAYER_PERSON_ADDR_COUNTRY'] ?? null;
        $customer->PAYER_PERSON_PHONE = $customerData['PAYER_PERSON_PHONE'] ?? null;
        $customer->PAYER_COMPANY_NAME = $customerData['PAYER_COMPANY_NAME'] ?? null;

        return $customer;
    }
}

<?php

declare(strict_types=1);

namespace Paytrail\E2Module;

class Customer
{
    const TYPE_NORMAL = 1;
    const TYPE_SHIPMENT_COST = 2;
    const TYPE_HANDLING_COST = 3;

    public $firstName;
    public $lastName;
    public $email;
    public $streetAddress;
    public $postalCode;
    public $town;
    public $countryCode;
    public $phone;
    public $company;

    public static function create(array $customerData)
    {
        $customer = new Self();
        $customer->firstName = $customerData['firstName'];
        $customer->lastName = $customerData['lastName'];
        $customer->email = $customerData['email'] ?? null;
        $customer->streetAddress = $customerData['streetAddress'] ?? null;
        $customer->postalCode = $customerData['postalCode'] ?? null;
        $customer->town = $customerData['town'] ?? null;
        $customer->countryCode = $customerData['countryCode'] ?? 'FI';
        $customer->phone = $customerData['phone'] ?? null;
        $customer->company = $customerData['company'] ?? null;

        return $customer;
    }
}

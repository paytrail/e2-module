# e2-module
Helper for creating payments with Paytrail Form Interface (E2)

## Installation
Install via composer

```bash
composer require Paytrail/e2-module
```

## Documentation

Paytrail official documentation can be found in [https://docs.paytrail.com](https://docs.paytrail.com)

## Examples

### Payment without customer and production information

```php
use Paytrail\E2Module\E2Payment;

$e2Payment = new E2Payment($merchantNumber, $merchantSecret);
$e2Payment->addAmount($orderAmount);
$e2Payment->createPayment($orderNumber);

echo $e2Payment->getPaymentForm();
```

### Payment with customer, production information and custom return urls

Include customer information, discounted product and custom return urls.
Payment, customer and product properties can be found from [documentation](https://docs.paytrail.com)

```php
use Paytrail\E2Module\E2Payment;
use Paytrail\E2Module\Product;
use Paytrail\E2Module\Customer;

$e2Payment = new E2Payment($merchantNumber, $merchantSecret);

$customer = Customer::create([
    'PAYER_PERSON_FIRSTNAME' => 'Test',
    'PAYER_PERSON_LASTNAME' => 'Customer',
    'PAYER_PERSON_EMAIL' => 'customer.email@nomail.com',
    'PAYER_PERSON_ADDR_STREET' => 'Test street 1',
    'PAYER_PERSON_ADDR_POSTAL_CODE' => '100200',
    'PAYER_PERSON_ADDR_TOWN' => 'Helsinki',
    'PAYER_PERSON_ADDR_COUNTRY' => 'FI',
    'PAYER_PERSON_PHONE' => '040123456' ,
]);
$e2Payment->addCustomer($customer);

$paymentData = [
    'URL_SUCCESS' => 'https://url/to/shop/successUrl',
    'URL_CANCEL' => 'https://url/to/shop/cancelUrl',
    'URL_NOTIFY' => 'https://url/to/shop/notifyUrl',
];

$e2Payment->createPayment($orderNumber, $paymentData);

$product = product::create([
    'ITEM_TITLE' => 'Test Product',
    'ITEM_ID' => '1234',
    'ITEM_UNIT_PRICE' => 50,
    'ITEM_QUANTITY' => 2,
    'ITEM_DISCOUNT_PERCENT' => 10,
]);
$shipping = product::create([
    'ITEM_TITLE' => 'Shipping',
    'ITEM_ID' => '001',
    'ITEM_UNIT_PRICE' => 5,
    'ITEM_TYPE' => Product::TYPE_SHIPMENT_COST,
]);
$e2Payment->addProducts([$product, $shipping]);

echo $e2Payment->getPaymentWidget();
```
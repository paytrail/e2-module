# e2-module
Helper for creating payments with Paytrail Form Interface (E2)

## Installation
Install via composer

```bash
composer require Paytrail/e2-module
```

## Documentation

Paytrail official documentation can be found in [https://docs.paytrail.com/en](https://docs.paytrail.com/en)

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

```php
use Paytrail\E2Module\E2Payment;
use Paytrail\E2Module\Product;
use Paytrail\E2Module\Customer;

$e2Payment = new E2Payment($merchantNumber, $merchantSecret);

$customer = Customer::create([
    'firstName' => 'Test',
    'lastName' => 'Customer',
    'email' => 'customer.email@nomail.com',
    'streetAddress' => 'Test street 1',
    'postalCode' => '100200',
    'town' => 'Helsinki',
    'countryCode' => 'FI',
    'phone' => '040123456' ,
]);
$e2Payment->addCustomer($customer);

$paymentData = [
    'URL_SUCCESS' => 'https://url/to/shop/successUrl';
    'URL_CANCEL' => 'https://url/to/shop/cancelUrl';
    'URL_NOTIFY' => 'https://url/to/shop/notifyUrl';
];
$e2Payment->createPayment($orderNumber, $paymentData);

$product = product::create([
    'title' => 'Test Product',
    'id' => '1234',
    'unitPrice' => 50,
    'quantity' => 2,
    'discount' => 10,
]);
$shipping = product::create([
    'title' => 'Shipping',
    'id' => '001',
    'unitPrice' => 5,
    'type' => Product::TYPE_SHIPMENT_COST,
]);
$e2Payment->addProducts([$product, $shipping]);

echo $e2Payment->getPaymentForm();
```
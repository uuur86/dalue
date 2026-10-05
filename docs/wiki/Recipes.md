# Recipes

## Mapping a webhook payload for AWS DynamoDB

```php
use Aws\DynamoDb\Marshaler;
use Dalue\Mapper;
use StrObj\StringObjects;

$data = StringObjects::instance($payload);

$item = Mapper::map($data, [
    'PK'          => '@data/transaction_id',
    'SK'          => 'METADATA',
    'CustomerId'  => '@data/customer/account_no',
    'TotalAmount' => '@data/purchase_details/total_amount',
    'OrderItems[]' => [
        'SKU'      => '@data/purchase_details/items/*/sku',
        'Quantity' => '@data/purchase_details/items/*/qty',
        'Price'    => '@data/purchase_details/items/*/price',
    ],
], function ($key, $value) {
    return $key === 'PK' ? 'ORDER#' . $value : $value;
});

$dynamoItem = (new Marshaler())->marshalItem($item);
```

## Renaming API fields for a database row

```php
$row = Mapper::map(StringObjects::instance($apiUser), [
    'external_id' => '@data/id',
    'full_name'   => '@data/profile/name',
    'email'       => '@data/contact/email',
    'created_at'  => '@data/meta/created',
], function ($key, $value) {
    return $key === 'created_at' && $value !== null ? date('Y-m-d H:i:s', strtotime($value)) : $value;
});
```

## Casting types

```php
$casts = ['id' => 'intval', 'price' => 'floatval', 'active' => 'boolval'];

$result = Mapper::map($data, $schema, function ($key, $value) use ($casts) {
    return isset($casts[$key]) && is_scalar($value) ? $casts[$key]($value) : $value;
});
```

## Reusing schemas

Schemas are plain arrays, so they can be stored as constants, composed and
reused:

```php
final class OrderSchema
{
    public const CUSTOMER = [
        'id'   => '@data/customer/account_no',
        'name' => '@data/customer/full_name',
    ];

    public const ORDER = [
        'id'       => '@data/transaction_id',
        'customer' => self::CUSTOMER,
    ];
}

$order = Mapper::map($data, OrderSchema::ORDER);
```

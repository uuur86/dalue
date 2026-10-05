<?php

/**
 * Example 6: webhook payload to a DynamoDB-style item.
 *
 * Maps an e-commerce webhook into a flat order record, then converts it to the
 * DynamoDB AttributeValue format. In a real project use Aws\DynamoDb\Marshaler;
 * the small toAttributeValue() helper keeps this example free of the AWS SDK.
 * Run: php examples/06-webhook-to-dynamodb.php
 */

require __DIR__ . '/bootstrap.php';

use Dalue\Mapper;
use StrObj\StringObjects;

$payload = [
    'transaction_id' => 'tx_987654321',
    'customer' => [
        'account_no' => 'CUST-1002',
        'full_name' => 'John Doe',
    ],
    'purchase_details' => [
        'total_amount' => 150.50,
        'items' => [
            ['sku' => 'ITEM-01', 'qty' => 2, 'price' => 50.00],
            ['sku' => 'ITEM-02', 'qty' => 1, 'price' => 50.50],
        ],
    ],
];

$order = Mapper::map(StringObjects::instance($payload), [
    'PK' => '@data/transaction_id',
    'SK' => 'METADATA',
    'CustomerId' => '@data/customer/account_no',
    'TotalAmount' => '@data/purchase_details/total_amount',
    'OrderItems[]' => [
        'SKU' => '@data/purchase_details/items/*/sku',
        'Quantity' => '@data/purchase_details/items/*/qty',
        'Price' => '@data/purchase_details/items/*/price',
    ],
], function ($key, $value) {
    return $key === 'PK' ? 'ORDER#' . $value : $value;
});

show('Mapped order', $order);

/**
 * Converts a PHP value to a DynamoDB AttributeValue.
 *
 * @param mixed $value Value to convert
 *
 * @return array<string, mixed>
 */
function toAttributeValue($value): array
{
    if (is_array($value)) {
        $isList = $value === [] || array_keys($value) === range(0, count($value) - 1);

        return $isList
            ? ['L' => array_map('toAttributeValue', $value)]
            : ['M' => array_map('toAttributeValue', $value)];
    }

    if (is_int($value) || is_float($value)) {
        return ['N' => (string) $value];
    }

    if (is_bool($value)) {
        return ['BOOL' => $value];
    }

    if ($value === null) {
        return ['NULL' => true];
    }

    return ['S' => (string) $value];
}

show('DynamoDB item', toAttributeValue($order)['M']);

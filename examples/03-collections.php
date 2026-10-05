<?php

/**
 * Example 3: collections.
 *
 * A key ending with "[]" turns wildcard columns into rows. Items without a field
 * get null, so the rows stay aligned with the source items.
 * Run: php examples/03-collections.php
 */

require __DIR__ . '/bootstrap.php';

use Dalue\Mapper;
use StrObj\StringObjects;

$data = StringObjects::instance([
    'order' => 'SO-1001',
    'items' => [
        ['sku' => 'ITEM-01', 'qty' => 2, 'price' => 50.0],
        ['qty' => 1, 'price' => 9.99],
        ['sku' => 'ITEM-03', 'qty' => 5, 'price' => 1.5],
    ],
]);

$columns = [
    'sku' => '@data/items/*/sku',
    'quantity' => '@data/items/*/qty',
];

show('Without [] the columns are returned', Mapper::map($data, ['lines' => $columns]));

show('With [] the columns become rows', Mapper::map($data, [
    'order' => '@data/order',
    'lines[]' => $columns,
]));

show('mergeColumns() can be used on its own', Mapper::mergeColumns([
    'name' => ['Ann', 'Bob', 'Cid'],
    'score' => [90, 75],
]));

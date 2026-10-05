<?php

/**
 * Example 4: callbacks.
 *
 * The callback receives every key and value (children first, then the parent)
 * and returns the value to store.
 * Run: php examples/04-callbacks.php
 */

require __DIR__ . '/bootstrap.php';

use Dalue\Mapper;
use StrObj\StringObjects;

$data = StringObjects::instance([
    'id' => '17',
    'email' => 'Jane.Doe@Example.COM',
    'price' => '19.90',
    'created' => '2026-01-15T08:30:00+00:00',
    'tags' => ['new', 'vip'],
]);

$schema = [
    'id' => '@data/id',
    'email' => '@data/email',
    'price' => '@data/price',
    'createdAt' => '@data/created',
    'tags' => '@data/tags',
];

$casts = [
    'id' => 'intval',
    'price' => 'floatval',
    'email' => 'strtolower',
];

show('Casting and formatting values', Mapper::map($data, $schema, function ($key, $value) use ($casts) {
    if (isset($casts[$key])) {
        return $casts[$key]($value);
    }

    if ($key === 'createdAt') {
        return (new DateTimeImmutable($value))->format('Y-m-d H:i:s');
    }

    if ($key === 'tags') {
        return implode(',', $value);
    }

    return $value;
}));

$calls = [];

Mapper::map($data, [
    'id' => '@data/id',
    'meta' => ['firstTag' => '@data/tags/0'],
], function ($key, $value) use (&$calls) {
    $calls[] = $key;

    return $value;
});

show('Callback order (children first)', $calls);

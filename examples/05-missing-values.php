<?php

/**
 * Example 5: stored falsy values and missing paths.
 *
 * Stored values are returned unchanged. Missing paths return null, or the
 * default given as the fourth argument.
 * Run: php examples/05-missing-values.php
 */

require __DIR__ . '/bootstrap.php';

use Dalue\Mapper;
use StrObj\StringObjects;

$data = StringObjects::instance([
    'active' => false,
    'balance' => 0,
    'nickname' => '',
    'manager' => null,
]);

$schema = [
    'active' => '@data/active',
    'balance' => '@data/balance',
    'nickname' => '@data/nickname',
    'manager' => '@data/manager',
    'phone' => '@data/contact/phone',
];

show('Default for missing paths is null', Mapper::map($data, $schema));
show('Custom default for missing paths', Mapper::map($data, $schema, null, 'N/A'));

<?php

/**
 * Example 1: basic mapping.
 *
 * Reads values with "@data/" paths, copies static values and maps a nested schema.
 * Run: php examples/01-basic-mapping.php
 */

require __DIR__ . '/bootstrap.php';

use Dalue\Mapper;
use StrObj\StringObjects;

$data = StringObjects::instance([
    'user' => [
        'id' => 1,
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
        'details' => [
            'age' => 30,
            'city' => 'New York',
        ],
    ],
]);

$schema = [
    'userID' => '@data/user/id',
    'userName' => '@data/user/name',
    'userDetails' => [
        'userAge' => '@data/user/details/age',
        'userCity' => '@data/user/details/city',
    ],
    'source' => 'crm',
];

show('Mapped user', Mapper::map($data, $schema));

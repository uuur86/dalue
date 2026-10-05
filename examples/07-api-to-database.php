<?php

/**
 * Example 7: API response to database rows.
 *
 * Maps a paginated API response with reusable schema constants and turns the
 * users into rows ready for an INSERT statement.
 * Run: php examples/07-api-to-database.php
 */

require __DIR__ . '/bootstrap.php';

use Dalue\Mapper;
use StrObj\StringObjects;

final class UserSchema
{
    public const PAGE = [
        'page' => '@data/meta/page',
        'total' => '@data/meta/total',
        'users[]' => [
            'external_id' => '@data/data/*/id',
            'full_name' => '@data/data/*/profile/name',
            'email' => '@data/data/*/contact/email',
            'is_active' => '@data/data/*/status',
        ],
    ];
}

$response = <<<'JSON'
{
    "meta": {"page": 1, "total": 3},
    "data": [
        {"id": 101, "profile": {"name": "Ada Lovelace"}, "contact": {"email": "ADA@example.com"}, "status": "active"},
        {"id": 102, "profile": {"name": "Alan Turing"}, "contact": {}, "status": "disabled"},
        {"id": 103, "profile": {"name": "Grace Hopper"}, "contact": {"email": "grace@example.com"}, "status": "active"}
    ]
}
JSON;

$page = Mapper::map(StringObjects::instance($response), UserSchema::PAGE, function ($key, $value) {
    switch ($key) {
        case 'email':
            return array_map(function ($email) {
                return $email === null ? null : strtolower($email);
            }, $value);
        case 'is_active':
            return array_map(function ($status) {
                return $status === 'active' ? 1 : 0;
            }, $value);
        default:
            return $value;
    }
});

show('Mapped page', $page);

$columns = array_keys($page['users'][0]);
$placeholders = '(' . implode(', ', array_fill(0, count($columns), '?')) . ')';

show('Prepared INSERT', [
    'sql' => sprintf(
        'INSERT INTO users (%s) VALUES %s',
        implode(', ', $columns),
        implode(', ', array_fill(0, count($page['users']), $placeholders))
    ),
    'bindings' => array_merge(...array_map('array_values', $page['users'])),
]);

<?php

/**
 * Example 2: mapping a JSON document.
 *
 * StringObjects::instance() accepts JSON strings. List items are addressed by index.
 * Run: php examples/02-json-input.php
 */

require __DIR__ . '/bootstrap.php';

use Dalue\Mapper;
use StrObj\StringObjects;

$json = <<<'JSON'
{
    "repository": {
        "full_name": "uuur86/dalue",
        "owner": {"login": "uuur86"},
        "topics": ["php", "mapper", "json"]
    },
    "stats": {"stars": 42, "forks": 7}
}
JSON;

$data = StringObjects::instance($json);

show('Repository card', Mapper::map($data, [
    'name' => '@data/repository/full_name',
    'owner' => '@data/repository/owner/login',
    'mainTopic' => '@data/repository/topics/0',
    'topics' => '@data/repository/topics',
    'popularity' => [
        'stars' => '@data/stats/stars',
        'forks' => '@data/stats/forks',
    ],
]));

try {
    StringObjects::instance('{invalid json');
} catch (InvalidArgumentException $exception) {
    show('Invalid JSON is rejected', [
        'exception' => get_class($exception),
        'code' => $exception->getCode(),
    ]);
}

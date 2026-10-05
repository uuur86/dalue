# Getting Started

Mapping always has three steps.

## 1. Load the source data

`StringObjects::instance()` accepts an array, an object or a JSON string.

```php
use StrObj\StringObjects;

$data = StringObjects::instance([
    'user' => [
        'id'      => 1,
        'name'    => 'John Doe',
        'email'   => 'john.doe@example.com',
        'details' => ['age' => 30, 'city' => 'New York'],
    ],
]);
```

Invalid JSON or unsupported input throws `InvalidArgumentException`; see the
[StrObj error handling guide](https://github.com/uuur86/strobj/wiki/Error-Handling).

## 2. Describe the result with a schema

The schema is an array whose keys are the keys of the result. Values that start
with `@data/` are read from the source data; everything else is copied as is.

```php
$schema = [
    'userID'      => '@data/user/id',
    'userName'    => '@data/user/name',
    'userDetails' => [
        'userAge'  => '@data/user/details/age',
        'userCity' => '@data/user/details/city',
    ],
    'mappedAt'    => date('c'),
];
```

## 3. Map

```php
use Dalue\Mapper;

$result = Mapper::map($data, $schema);
```

```php
[
    'userID'      => 1,
    'userName'    => 'John Doe',
    'userDetails' => ['userAge' => 30, 'userCity' => 'New York'],
    'mappedAt'    => '2026-10-05T10:00:00+00:00',
]
```

## Method signature

```php
Mapper::map(
    StringObjects $data,       // source data
    array $schema,             // result structure
    ?callable $callback = null, // optional value transformer
    $default = null            // value used for missing paths
): array
```

Continue with the [Schema Reference](Schema-Reference).

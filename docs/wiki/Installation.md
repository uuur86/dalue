# Installation

## Requirements

| Requirement | Version |
| --- | --- |
| PHP | 7.4 or later (tested on 7.4 – 8.5) |
| [uuur86/strobj](https://github.com/uuur86/strobj) | 3.x, installed automatically |

## Composer

```sh
composer require uuur86/dalue
```

Load Composer's autoloader in your application:

```php
require __DIR__ . '/vendor/autoload.php';
```

## Verifying the installation

```php
use Dalue\Mapper;
use StrObj\StringObjects;

var_dump(Mapper::map(StringObjects::instance(['ok' => true]), ['ok' => '@data/ok']));
// array(1) { ["ok"]=> bool(true) }
```

Next: [Getting Started](Getting-Started).

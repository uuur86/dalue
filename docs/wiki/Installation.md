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

## Trying the examples

Clone the repository and run the example scripts:

```sh
git clone https://github.com/uuur86/dalue.git
cd dalue
composer install
composer examples
```

See [Examples](Examples) for what each script shows and how to verify the output.

Next: [Getting Started](Getting-Started).

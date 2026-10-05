# Dalue

**Dalue** maps nested arrays, objects and JSON documents into a new structure
described by a plain PHP array (the *schema*). It is built on
[uuur86/strobj](https://github.com/uuur86/strobj), which reads the source data
with slash-separated paths.

```php
use Dalue\Mapper;
use StrObj\StringObjects;

$data = StringObjects::instance('{"user":{"id":7,"name":"Jane"}}');

$result = Mapper::map($data, [
    'id'     => '@data/user/id',
    'name'   => '@data/user/name',
    'source' => 'api',
]);
// ['id' => 7, 'name' => 'Jane', 'source' => 'api']
```

## Pages

- [Installation](Installation)
- [Getting Started](Getting-Started)
- [Schema Reference](Schema-Reference)
- [Collections](Collections)
- [Callbacks](Callbacks)
- [Missing Values and Defaults](Missing-Values-and-Defaults)
- [Recipes](Recipes)
- [Upgrading to StrObj 3](Upgrading-to-StrObj-3)
- [FAQ](FAQ)

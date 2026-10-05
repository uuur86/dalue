# Callbacks

The optional third argument of `Mapper::map()` receives every result entry and
returns the value to store.

```php
$result = Mapper::map($data, $schema, function ($key, $value) {
    if ($key === 'email') {
        return strtolower($value);
    }

    return $value;
});
```

## How it is called

- Arguments: the result key (`string` or `int`) and the mapped value.
- The key of a collection is passed without the `[]` suffix.
- Nested schemas are processed first: the callback sees each child entry, then
  the parent key with the already transformed array.
- For a collection, the callback sees each column (a list) and then the merged
  rows.
- Any callable works: closures, function names, `[ClassName::class, 'method']`
  and invokable objects.

```php
// Schema: ['id' => '@data/id', 'lines[]' => ['sku' => '@data/items/*/sku']]
// Calls, in order:
// ('id', 10)
// ('sku', ['A', 'B'])
// ('lines', [['sku' => 'A'], ['sku' => 'B']])
```

## Tips

- Always return `$value` for keys you do not change.
- Key names can repeat at different levels. Check the value type as well as the
  key when that matters.
- A schema with integer keys passes integers to the callback. Declare the key
  parameter without a type, or as `int|string` on PHP 8, when you use such
  schemas.

# Collections

A key ending with `[]` turns a nested schema of *columns* into a list of *rows*.

```php
$data = StringObjects::instance([
    'items' => [
        ['sku' => 'ITEM-01', 'qty' => 2],
        ['sku' => 'ITEM-02', 'qty' => 1],
    ],
]);

Mapper::map($data, [
    'lines[]' => [
        'code'     => '@data/items/*/sku',
        'quantity' => '@data/items/*/qty',
    ],
]);
```

```php
[
    'lines' => [
        ['code' => 'ITEM-01', 'quantity' => 2],
        ['code' => 'ITEM-02', 'quantity' => 1],
    ],
]
```

Without the `[]` suffix the same schema returns the columns:

```php
['lines' => ['code' => ['ITEM-01', 'ITEM-02'], 'quantity' => [2, 1]]]
```

## Rules

- Every column should be a list, normally a wildcard path (`@data/items/*/field`).
- Items without a field produce `null` in that row, so rows always stay aligned
  with the source items.
- When columns have different lengths, every row of the longest column is kept
  and missing cells are `null`.
- Columns that are not non-empty arrays (static values, missing paths, empty
  lists) are left out of the rows.
- An empty source list produces an empty list.
- Collections can be nested inside other schemas.

## Merging columns directly

`Mapper::mergeColumns()` is public and can be used on its own:

```php
Mapper::mergeColumns(['sku' => ['A', 'B'], 'qty' => [1]]);
// [['sku' => 'A', 'qty' => 1], ['sku' => 'B', 'qty' => null]]
```

## Adding a constant to every row

Static values are not repeated in rows. Add them with a callback:

```php
Mapper::map($data, ['lines[]' => ['code' => '@data/items/*/sku']], function ($key, $value) {
    if ($key !== 'lines') {
        return $value;
    }

    return array_map(function (array $row) {
        return $row + ['currency' => 'USD'];
    }, $value);
});
```

# Schema Reference

## Keys

Every schema key becomes a key of the result, in the same order. Integer keys,
`0` and `''` are valid keys, so a list schema produces a list:

```php
Mapper::map($data, ['@data/a', '@data/b', 'static']);
// ['A', 'B', 'static']
```

A key ending with `[]` marks a [collection](Collections). The suffix is removed
from the result key. `[]` anywhere else in a key has no special meaning.

## Values

| Schema value | Result |
| --- | --- |
| `'@data/'` | The whole source document |
| `'@data/user/name'` | Value at `user/name` |
| `'@data/items/0/sku'` | Value of `sku` in the first item |
| `'@data/items/*/sku'` | List with one entry per item; `null` where the item has no `sku` |
| Nested array | Mapped recursively with the same callback and default |
| Any other value | Copied unchanged (strings, numbers, booleans, `null`, objects) |

Notes:

- The prefix is case-sensitive and must be at the start of the string:
  `'@DATA/a'` and `'x @data/a'` are copied as plain strings.
- Paths use `/` as separator. See the
  [StrObj path documentation](https://github.com/uuur86/strobj/wiki/Getting-Started)
  for every path feature.
- Stored values are returned unchanged, including `false`, `0`, `''` and `null`.
  Paths that do not exist return the default; see
  [Missing Values and Defaults](Missing-Values-and-Defaults).

## Constants

| Constant | Value |
| --- | --- |
| `Mapper::DATA_PREFIX` | `'@data/'` |
| `Mapper::COLLECTION_SUFFIX` | `'[]'` |

Use them to build schemas programmatically:

```php
$schema = ['name' => Mapper::DATA_PREFIX . 'user/name'];
```

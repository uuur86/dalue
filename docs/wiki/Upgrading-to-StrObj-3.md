# Upgrading to StrObj 3

The current version of Dalue requires `uuur86/strobj` 3.x and PHP 7.4 or later.

```sh
composer require uuur86/dalue uuur86/strobj:^3.0
```

## What changes for Dalue users

| Situation | Before (StrObj 2.x) | Now |
| --- | --- | --- |
| Stored `false` | Mapped as `''` | Mapped as `false` |
| Missing path | `null` | `null`, or the new `$default` argument |
| Collection item without a field | Rows shifted; values from different items were combined | `null` in that row; rows stay aligned |
| Columns of different length | Short columns left keys out of later rows | Missing cells are `null` |
| Schema key `0`, `"0"` or `""` | Mapping stopped at that key | Mapped like any other key |
| Key like `a[]b` | Last two characters removed | Kept as is; only a trailing `[]` marks a collection |
| Return type | Not declared | `array` |

## Checklist

1. Update the dependencies with Composer.
2. Search your code for checks such as `=== ''` on mapped values that could now
   be `false` or `null`.
3. If you relied on misaligned collection rows or on columns being left out, review
   those schemas and add tests with your real payloads.
4. If you extend `Mapper` and override `map()` or `mergeColumns()`, add the
   `array` return type and the `$default` parameter to your overrides.

## Keeping the StrObj 2.x read rules

StrObj 3 can reproduce its old read rules:

```php
use StrObj\Behavior;

$data = StringObjects::instance($payload, ['behavior' => Behavior::LEGACY]);
```

Dalue still fixes the schema-key and collection-suffix problems in this mode,
but `false` values and wildcard reads follow the 2.x rules. See
[Missing Values and Defaults](Missing-Values-and-Defaults).

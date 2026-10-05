# Missing Values and Defaults

## Stored values are never replaced

`false`, `0`, `''`, `[]` and `null` that exist in the source data are returned
as they are.

```php
$data = StringObjects::instance(['active' => false, 'note' => null]);

Mapper::map($data, ['active' => '@data/active', 'note' => '@data/note']);
// ['active' => false, 'note' => null]
```

## Missing paths return the default

A path that does not exist returns `null`:

```php
Mapper::map($data, ['phone' => '@data/contact/phone']);
// ['phone' => null]
```

Pass a different default as the fourth argument. It is used for every missing
path in the schema, including nested schemas:

```php
Mapper::map($data, ['phone' => '@data/contact/phone'], null, 'N/A');
// ['phone' => 'N/A']
```

Wildcard paths always return a list, so the default is not used for them.
Items without the field appear as `null` inside the list.

## StrObj filters

When the `StringObjects` instance has filters, a value rejected by a filter
callback also returns the default.

## StrObj legacy behavior

Dalue works with instances created with `['behavior' => Behavior::LEGACY]`, but
that behavior keeps the StrObj 2.x read rules: stored `false` values are
replaced by the default and wildcard reads skip items without the field, which
can misalign [collections](Collections). Use the default (consistent) behavior
for new code. See the
[StrObj compatibility guide](https://github.com/uuur86/strobj/blob/master/docs/compatibility.md).

# Examples

The repository contains runnable example scripts in
[`examples/`](https://github.com/uuur86/dalue/tree/main/examples). Every script
prints its result as JSON, and its expected output is stored in
[`examples/output/`](https://github.com/uuur86/dalue/tree/main/examples/output).

## Run them

```sh
git clone https://github.com/uuur86/dalue.git
cd dalue
composer install

php examples/01-basic-mapping.php   # one example
composer examples                   # all examples
```

The last line of `composer examples` should be:

```text
7 example(s) run, 0 failed.
```

## Verify the results

```sh
# Compare one example with its expected output
php examples/03-collections.php | diff - examples/output/03-collections.txt && echo OK

# Run every example and compare all outputs automatically
vendor/bin/phpunit --filter ExamplesTest

# Run the whole test suite, static analysis and coding standard
composer check
```

GitHub Actions runs these checks on PHP 7.4 – 8.5 for every push, so the
outputs shown here always match the current code.

## Scripts

| Script | Shows |
| --- | --- |
| `01-basic-mapping.php` | `@data/` paths, static values and nested schemas |
| `02-json-input.php` | JSON input, list indexes and invalid JSON handling |
| `03-collections.php` | `[]` collections, aligned rows and `mergeColumns()` |
| `04-callbacks.php` | Casting and formatting values in a callback, callback order |
| `05-missing-values.php` | Stored `false`/`0`/`''`/`null` and the default for missing paths |
| `06-webhook-to-dynamodb.php` | Webhook payload to a DynamoDB AttributeValue item |
| `07-api-to-database.php` | Paginated API response to rows for an `INSERT` statement |

## Example: collections

[`examples/03-collections.php`](https://github.com/uuur86/dalue/blob/main/examples/03-collections.php)

```php
$data = StringObjects::instance([
    'order' => 'SO-1001',
    'items' => [
        ['sku' => 'ITEM-01', 'qty' => 2, 'price' => 50.0],
        ['qty' => 1, 'price' => 9.99],
        ['sku' => 'ITEM-03', 'qty' => 5, 'price' => 1.5],
    ],
]);

Mapper::map($data, [
    'order' => '@data/order',
    'lines[]' => [
        'sku' => '@data/items/*/sku',
        'quantity' => '@data/items/*/qty',
    ],
]);
```

Output:

```json
{
    "order": "SO-1001",
    "lines": [
        {"sku": "ITEM-01", "quantity": 2},
        {"sku": null, "quantity": 1},
        {"sku": "ITEM-03", "quantity": 5}
    ]
}
```

The second item has no `sku`, so its row contains `null` and the rows stay
aligned with the source items.

## Example: missing values

[`examples/05-missing-values.php`](https://github.com/uuur86/dalue/blob/main/examples/05-missing-values.php)

```php
$data = StringObjects::instance([
    'active' => false,
    'balance' => 0,
    'nickname' => '',
    'manager' => null,
]);

$schema = [
    'active' => '@data/active',
    'balance' => '@data/balance',
    'nickname' => '@data/nickname',
    'manager' => '@data/manager',
    'phone' => '@data/contact/phone',
];

Mapper::map($data, $schema, null, 'N/A');
```

Output:

```json
{
    "active": false,
    "balance": 0,
    "nickname": "",
    "manager": null,
    "phone": "N/A"
}
```

Only `phone` is missing in the source, so only `phone` receives the default.

## Troubleshooting

| Message | Fix |
| --- | --- |
| `Dependencies are missing. Run "composer install"…` | Run `composer install` in the project root. |
| `Could not open input file` | Run the command from the project root, not from `examples/`. |
| `ExamplesTest` fails after you changed an example | Regenerate the output: `php examples/NN-name.php > examples/output/NN-name.txt`, then review the diff. |

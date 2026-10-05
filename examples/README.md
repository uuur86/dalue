# Dalue examples

Runnable scripts that show every Dalue feature. Each script prints its result
as pretty JSON, and the expected output is stored in [`output/`](output).

## Running the examples

The examples are not part of the Composer package, so run them from a clone of
the repository. PHP 7.4 or later and Composer are required.

```sh
git clone https://github.com/uuur86/dalue.git
cd dalue
composer install

# Run one example
php examples/01-basic-mapping.php

# Run all examples
composer examples
```

`composer examples` prints every result and ends with a summary such as
`7 example(s) run, 0 failed.` It exits with a non-zero code if an example fails.

## Checking that the output is correct

Compare an example with its expected output:

```sh
php examples/03-collections.php | diff - examples/output/03-collections.txt && echo OK
```

The test suite runs every example and compares the output automatically:

```sh
composer test
# or only the examples:
vendor/bin/phpunit --filter ExamplesTest
```

GitHub Actions runs the same checks on PHP 7.4 – 8.5 for every push.

## Examples

| Script | Shows |
| --- | --- |
| [`01-basic-mapping.php`](01-basic-mapping.php) | `@data/` paths, static values and nested schemas |
| [`02-json-input.php`](02-json-input.php) | JSON input, list indexes and invalid JSON handling |
| [`03-collections.php`](03-collections.php) | `[]` collections, aligned rows and `mergeColumns()` |
| [`04-callbacks.php`](04-callbacks.php) | Casting and formatting values in a callback, callback order |
| [`05-missing-values.php`](05-missing-values.php) | Stored `false`/`0`/`''`/`null` and the default for missing paths |
| [`06-webhook-to-dynamodb.php`](06-webhook-to-dynamodb.php) | Webhook payload to a DynamoDB AttributeValue item |
| [`07-api-to-database.php`](07-api-to-database.php) | Paginated API response to rows for an `INSERT` statement |

## Adding an example

1. Create `examples/NN-short-name.php` (two-digit number first). Start it with
   `require __DIR__ . '/bootstrap.php';` and print results with `show($title, $value)`.
2. Generate its expected output:
   `php examples/NN-short-name.php > examples/output/NN-short-name.txt`
3. Check the output by hand, then run `composer check`.
4. Add the script to the table above and to the wiki page `docs/wiki/Examples.md`.

After an intentional behavior change, regenerate the affected output files with
step 2 and review the diff before committing.

# Dalue - PHP Data Mapper

The `Dalue` library provides a powerful, recursive, and memory-efficient method for data mapping and structural transformation in PHP. It allows you to ingest complex, unstructured data sets (like API responses or raw JSON payloads) and map them directly into strict domain models, NoSQL item structures, or specific array formats while optionally mutating the values during the allocation phase.

It eliminates the need for repetitive array traversal, nested loops, and deep conditional type casting in your application layer.

## Requirements

- PHP 7.4 or higher (PHP 8.x recommended).
- [uuur86/strobj](https://github.com/uuur86/strobj) (Automatically installed via Composer).

## Installation

You can install the package via Composer:

```sh
composer require uuur86/dalue
```

## How It Works

Dalue relies on a defined schema array where you specify the target structure. It supports:

1. **Dot/Path Notation:** Using the `@data/` prefix, you can fetch deeply nested values instantly. Use `/*` wildcard notation to extract columns across array items (e.g., `@data/items/*/sku`).
2. **Recursive Array Mapping:** You can define nested arrays in your target schema.
3. **Collection Merging (`[]` notation):** Automatically resolves and merges `has_many` relationships into structured row arrays.
4. **On-the-fly Mutation:** Allows passing a callback function to manipulate keys and values (e.g., formatting dates, prefixing keys, casting types) during the mapping loop.

---

## Basic Usage: Transforming a Dataset

Below is a basic example of extracting specific data from a deep array. Use `StringObjects::instance()` to ingest array or JSON payloads.

```php
use Dalue\Mapper;
use StrObj\StringObjects;

// 1. Ingest your source data
$data = StringObjects::instance([
    'user' => [
        'id'      => 1,
        'name'    => 'John Doe',
        'email'   => 'john.doe@example.com',
        'details' => [
            'age'  => 30,
            'city' => 'New York'
        ]
    ]
]);

// 2. Define the structural schema you want to achieve
// Note the '@data/' prefix to read from the source StringObject
$schema = [
    'userID'      => '@data/user/id',
    'userName'    => '@data/user/name',
    'userDetails' => [
        'userAge'  => '@data/user/details/age',
        'userCity' => '@data/user/details/city'
    ],
    'mapped_at'   => time() // Static values can be assigned directly
];

// 3. Execute the transformation
$mappedData = Mapper::map($data, $schema);

print_r($mappedData);
```

**Output:**

```php
Array
(
    [userID] => 1
    [userName] => John Doe
    [userDetails] => Array
        (
            [userAge] => 30
            [userCity] => New York
        )
    [mapped_at] => 1723126306
)
```

---

## Advanced Usage: Type Casting & Callbacks

You can perform complex transformations by providing a callback function as the third parameter. This is especially useful for formatting strings, rounding floats, or mutating specific fields during mapping.

```php
use Dalue\Mapper;
use StrObj\StringObjects;

// ... (using the same $data from above)

$schema = [
    'userID'    => '@data/user/id',
    'userName'  => '@data/user/name',
    'userEmail' => '@data/user/email',
];

$mappedData = Mapper::map(
    $data, 
    $schema, 
    function (string $key, $value) {
        if ($key === 'userEmail') {
            return strtoupper($value);
        }
        
        return $value;
    }
);
```

---

## Real-World Example: Adapting API JSON for AWS DynamoDB

NoSQL databases like AWS DynamoDB require strict type markers (e.g., `S` for String, `N` for Number, `L` for List, `M` for Map). `Dalue` cleanly maps complex webhook structures into clean domain models, which can then be formatted into DynamoDB AttributeValue format using AWS SDK's `Marshaler`.

### Scenario:

We receive an e-commerce webhook payload and need to format it for a DynamoDB `PutItem` operation.

```php
use Dalue\Mapper;
use StrObj\StringObjects;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;

$apiPayload = [
  "transaction_id" => "tx_987654321",
  "customer" => [
    "account_no" => "CUST-1002",
    "full_name" => "John Doe",
  ],
  "purchase_details" => [
    "total_amount" => 150.50,
    "items" => [
      ["sku" => "ITEM-01", "qty" => 2, "price" => 50.00],
      ["sku" => "ITEM-02", "qty" => 1, "price" => 50.50]
    ]
  ]
];

$stringObject = StringObjects::instance($apiPayload);

// The '[]' notation triggers Dalue's mergeColumns logic for collections.
// The '/*' wildcard extracts array column values across all item rows.
$schema = [
    'PK'           => '@data/transaction_id',
    'SK'           => 'METADATA',
    'CustomerId'   => '@data/customer/account_no',
    'TotalAmount'  => '@data/purchase_details/total_amount',
    'OrderItems[]' => [
        'SKU'      => '@data/purchase_details/items/*/sku',
        'Quantity' => '@data/purchase_details/items/*/qty',
        'Price'    => '@data/purchase_details/items/*/price'
    ]
];

// Step 1: Perform structural mapping and value mutations
$mappedData = Mapper::map(
    $stringObject, 
    $schema, 
    function (string $key, $value) {
        if ($key === 'PK') {
            return 'ORDER#' . $value;
        }
        return $value;
    }
);

// Step 2: Format the clean mapped array into DynamoDB AttributeValue format
$marshaler = new Marshaler();
$dynamoItem = $marshaler->marshalItem($mappedData);

// Result is ready to be sent to AWS DynamoDB PutItem
/*
$dynamoDbClient = new DynamoDbClient([
    'region'  => 'us-east-1',
    'version' => 'latest'
]);

$dynamoDbClient->putItem([
    'TableName' => 'OrdersTable',
    'Item'      => $dynamoItem
]);
*/
```

---

## LICENSE

This project is licensed under the GPL-3.0-or-later License. See the [LICENSE](LICENSE) file for more details.

## AUTHOR

Uğur Biçer - [@uuur86](https://github.com/uuur86)

## CONTRIBUTING

If you want to contribute to this project, you can send pull requests. We expect all contributors to follow our [Code of Conduct](CONTRIBUTING.md).

## CONTACT

You can contact me via email: [contact@codeplus.dev](mailto:contact@codeplus.dev)

## BUGS

You can report bugs via GitHub issues.

## SECURITY

If you find a security issue, please report it via email: [contact@codeplus.dev](mailto:contact@codeplus.dev)

## DONATE

If you want to support me, you can donate via GitHub sponsors: [https://github.com/sponsors/uuur86](https://github.com/sponsors/uuur86)

## SEE ALSO

* [uuur86/strobj](https://github.com/uuur86/strobj) - PHP String Objects
* [uuur86/wasp](https://github.com/uuur86/wasp) - WordPress Advanced Settings Page
* [@codeplusdev](https://github.com/fyndsoft) - Codeplus Development

# Dalue Mapper Class Usage

The `Mapper` class under the `Dalue` namespace offers a powerful and flexible method for data transformation and restructuring. This class allows you to create a new structure from a given data set, optionally performing transformations on the data in the process.

## Getting Started

Before you begin using the `Mapper` class, ensure that the library containing the `StrObj\StringObjects` class is installed. The `Mapper` class utilizes the functionalities provided by this class for data access and manipulation.

### Installation

First, install the required packages via Composer:

```sh
composer require uuur86/dalue:0.1.1-beta
```

## Example Usage

Below is an example of how to use the `Mapper` class:

```php
use Dalue\Mapper;
use StrObj\StringObjects;

// Sample data set
$data = new StringObjects([
    'user' => [
        'id' => 1,
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
        'details' => [
            'age' => 30,
            'city' => 'New York'
        ]
    ]
]);

// Defining the data structure we want to transform into.
$paths = [
    'userID' => '@data/user/id',
    'userName' => '@data/user/name',
    'userDetails' => [
        'userAge' => '@data/user/details/age',
        'userCity' => '@data/user/details/city'
    ]
];

// Using the Mapper::map function to perform the transformation.
$mappedData = Mapper::map($data, $paths);

print_r($mappedData);
```

### Explanation

1. **Data Initialization**:
    - A sample data set is created using the `StringObjects` class.
2. **Path Definition**:
    - The structure we want to transform into is defined in the `$paths` array.
3. **Data Transformation**:
    - The `Mapper::map` function is used to transform the data according to the defined paths.

### Output

The above example will produce the following output:

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
)
```

## Advanced Usage

You can also perform more complex transformations by incorporating custom functions in the mapping paths. Here is an example:

```php
use Dalue\Mapper;
use StrObj\StringObjects;

$data = new StringObjects([
    'user' => [
        'id' => 1,
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
        'details' => [
            'age' => 30,
            'city' => 'New York'
        ]
    ]
]);

$paths = [
    'userID' => '@data/user/id',
    'userName' => '@data/user/name',
    'userEmail' => function($data) {
        return strtoupper($data->get('user.email'));
    },
    'userDetails' => [
        'userAge' => '@data/user/details/age',
        'userCity' => '@data/user/details/city',
        'description' => function($data) {
            return $data->get('user.details.age') . ' years old, lives in ' . $data->get('user.details.city');
        }
    ]
];

$mappedData = Mapper::map($data, $paths);

print_r($mappedData);
```

### Output

The advanced example will produce the following output:

```php
Array
(
    [userID] => 1
    [userName] => John Doe
    [userEmail] => JOHN.DOE@EXAMPLE.COM
    [userDetails] => Array
        (
            [userAge] => 30
            [userCity] => New York
            [description] => 30 years old, lives in New York
        )
)
```

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

- [uuur86/strobj](https://github.com/uuur86/strobj) - PHP String Objects
- [uuur86/wpoauth](https://github.com/uuur86/wpoauth) - WordPress OAuth2 Client
- [@codeplusdev](https://github.com/codeplusdev) - Codeplus Development

